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

class __CommonHtmlDecorator extends __ComponentWriterDecorator {
    
	public function bindComponentToClient(__IComponent &$component) {
        $this->_component_writer->bindComponentToClient($component);
        
        $component_id = $component->getId();
        
        $cep = new __HtmlSelectiveElementCallback($component_id);
        $cep->addMappingCallback(true, 'show');
        $cep->addMappingCallback(false, 'hide');
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'visible'), $cep);
        __UIBindingManager::getInstance()->bindFromClientToServer(new __ComponentProperty($component, 'visible'), new __HtmlElementAccessor($component_id, 'visible'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'disabled'), new __HtmlElementProperty($component_id, 'disabled'));
    }
    
    public function endRender(__IComponent &$component) {
        $component_id = $component->getId();
        $return_value = $this->_component_writer->endRender($component);
        $place_after_element = $component->placeAfterElement;
        if(!empty($place_after_element)) {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('place-after-element' . $component_id);
            $jod_response_writer->addJsCode("var $component_id = $('$component_id').remove();\n
            $component_id.insert($place_after_element);\n");
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
        $waiting_components = $component->waitingComponents;
        if(!empty($waiting_components)) {
            if(__ResponseWriterManager::getInstance()->hasResponseWriter('waiting-components')) {
                $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('waiting-components');
            }
            else {
                $jod_response_writer = new __JavascriptOnDemandResponseWriter('waiting-components');
                $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
                $javascript_rw->addResponseWriter($jod_response_writer);              
            }            
            
            $waiting_components_array = preg_split('/,/', $waiting_components);
            foreach($waiting_components_array as $waiting_component_name) {
                $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($component->getViewCode());
                $waiting_component = $component_handler->getComponent($waiting_component_name);
                $waiting_component_id = $waiting_component->getId();
                $js_code = <<<CODE
(__ProgressBroadcaster.getInstance()).setWaitingDependence("$waiting_component_id", "$component_id");                
CODE;
                $jod_response_writer->addJsCode($js_code);
            }
        }
        return $return_value;
   }        
    
}