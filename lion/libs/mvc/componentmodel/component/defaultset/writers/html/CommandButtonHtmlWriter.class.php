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


class __CommandButtonHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'caption'), new __HtmlElementProperty($component->getId(), 'value'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'src'), new __HtmlElementProperty($component->getId(), 'src'));
    }
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        if($component->getType() != null) {
            $properties[] = 'type="' . $component->getType() . '"';
        }
        else if($component->getOnClickSubmit()) {
            $properties[] = 'type="submit"';
        }
        else {
            $properties[] = 'type="button"';
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'value="' . htmlentities($component->getCaption()) . '"';
        
        $image_src = $component->getSrc();
        if($image_src != null) {
            $properties[] = 'src="' . $image_src . '"';
        }
                
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }        
        $return_value = '<input ' . implode(' ', $properties) . '>';
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '</input>';
    }
    
    
}
