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

class __UICompositeComponentInterfaceSpec {

    protected $_component_property_specs = array();
    
    public function clearComponentPropertySpecs() {
        $this->_component_property_specs = array();
    }
    
    public function hasComponentPropertySpec($property_name) {
        $return_value = false;
        $property_name = strtoupper($property_name);
        if(key_exists($property_name, $this->_component_property_specs)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setComponentPropertySpecs(array $component_property_specs) {
        $this->clearComponentPropertySpecs();
        foreach($component_property_specs as $component_property_spec) {
            $this->addComponentPropertySpec($component_property_spec);
        }
    }

    public function addComponentPropertySpec(__ComponentPropertySpec &$component_property_spec) {
        $property_name = strtoupper($component_property_spec->getName());
        $this->_component_property_specs[$property_name] = $component_property_spec;
    }
    
    public function getComponentPropertySpecs() {
        return $this->_component_property_specs;
    }
    
    public function getComponentPropertySpec($property_name) {
        $return_value = null;
        $property_name = strtoupper($property_name);
        if(key_exists($property_name, $this->_component_property_specs)) {
            $return_value = $this->_component_property_specs[$property_name];
        }
        return $return_value;
    }
    
    
}
