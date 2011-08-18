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

class __ToggleButtonHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        $component_id = $component->getId();
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'caption'), new __JavascriptObjectProperty($component_id, 'caption'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __JavascriptObjectProperty($component_id, 'value'));
    }
    
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        
        $jod_response_writer = $this->_getJavascriptResponseWriter();
        
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        $value = $component->getValue();
        if($value) {
            $properties[] = 'class="depressed"';
        }
        else {
            $properties[] = 'class="raised"';
        }
        
        $width = $component->getWidth();
        $height = $component->getHeight();
        
        $style = array();
        if($width != null) {
            $style[] = 'width:' . $width;
        }
        if($height != null) {
            $style[] = 'height:' . $height;
        }
        
        $return_value = '<span style="display: inline-block; ' . implode('; ', $style) . '" onclick="toggleButton(this);" ' . implode(' ', $properties) . '>';
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $component->getCaption();
    }
    
    public function endRender(__IComponent &$component) {
        return '</span>';
    }
    
    protected function &_getJavascriptResponseWriter() {
        $js_code = <<<CODESET
        
function toggleButton(element) {
    if(element.className == 'depressed') {
        element.className = 'raised';
    }
    else {
        element.className = 'depressed';
    }
}
        
        
CODESET;

        if(__ResponseWriterManager::getInstance()->hasResponseWriter('togglebutton')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('togglebutton');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('togglebutton');

            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
            $jod_response_writer->addJsCode($js_code); 
        }
        
        if(!__ResponseWriterManager::getInstance()->hasResponseWriter('togglebuttoncss')) {
            $css_response_writer = new __CssResponseWriter('togglebuttoncss');
            $css_rules = <<<CODESET
         
span.depressed {
    text-align: center;
    border:1px inset #CCCCCC;    
    color: black;
    background: url(media/images/backgrounds/button_pressed.gif) repeat-x;    
    padding: 1px 4px 1px 4px;
    line-height: 1.4em;
}
span.raised {
    text-align: center;
    border:1px outset #CCCCCC;    
    color: black;
    background: url(media/images/backgrounds/button.gif) repeat-x;    
    background-color: #F3F3EE;
    padding: 1px 4px 1px 4px;
    line-height: 1.4em;
}
span.depressed:hover {
    cursor: pointer;
    cursor: hand;
}
span.raised:hover {
    cursor: pointer;
    cursor: hand;
}
            
CODESET;
            $css_response_writer->addCssRules($css_rules);
            __ResponseWriterManager::getInstance()->addResponseWriter($css_response_writer);
        }
        

        return $jod_response_writer;
    }        
    
    
    
}
