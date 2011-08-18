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

class __ValidationRuleHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'errorMessage'), new __ShowErrorMessage($component->getId()));
	}
    
    public function startRender(__IComponent &$component) {
        $component_id = $component->getId();
        $event_handler = __EventHandlerManager::getInstance()->getEventHandler($component->getViewCode());
        $component_to_validate = $component->getComponentToValidate();
        $component_to_validate_id = $component_to_validate->getId();
        if(__ResponseWriterManager::getInstance()->hasResponseWriter('livevalidation')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('livevalidation');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('livevalidation');
            $jod_response_writer->addJsFileRef('livevalidation/livevalidation_1.3.1.js');
            $jod_response_writer->addLoadCheckingVariable('Validate');
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
       
        $configuration = array('validMessage : ""');
        if($component->getValidateOnlyOnBlur()) {
            $configuration[] = 'onlyOnBlur: true';
        }
        else if($component->getValidateOnlyOnSubmit()) {
            $configuration[] = 'onlyOnSubmit: true';
        }
        else {
            $configuration[] = 'wait: ' . $component->getWait();
        }
        $report_after_component = $component->getComponentForErrorReporting();
        $report_after_element   = $component->getReportAfterElement();
        if($report_after_component != null) {
            $configuration[] = 'insertAfterWhatNode: "' . $report_after_component->getId() . '"';
        }
        else if($report_after_element != null) {
            $configuration[] = 'insertAfterWhatNode: "' . $report_after_element . '"';
        }
        
        if($event_handler->isEventHandled('validate', $component_to_validate->getName())) {
            $configuration[] = 'onValid: function() { (__ClientEventHandler.getInstance()).sendEvent("validate", { validationRule : "' . $component_id . '"}, "' . $component_to_validate_id . '", true); }';
        }
        else if($event_handler->isEventHandled('validate', $component->getName())) {
            $configuration[] = 'onValid: function() { (__ClientEventHandler.getInstance()).sendEvent("validate", { validationRule : "' . $component_id . '"}, "' . $component_id . '", true); }';
        }
        $on_invalid = $component->getOnInvalid();
        if(!empty($on_invalid)) {
            $configuration[] = 'onInvalid: ' . $on_invalid;
        }
        
        $js_code  = "if($(\"$component_to_validate_id\")) {\n";
        $js_code .= "if(!window['$component_id']) {\n";
        $js_code .= '    window[\'' . $component_id . '\'] = new LiveValidation("' . $component_to_validate_id . '", {' . join(', ', $configuration) . '});' . "\n";
        $js_code .= "} else {\n";
        $js_code .= '    ' . $component_id . '.initialize("' . $component_to_validate_id . '", {' . join(', ', $configuration) . '});' . "\n";
        $js_code .= "}\n";
        $js_code .= $this->_getValidLengthJsPart($component);
        $js_code .= $this->_getValidFormatJsPart($component);
        $js_code .= $this->_getComponentMatchJsPart($component);
        $js_code .= $this->_getMandatoryJsPart($component);
        $js_code .= $this->_getAcceptanceJsPart($component);
        $js_code .= $this->_getShowMessageJsPart($component);
        $js_code .= $this->_getAllowedExtensionsJsPart($component);
        $js_code .= $this->_getNumericalityJsPart($component);
        $js_code .= $this->_getListenValidateEvent($component);
        $js_code .= "}\n";
        $jod_response_writer->addJsCode($js_code);
    }
    
    private function _getValidLengthJsPart(__IComponent &$component) {
        $return_value = '';
        $length_parameters = array();
        $valid_length = $component->getValidLength();
        if($valid_length != null) {
            $length_parameters[] = 'is: ' . $valid_length;
        }
        else {
            $min_length = $component->getMinLength();
            if($min_length != null) {
                $length_parameters[] = 'minimum: ' . $min_length;
            }
            $max_length = $component->getMaxLength();
            if($max_length != null) {
                $length_parameters[] = 'maximum: ' . $max_length;
            }
        }
        if(count($length_parameters) > 0) {
            $return_value = $component->getId() . '.add(Validate.Length, {' . join(', ', $length_parameters) . '});' . "\n";
        }
        return $return_value;
    }
    
    private function _getValidFormatJsPart(__IComponent &$component) {
        $return_value = '';
        $valid_format = $component->getPattern();
        if($valid_format != null) {
            $return_value = $component->getId() . '.add(Validate.Format, { pattern: /' . $valid_format . '/i} );' . "\n";
        }
        return $return_value;
    }
    
    private function _getComponentMatchJsPart(__IComponent &$component) {
        $return_value = '';
        $component_to_match = $component->getComponentToMatch();
        if($component_to_match != null) {
            $return_value = $component->getId() . '.add(Validate.Confirmation, { match: \'' . $component_to_match->getId() . '\' });' . "\n";
        }
        return $return_value;
    }
    
    private function _getMandatoryJsPart(__IComponent &$component) {
        $return_value = '';
        if($component->getMandatory()) {
            $return_value = $component->getId() . '.add(Validate.Presence);' . "\n";
        }
        return $return_value;
    }

    private function _getAcceptanceJsPart(__IComponent &$component) {
        $return_value = '';
        if($component->getAcceptance()) {
            $return_value = $component->getId() . '.add(Validate.Acceptance);' . "\n";
        }
        return $return_value;
    }
    
    private function _getAllowedExtensionsJsPart(__IComponent &$component) {
        $return_value = '';
        $allowed_extensions = $component->getAllowedExtensions();
        if(is_array($allowed_extensions) && count($allowed_extensions) > 0) {
            $return_value = $component->getId() . '.add(Validate.AllowedFileExtensions, { allowedExtensions: /\.(' . join('|', $allowed_extensions) . ')$/i} );' . "\n";
        }
        return $return_value;
    }
    
    private function _getNumericalityJsPart(__IComponent &$component) {
        $return_value = '';
        $properties = array();
        if($component->getOnlyInteger()) {
            $properties[] = 'onlyInteger: true';
        }
        $specific_number = $component->getSpecificNumber();
        if($specific_number !== null) {
            $properties[] = 'is: ' . $specific_number;
        }
        $minimum_number = $component->getMinimumNumber();
        if($minimum_number !== null) {
            $properties[] = 'minimum: ' . $minimum_number;
        }
        $maximum_number = $component->getMaximumNumber();
        if($maximum_number !== null) {
            $properties[] = 'maximum: ' . $maximum_number;
        }
        if(count($properties) > 0) {        
            $return_value = $component->getId() . '.add(Validate.Numericality, { ' . join(', ', $properties) . '} );' . "\n";
        }
        return $return_value;
    }    
    
    private function _getListenValidateEvent(__IComponent &$component) {
        $component_to_validate = $component->getComponentToValidate();
        $return_value = 'document.observe("lion:validate", function(event) { if(event.target.id == "' . $component_to_validate->getId() . '") { ' . $component->getId() . '.removeMessageAndFieldClass(); event.validationResult = ' . $component->getId() . '.validate(); } } );' . "\n";
        return $return_value;
    }
    
    /**
     * Will render error message (if any) instead of delegating on async message commands 
     * to avoid race conditions
     *
     * @param __IComponent $component
     * @return string
     */
    public function _getShowMessageJsPart(__IComponent &$component) {
        $return_value = '';
        $error_message = $component->getErrorMessage();
        if(!empty($error_message)) {
            $return_value .= 'new __ShowValidationErrorCommand("' . $error_message . '", "' . $component->getId() . '").execute();' . "\n";
        }
        return $return_value;
    }
        
}