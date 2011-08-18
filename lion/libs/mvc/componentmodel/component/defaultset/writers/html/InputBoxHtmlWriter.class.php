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


class __InputBoxHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __HtmlElementProperty($component->getId(), 'value'));
    }
    
    public function startRender(__IComponent &$component) {
        $component_id = $component->getId();
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        if(!key_exists('TYPE', $component_properties)) {
            $properties[] = 'type="text"';
        }
        $properties[] = 'id="' . $component_id . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $value = $component->getValue();
        $example_value = $component->getExampleValue();
        if(!empty($value)) {
            $properties[] = 'value="' . htmlentities($value) . '"';
        }
        else if(!empty($example_value)) {
            $properties[] = 'value="' . htmlentities($example_value) . '"';
        }

        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        
        $return_value = '<input ' . implode(' ', $properties) . '>';
        
        if(!empty($example_value)) {
            if(__ResponseWriterManager::getInstance()->hasResponseWriter('examplevalues')) {
                $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('examplevalues');
            }
            else {
                $jod_response_writer = new __JavascriptOnDemandResponseWriter('examplevalues');
                $js_code = <<<CODE
function hideExampleValue(event){
    var formfield = Event.element(event);
    var exampleValue = (\$A(arguments)).last();
    if (formfield.value == exampleValue) {
        formfield.value = "";
    }
}     
CODE;
                $jod_response_writer->addJsCode($js_code);
                $jod_response_writer->setLoadAfterDomLoaded(true);
                $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
                $javascript_rw->addResponseWriter($jod_response_writer);
            }
            $js_code = "Event.observe('$component_id', 'focus', hideExampleValue.bindAsEventListener(\$($component_id), '$example_value'));\n";
            $jod_response_writer->addJsCode($js_code);
        }
        
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '</input>';
    }
    
    
}
