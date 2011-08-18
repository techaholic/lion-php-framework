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


abstract class __UIContainer extends __UIComponent implements __IContainer {
    
    protected $_components  = array();
    
    public function addComponent(__IComponent &$component) {
        //protect to infinite recursion
        if(!key_exists($component->getId(), $this->_components)) {
            $this->_components[$component->getId()] =& $component;
            $component->setContainer($this);
        }
        return $this;
    }
    
    public function removeComponent($component_id) {
        if(key_exists($component_id, $this->_components)) {
            unset($this->_components[$component_id]);
        }
    }
    
	public function &getComponentsByClass($class_name) {
		return $this->_searchForComponentsByClass($this, $class_name);
	}
	
	private function &_searchForComponentsByClass(&$parent_component, $class_name) {
		$return_value = array();
		$components =& $parent_component->getChildComponents();
		foreach($components as &$component) {
			if( $component instanceof $class_name ) {
				$return_value[$component->getId()] =& $component;
			}
			if( $component instanceof __UIContainer ) {
				$subcomponents = $this->_searchForComponentsByClass($component, $class_name);
				foreach($subcomponents as &$subcomponent) {
					$return_value[$subcomponent->getId()] =& $subcomponent;
				}
			}
		}
		return $return_value;
	}
    
    public function &getChildComponents() {
    	return $this->_components;
    }
    
    public function &getValueHolderComponents() {
        $return_value = array();
		$child_components = $this->getChildComponents();
		foreach($child_components as &$child_component) {
		    if( $child_component instanceof __IValueHolder ) {
    		    $return_value[$child_component->getId()] =& $child_component;
		    }
		    if($child_component instanceof __IContainer) {
		        $grand_child_components = $child_component->getValueHolderComponents();
		        foreach($grand_child_components as &$grand_child_component) {
                    $return_value[$grand_child_component->getId()] =& $grand_child_component;
		        }
		    }
		}
		return $return_value;
    }
    
}