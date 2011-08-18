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

/**
 * This class is a state machine representing the execution of a flow
 * 
 */
class __FlowExecution {
    
    protected $_id = null;
    protected $_vars = array();
    protected $_flow_definition = null;
    protected $_current_state = null;
    protected $_response = null;
    protected $_visited_states = array();
    
    public function __construct(__FlowDefinition $flow_definition, $id = null) {
        $this->setFlowDefinition($flow_definition);
        if($id == null) {
            $id = md5(uniqid(rand(), true));
        }
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function getKey() {
        return $this->getId();
    }
    
    public function setFlowDefinition(__FlowDefinition $flow_definition) {
        $this->_flow_definition = $flow_definition;
    }
    
    public function getFlowDefinition() {
        return $this->_flow_definition;
    }
    
    public function isStateVisited($state_id) {
        $return_value = false;
        if(key_exists($state_id, $this->_visited_states)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setAttribute($name, $value) {
        $this->setVar($name, $value);
    }
    
    public function getAttribute($name) {
        return $this->getVar($name);
    }
    
    public function setVar($name, $value) {
        $this->_vars[$name] = $value;
    }
    
    public function addVar($name, $value) {
        $this->setVar($name, $value);
    }
    
    public function getVar($name) {
        $return_value = null;
        if(key_exists($name, $this->_vars)) {
            $return_value = $this->_vars[$name];
        }
        return $return_value;
    }
    
    public function getVars() {
        return $this->_vars;
    }
    
    public function setCurrentState(__IFlowState $state) {
        while($state instanceof __DecisionFlowState) {
            $state_id = $this->_evaluateDecisionState($state);
            if($state_id != null) {
                $state = $this->_flow_definition->getState($state_id);         
            }
            else {
                $state = null;
            }
        }
        if($state != null) {
            $this->_setupScopeAttributes($state->getAttributes());
            $this->_visited_states[$state->getId()] = true;
        }
        $this->_current_state = $state;
        return $state;
    }
    
    public function getCurrentState() {
        return $this->_current_state;
    }
    
    public function signalEvent($event) {
        $this->setCurrentEvent($event);
    }
    
    public function &moveToStartState() {
        $return_value = null;
        $start_state = $this->_flow_definition->getStartState();
        if($start_state != null) {
            $return_value = $this->goToState($start_state);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Missing start state in flow ' . $this->_flow_definition->getId());
        }        
        return $return_value;
    }
    
    public function moveToNextState($event_id) {
        $return_value = null;
        //if no current event, move to start state:
        if($this->_current_state == null) {
            $return_value = $this->moveToStartState();
        }
        else {
            if($this->_current_state->hasTransition($event_id)) {
                $transition = $this->_current_state->getTransition($event_id);
                //setup scope attributes because of the transition:
                $this->_setupScopeAttributes($transition->getAttributes());
                //go to the given step:
                $next_state_id = $transition->getNextState();
                if($this->_flow_definition->hasState($next_state_id)) {
                    $return_value = $this->goToState($this->_flow_definition->getState($next_state_id));
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('State not found for id ' . $next_state_id);
                }
            }
        }
        return $return_value;
    }
    
    public function getCurrentActionIdentity() {
        $return_value = null;
        $state = $this->getCurrentState();
        if($state != null) {
            $return_value = $state->getActionIdentity();
        }
        return $return_value;
    }
    
    public function goToState($state) {
        if(is_string($state)) {
            $state = $this->_flow_definition->getState($state);
        }
        $return_value = null;
        $this->_visited_states[$state->getId()] = true;
        if($state instanceof __ActionFlowState) {
            $return_value = $this->setCurrentState($state);
        }
        else if($state instanceof __DecisionFlowState) {
            $return_value = $this->setCurrentState($state);
        }
        else if($state instanceof __SubFlowState) {
            $return_value = $this->_goToSubFlowState($state);
        }
        return $return_value;
    }
        
    protected function _evaluateDecisionState(__DecisionFlowState $state) {
        $conditional_transitions = $state->getTransitions();
        $return_value = null;
        foreach($conditional_transitions as $conditional_transition) {
            if($return_value == null) {
                if($conditional_transition->evaluateCondition()) {
                    $return_value = $conditional_transition->getNextStateIfTrue();
                }
                else {
                    $return_value = $conditional_transition->getNextStateIfFalse();
                }
            }
        }
        return $return_value;
    }
    
    protected function _goToSubFlowState(__SubFlowState $state) {
        //todo...
        return null;
    }
    
    protected function _setupScopeAttributes(array $attributes) {
        foreach($attributes as $attribute) {
            $target_attribute = $attribute->getAttribute();
            $value = $attribute->getValue();
            if($value instanceof __FlowAttribute) {
                $value = $this->_resolveScopeAttributeValue($value->getAttribute(), $value->getScope());
            }
            $this->_setScopeAttributeValue($target_attribute->getAttribute(), $target_attribute->getScope(), $value);
        }
    }
    
    protected function _setScopeAttributeValue($attribute, $scope, $value) {
        switch($scope) {
            case __FlowDefinition::SCOPE_FLOW:
                $this->addVar($attribute, $value);
                break;
            case __FlowDefinition::SCOPE_REQUEST:
                $request = __FrontController::getInstance()->getRequest();
                $request->addParameter($attribute, $value);
                break;
            case __FlowDefinition::SCOPE_SESSION:
                $session = __ApplicationContext::getInstance()->getSession();
                $session->setData($attribute, $value);
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Unknown flow scope ' . $scope);
                break;
        }
    }
    
    protected function _resolveScopeAttributeValue($attribute, $scope) {
        $return_value = null;
        switch($scope) {
            case __FlowDefinition::SCOPE_FLOW:
                $return_value = $this->getVar($attribute);
                break;
            case __FlowDefinition::SCOPE_REQUEST:
                $request = __FrontController::getInstance()->getRequest();
                if($request->hasParameter($attribute)) {
                    $return_value = $request->getParameter($attribute);
                }
                break;
            case __FlowDefinition::SCOPE_SESSION:
                $session = __ApplicationContext::getInstance()->getSession();
                if($session->hasData($attribute)) {
                    $return_value = $session->getData($attribute);
                }
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Unknown flow scope ' . $scope);
                break;
        }
        return $return_value;
    }
    
    public function setResponse(__IResponse $response) {
        $this->_response = $response;
    }
    
    public function getResponse() {
        return $this->_response;
    }
    
    
}
    