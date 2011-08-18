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


class __ComboBoxHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'itemValues'), new __HtmlElementProperty($component->getId(), 'items'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'selectedIndex'), new __HtmlElementProperty($component->getId(), 'selectedIndex'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        $style = array();
        foreach($component_properties as $property => $value) {
            if($property != 'STYLE') {
                $properties[] = $property . '="' . $value . '"';
            }
            else {
                $style[] = $value;
            }
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        if($component->getVisible() == false) {
            $style[] = 'display : none;';
        }
        if(count($style > 0)) {
            $style_attribute = 'style = "' . implode('', $style) . '"';
        }
        else {
            $style_attribute = null;
        }
        $return_value = '<select ' . implode(' ', $properties) . ' ' . $style_attribute . '>';
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $return_value = '';
        $component_items = $component->getItems();
        foreach($component_items as $component_item) {
            $return_value .= '<option value="' . htmlentities($component_item->getValue()) . '"';
            if($component_item->getSelected()) {
                $return_value .= ' selected="selected"';
            }
            $properties = array();
            $component_item_properties = $component_item->getProperties();
            foreach($component_item_properties as $property => $value) {
                $properties[] = $property . '="' . $value . '"';
            }
            
            $return_value .= ' ' . implode(' ', $properties) . '>' . $component_item->getText() . '</option>';
        }
        return $return_value;
    }
    
    public function endRender(__IComponent &$component)
    {
        return '</select>';
    }    
    
}
