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


class __WindowHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        
        $component_id = $component->getId();
        
        if($component->getModal()) {
            $show_code = $component_id . '.showCenter(true);';
        }
        else {
            $show_code = $component_id . '.showCenter();';
        }
        $js_code = 'if({value} == 1) {' . $show_code . '} else {'.$component_id . '.close();}';
        
        $cep = new __JavascriptOnDemand($js_code);
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'visible'), $cep);     
        
        $cep = new __JavascriptCallback($component_id, 'setHTMLContent');
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'content'), $cep);

        $cep = new __JavascriptObjectProperty($component_id, 'url');
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'url'), $cep);

        $cep = new __JavascriptObjectProperty($component_id, 'title');
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'title'), $cep);
        
        $cep = new __JavascriptObjectProperty($component_id, 'width');
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'width'), $cep);
        
        $cep = new __JavascriptObjectProperty($component_id, 'height');
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'height'), $cep);

    }

    public function startRender(__IComponent &$component) {
        
        $component_id = $component->getId();
        $class_name   = $component->getClassName();

        $window_properties = array();
        $window_properties[] = 'width: ' . $component->getWidth();
        $window_properties[] = 'height: ' . $component->getHeight();
        $window_properties[] = 'className: "' . $class_name . '"';
        $window_properties[] = 'destroyOnClose: false';
        $window_properties[] = 'recenterAuto: false';
        $window_properties[] = 'id: "' . $component->getId() . '"';
        if($component->getTitle() !== null) {
            $window_properties[] = 'title: "' . $component->getTitle() . '"';
        }
        if(!$component->getShowCloseButton()) {
            $window_properties[] = 'closable: false';
        }
        if(!$component->getShowMaximizeButton()) {
            $window_properties[] = 'maximizable: false';
        }
        if(!$component->getShowMinimizeButton()) {
            $window_properties[] = 'minimizable: false';
        }
                
        if(__ResponseWriterManager::getInstance()->hasResponseWriter('prototype_window')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('prototype_window');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('prototype_window');
            
            $jod_response_writer->addCssFileRef('prototypewindow/themes/default.css');
            if($class_name != 'dialog') {
                $jod_response_writer->addCssFileRef('prototypewindow/themes/' . $class_name . '.css');
            }
            $jod_response_writer->addJsFileRef('prototypewindow/javascripts/window.js');
            $jod_response_writer->addLoadCheckingVariable('Window');            
            $jod_response_writer->addLoadCheckingVariable('Windows');            
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
        $js_code  = "if(!window['$component_id']) {\n";
        $js_code .= "    $component_id = new Window({" . join(', ', $window_properties) . "});\n";
        $js_code .= "    $component_id.setCloseCallback(function() {\n";
        $js_code .= "        (__ClientEventHandler.getInstance()).sendEvent(\"close\", {}, \"$component_id\");\n";
        $js_code .= "        return true;\n";
        $js_code .= "    });\n";
        $content = $component->getContent();
        if(!empty($content)){
            $js_code .= "    $component_id.setHTMLContent(".json_encode($content).");\n";
        }
        $js_code .= "}\n";
        
        $jod_response_writer->addJsCode($js_code); 

    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $component->setContent($enclosed_content);
        return '';
    }
    
}
