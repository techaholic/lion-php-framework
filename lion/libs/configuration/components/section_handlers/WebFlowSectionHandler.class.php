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
 * @package    Configuration
 * 
 */

/**
 * This is the section handler in charge of processing &lt;webflow&gt; configuration sections
 *
 */
class __WebFlowSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array();
        $subsections = $section->getSections();
        foreach($subsections as &$subsection) {
            switch(strtoupper($subsection->getName())) {
                case 'FLOW':
                    $flow = $this->_createFlow($subsection);
                    $return_value[$flow->getId()] = $flow;
                    unset($flow);
                    break;
                default:
                    throw new __ConfigurationException('Unexpected tag ' . $subsection->getName() . ' at current position');
                    break;
            }
        }
        return $return_value;
    }

    protected function &_createFlow(__ConfigurationSection &$section) {
        $return_value = new __FlowDefinition();
        if($section->hasAttribute('id')) {
            $return_value->setId($section->getAttribute('id'));
        }
        $subsections = $section->getSections();
        foreach($subsections as $flow_section) {
            switch (strtoupper($flow_section->getName())) {
                case 'ACTION-STATE':
                    $state_type = __FlowStateFactory::ACTION_STATE;
                    break;
                case 'START-STATE':
                    $state_type = __FlowStateFactory::START_STATE;
                    break;
                case 'END-STATE':
                    $state_type = __FlowStateFactory::END_STATE;
                    break;
                case 'DECISION-STATE':
                    $state_type = __FlowStateFactory::DECISION_STATE;
                    break;
                case 'SUBFLOW-STATE':
                    $state_type = __FlowStateFactory::SUBFLOW_STATE;
                    break;
                default:
                    throw new __ConfigurationException('Unexpected tag ' . $flow_section->getName() . ' as child of ' . $section->getName());
                    break;
            }
            //create the new state
            $state = __FlowStateFactory::createState($state_type);
            if($flow_section->hasAttribute('id')) {
                $state->setId($flow_section->getAttribute('id'));
            }
            if($state instanceof __ActionFlowState) {
                $action_identity = $this->_parseActionNode($flow_section);
                if($action_identity != null) {
                    $state->setActionIdentity($action_identity);
                }
            }
            //setup the new state
            $this->_setupFlow($state, $flow_section);
            //add the new state
            $return_value->addState($state);
            unset($state);
        }
        if($section->hasAttribute('start-state')) {
            $return_value->setStartState($section->getAttribute('start-state'));
        }
        return $return_value;
    }
    
    protected function _setupFlow(__IFlowState &$flow_state,  __ConfigurationSection &$section) {
        $subsections = $section->getSections();
        foreach($subsections as $flow_section) {
            switch (strtoupper($flow_section->getName())) {
                case 'TRANSITION':
                    $transition = $this->_parseTransitionNode($flow_section);
                    $flow_state->addTransition($transition);
                    break;
                case 'SET':
                    $attribute =$this->_parseAttribute($flow_section);
                    $flow_state->addAttribute($attribute);
                    break;
                case 'IF':
                    $transition = $this->_parseIfNode($flow_section);
                    $flow_state->addTransition($transition);
                    break;
                case 'IF-PERMISSION':
                    $transition = $this->_parseIfPermissionNode($flow_section);
                    $flow_state->addTransition($transition);
                    break;
                default:
                    throw new __ConfigurationException('Unexpected tag ' . $flow_section->getName() . ' as child of ' . $section->getName());
                    break;
            }
        }
        return $flow_state;        
    }
    
    protected function _parseActionNode(__ConfigurationSection &$flow_section) {
        $return_value = null;
        if($flow_section->hasAttribute('controller')) {
            $return_value = new __ActionIdentity();
            $return_value->setControllerCode($flow_section->getAttribute('controller'));
            if($flow_section->hasAttribute('action')) {
                $return_value->setActionCode($flow_section->getAttribute('action'));
            }
        }
        return $return_value;
    }

    protected function _parseIfPermissionNode(__ConfigurationSection &$flow_section) {
        $transition = new __IfPermissionFlowTransition();
        if($flow_section->hasAttribute('permission')) {
            $transition->setPermission($flow_section->getAttribute('permission'));
        }
        else {
            throw new __ConfigurationException('Missing permission in webflow if-permission definition');
        }
        if($flow_section->hasAttribute('then')) {
            $transition->setNextStateIfTrue($flow_section->getAttribute('then'));
        }
        if($flow_section->hasAttribute('else')) {
            $transition->setNextStateIfFalse($flow_section->getAttribute('else'));
        }
        return $transition;
    }

    protected function _parseIfNode(__ConfigurationSection &$flow_section) {
        $transition = new __IfFlowTransition();
        if($flow_section->hasAttribute('test')) {
            $transition->setCondition($this->_parseExpression($flow_section->getAttribute('test')));
        }
        else {
            throw new __ConfigurationException('Missing condition to check in webflow decision-state definition');
        }
        if($flow_section->hasAttribute('them')) {
            $transition->setNextStateIfTrue($flow_section->getAttribute('them'));
        }
        if($flow_section->hasAttribute('else')) {
            $transition->setNextStateIfFalse($flow_section->getAttribute('else'));
        }
        return $transition;
    }
    
    protected function _parseTransitionNode(__ConfigurationSection &$flow_section) {
        if($flow_section->hasAttribute('on')) {
            $transition = __FlowTransitionFactory::createTransition(__FlowTransition::EVENT_TRANSITION);
            $transition->setEvent($flow_section->getAttribute('on'));
        }
        else if($flow_section->hasAttribute('on-exception')) {
            $transition = __FlowTransitionFactory::createTransition(__FlowTransition::EXCEPTION_TRANSITION);
            $transition->setException($flow_section->getAttribute('on-exception'));
        }
        else {
            throw new __ConfigurationException('Missing argument in webflow transition definition');
        }
        if($flow_section->hasAttribute('to')) {
            $transition->setNextState($flow_section->getAttribute('to'));
        }
        else {
            throw new __ConfigurationException('Missing state to go in webflow transition definition');
        }
        $subsections = $flow_section->getSections();
        foreach($subsections as $flow_subsection) {
            switch (strtoupper($flow_subsection->getName())) {
                case 'SET':
                    $attribute =$this->_parseAttribute($flow_subsection);
                    $transition->addAttribute($attribute);
            }
        }
        return $transition;
    }
    
    protected function _parseAttribute(__ConfigurationSection &$flow_section) {
        $attribute = new __FlowAttribute();
        if($flow_section->hasAttribute('attribute') && $flow_section->hasAttribute('scope')) {
            $flow_attribute = new __FlowAttribute();
            $flow_attribute->setAttribute($flow_section->getAttribute('attribute'));
            $scope = __WebflowExpressionHelper::resolveScope($flow_section->getAttribute('scope'));
            $flow_attribute->setScope($scope);
            $attribute->setAttribute($flow_attribute);
        }
        if($flow_section->hasAttribute('value')) {
            $value = __WebflowExpressionHelper::resolveExpression($flow_section->getAttribute('value'));
            $attribute->setValue($value);
        }
        return $attribute;
    }
    
}

