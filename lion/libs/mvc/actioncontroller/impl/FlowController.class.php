<?php
/**
 * This file is part of lion framework.
 * 
 * Copyright (c) 2011 Antonio Parraga Navarro
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 *
 * @copyright  Copyright (c) 2011 Antonio Parraga Navarro
 * @author     Antonio Parraga Navarro
 * @link       http://www.lionframework.org
 * @license    http://www.lionframework.org/license.html
 * @version    1.4
 * @package    ActionController
 * 
 */

class __FlowController extends __ActionController {

    public function startFlowAction() {
        $return_value = null;
        $request = __FrontController::getInstance()->getRequest();
        $flow_id = $request->getFlowId();
        if($flow_id != null) {
            $state = __FlowExecutor::getInstance()->launch($flow_id);
            $action_identity = $state->getActionIdentity();
            $return_value = __ActionDispatcher::getInstance()->dispatch($action_identity);
        }
        return $return_value;
    }
    
    public function defaultAction() {
        $return_value = null;
        $request = __FrontController::getInstance()->getRequest();
        $application_context = __ApplicationContext::getInstance();
        $request_flow_execution_key = $application_context->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
        if($request->hasParameter($request_flow_execution_key)) {
            $flow_execution_key = $request->getParameter($request_flow_execution_key);
            $request_flow_event_id = $application_context->getPropertyContent('REQUEST_FLOW_EVENT_ID');
            $request_flow_state_id = $application_context->getPropertyContent('REQUEST_FLOW_STATE_ID');
            $flow_executor = __FlowExecutor::getInstance();
            
            //sync with the current state sent from client:
            if($request->hasParameter($request_flow_state_id)) {
                $flow_execution = $flow_executor->getActiveFlowExecution();
                if($flow_execution != null) {
                    $flow_state_id = $request->getParameter($request_flow_state_id);
                    if($flow_execution->isStateVisited($flow_state_id)) {
                        $current_state = $flow_execution->getCurrentState();
                        if($current_state != null && $current_state->getId() != $flow_state_id) {
                            $state = $flow_execution->goToState($flow_state_id);
                            if($state != null && !$request->hasParameter($request_flow_event_id)) {
                                $this->_executeControllerAssociatedToState($flow_executor, $state);
                            }
                        }
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Flow state not yet visited: ' . $flow_state_id);
                    }
                }
            }
            
            //checks flow event:
            if($request->hasParameter($request_flow_event_id)) {
                $flow_event_id = $request->getParameter($request_flow_event_id);
                $state = $flow_executor->resume($flow_execution_key, $flow_event_id);
                if($state != null) {
                    $this->_executeControllerAssociatedToState($flow_executor, $state);
                    //redirect via 303 because of the redirect after submit pattern (alwaysRedirectOnPause)
                    $uri = $request->getUri();
                    //add the flow execution key parameter:
                    $flow_execution_key = $request->getParameter($request_flow_execution_key);
                    $uri->addParameter($request_flow_execution_key, $flow_execution_key);
                    $uri->addParameter($request_flow_state_id, $state->getId());
                    $empty_request = __RequestFactory::getInstance()->createRequest();
                    __FrontController::getInstance()->redirect($uri, $empty_request, 303);
                }
            }
            else {
                $return_value = $flow_executor->getResponse($flow_execution_key);
                $return_value->setBufferControl(true);
            }
        }
        return $return_value;
    }
    
    protected function _executeControllerAssociatedToState(__FlowExecutor &$flow_executor, __IFlowState &$state) {
        $application_context = __ApplicationContext::getInstance();
        $request = __FrontController::getInstance()->getRequest();
        $action_identity = $state->getActionIdentity();
        
        $request_flow_execution_key = $application_context->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
        $flow_execution_key = $request->getParameter($request_flow_execution_key);
        
        try {
            $response = __ActionDispatcher::getInstance()->dispatch($action_identity);
            if($response instanceof __IResponse) {
                $flow_executor->setResponse($response);
                $response->clear();
            }
            else if($response instanceof __FlowEvent) {
                $state = $flow_executor->resume($flow_execution_key, $response->getEventName());
                if($state != null) {
                    $this->_executeControllerAssociatedToState($flow_executor, $state);
                }
            }
        }
        catch(Exception $e) {
            if($flow_executor->isExceptionHandled($e)) {
                //$state = $flow_executor->handleException($flow_execution_key, $e);
                //if($state != null) {
                //    $this->_executeControllerAssociatedToState($flow_executor, $state);
                //}
            }
            else {
                throw $e;
            }
        }
        
    }
    
}
