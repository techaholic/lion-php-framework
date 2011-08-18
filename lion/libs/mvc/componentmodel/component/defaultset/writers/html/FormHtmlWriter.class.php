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
 * @package    ComponentModel
 * 
 */


class __FormHtmlWriter extends __ComponentWriter {
	
    public function startRender(__IComponent &$component)
    {
        $component_id = $component->getId();
        $component_properties = $component->getProperties();
        
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'id="' . $component_id . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'action = "' . __UriContainerWriterHelper::resolveUrl($component) . '"';
        $properties[] = 'method="' . strtoupper($component->getMethod()) . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }        
        
        $form_code  = '<form ' . join(' ', $properties) . ' onSubmit="return (__ClientEventHandler.getInstance()).handleSubmit(this);">' . "\n";
        $request_submit_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_SUBMIT_CODE');
        $form_code .= '<input type="HIDDEN" name="' . $request_submit_code . '" value="' . $component_id . '"></input>' . "\n";
        $flow_executor = __FlowExecutor::getInstance();
        if($flow_executor->hasActiveFlowExecution()) {
            $active_flow_execution = $flow_executor->getActiveFlowExecution();
            $request_flow_execution_key = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
            $form_code .= '<input type="HIDDEN" name="' . $request_flow_execution_key . '" value="' . $active_flow_execution->getId() . '"></input>' . "\n";
            $current_state = $active_flow_execution->getCurrentState();
            if($current_state != null) {
                $request_flow_state_id = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_FLOW_STATE_ID');
                $form_code .= '<input type="HIDDEN" name="' . $request_flow_state_id . '" value="' . $current_state->getId() . '"></input>' . "\n";
            }
        }
        
        $hidden_parameters = $component->getHiddenParameters();
        foreach($hidden_parameters as $hidden_parameter_name => $hidden_parameter_value) {
            if(strtoupper($hidden_parameter_name) != strtoupper($request_submit_code) &&
               strtoupper($hidden_parameter_name) != 'CLIENTENDPOINTVALUES') {
                $form_code .= '<input type="HIDDEN" name="' . $hidden_parameter_name . '" value="' . htmlentities($hidden_parameter_value) . '"></input>' . "\n";
            }
        }
        return $form_code;
    }
    
    public function endRender(__IComponent &$component) {
        return '</form>';
    }
    
}
