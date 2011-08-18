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
 * @package    WebFlow
 * 
 */

class __FlowExecutor {

    static protected $_instance = null;
    
    protected $_flow_executions = array();
    protected $_active_flow_execution = null;  
    protected $_responses = array();
    protected $_event_id = null;  

    /**
     * 
     * @return __FlowExecutor
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __FlowExecutor();
        }
        return self::$_instance;
    }
    
    protected function __construct() {
        $session = __ApplicationContext::getInstance()->getSession();
        if($session->hasData('__FlowExecutor::_flow_executions')) {
            //for any reason, usage of "&" is the only way to get a reference of the flow_executions array:
            $this->_flow_executions  =& $session->getData('__FlowExecutor::_flow_executions');
            $this->_responses =& $session->getData('__FlowExecutor::_responses');
        }
        else {
            $session->setData('__FlowExecutor::_flow_executions', $this->_flow_executions);
            $session->setData('__FlowExecutor::_responses', $this->_responses);
        }
    }
    
    public function &launch($flow_id) {
        $flow_execution = __FlowExecutionFactory::createFlowExecution($flow_id);
        $this->addFlowExecution($flow_execution);
        $start_state = $flow_execution->moveToStartState();
        $this->_active_flow_execution =& $flow_execution;
        return $start_state;
    }
    
    public function &resume($flow_execution_key, $event_id) {
        $flow_execution = $this->getFlowExecution($flow_execution_key);
        $this->_active_flow_execution =& $flow_execution;
        $state = $flow_execution->moveToNextState(!empty($this->_event_id) ? $this->_event_id : $event_id);
        if($state != null) {
            //if the current state is an end-state, let's remove the flow execution from the context:
            if($state instanceof __EndFlowState) {
                //$this->removeFlowExecution($flow_execution_key);
            }
        }
        return $state;
    }
    
    public function &handleException($flow_execution_key, $exception) {
//todo...
    }
    
    public function &goToState($state) {
        return $this->_active_flow_execution->goToState($state);
    }
    
    public function signalEvent($event_id) {
        $this->_event_id = $event_id;
    }
    
    public function getResponse($flow_execution_key) {
        $return_value = null;
        if(key_exists($flow_execution_key, $this->_responses)) {
            $return_value = clone($this->_responses[$flow_execution_key]);
        }
        return $return_value;
    }
    
    public function setResponse(__IResponse $response) {
        $active_flow_execution = $this->getActiveFlowExecution();
        if($active_flow_execution != null) {
            $flow_execution_key = $active_flow_execution->getKey();
            $response_to_save = clone($response);
            //save the response
            $response_to_save->prepareToSleep();
            $this->_responses[$flow_execution_key] = $response_to_save;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('There is not any active flow to set response to');
        }
    }
    
    public function hasActiveFlowExecution() {
        $return_value = false;
        if($this->getActiveFlowExecution() != null) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function &getFlowScope() {
        return $this->getActiveFlowExecution();
    }
    
    public function &getRequestScope() {
        $return_value = __FrontController::getInstance()->getRequest();
        return $return_value;
    }
    
    public function &getSessionScope() {
        return __ApplicationContext::getInstance()->getSession();
    }
    
    public function &getActiveFlowExecution() {
        if($this->_active_flow_execution == null) {
            $request_flow_execution_key = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
            $request = __FrontController::getInstance()->getRequest();
            if($request->hasParameter($request_flow_execution_key)) {
                $flow_execution_key = $request->getParameter($request_flow_execution_key);
                if($this->hasFlowExecution($flow_execution_key)) {
                    $this->_active_flow_execution = $this->getFlowExecution($flow_execution_key);
                }
            }
        }
        return $this->_active_flow_execution;
    }
    
    public function addFlowExecution(__FlowExecution &$flow_execution) {
        $this->_flow_executions[$flow_execution->getId()] =& $flow_execution;
    }
    
    public function &getFlowExecution($flow_execution_key) {
        $return_value = null;
        if(key_exists($flow_execution_key, $this->_flow_executions)) {
            $return_value =& $this->_flow_executions[$flow_execution_key];
        }
        return $return_value;
    }
    
    public function hasFlowExecution($flow_execution_key) {
        $return_value = false;
        if(key_exists($flow_execution_key, $this->_flow_executions)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function removeFlowExecution($flow_execution_key) {
        if(key_exists($flow_execution_key, $this->_flow_executions)) {
            unset($this->_flow_executions[$flow_execution_key]);
        }
    }
    
    public function isExceptionHandled(Exception &$e) {
        return false;
    }
    
    
}
