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

class __UICompositeComponentInterfaceHandler {

    protected $_component_interface = null;
    protected $_component = null;
    
    /**
     * Current values
     *
     * @var array
     */
    protected $_component_values = array();
    
    public function __construct(__UICompositeComponentInterfaceSpec &$composite_component_interface, 
                                __UICompositeComponent &$composite_component) {

        $this->_component_interface =& $composite_component_interface;
        $this->_component =& $composite_component;                                        
                                    
    }
    
    public function hasProperty($property_name) {
        return $this->_component_interface->hasComponentPropertySpec($property_name);
    }

    public function getComponentPropertySpec($property_name) {
        $return_value = null;
        if($this->_component_interface->hasComponentPropertySpec($property_name)) {
            $return_value = $this->_component_interface->getComponentPropertySpec($property_name);
        }
        return $return_value;
    }
    
    public function getProperty($property_name) {
        $return_value = null;
        $property_name = strtoupper($property_name);
        $component_property_spec = $this->getComponentPropertySpec($property_name);
        if($component_property_spec != null) {
            $receiver = $component_property_spec->resolveReceiver($this->_component);
            $receiver_property = $component_property_spec->getProperty();
            if($receiver != null) {
                if(property_exists($receiver, $receiver_property)) {
                    $this->_component_values[$property_name] = $receiver->$receiver_property;
                }
                else if(method_exists($receiver, 'get' . ucfirst($receiver_property))) {
                    $this->_component_values[$property_name] = call_user_func_array(array($receiver, 'get' . ucfirst($receiver_property)), array());
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('Property does not exists or can not be retrieved: ' . $property_name);
                }
            }
        }
        if(key_exists($property_name, $this->_component_values)) {
            $return_value = $this->_component_values[$property_name];
        }
        return $return_value;
    }
    
    public function setProperty($property_name, $property_value) {
        $property_name = strtoupper($property_name);
        $this->_component_values[$property_name] = $property_value;
        $this->_setPropertyToTargetInstance($property_name, $property_value);
    }
    
    protected function _setPropertyToTargetInstance($property_name, $property_value) {
        $property_name = strtoupper($property_name);
        $component_property_spec = $this->getComponentPropertySpec($property_name);
        if($component_property_spec != null) {
            $receiver = $component_property_spec->resolveReceiver($this->_component);
            $receiver_property = $component_property_spec->getProperty();
            if($receiver != null) {
                if(property_exists($receiver, $receiver_property)) {
                    $receiver->$receiver_property = $property_value;
                }
                else if(method_exists($receiver, 'set' . ucfirst($receiver_property))) {
                    call_user_func_array(array($receiver, 'set' . ucfirst($receiver_property)), array($property_value));
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('Property does not exists or can not be set: ' . $property_name);
                }
            }
        }
    }
    
    public function setupCompositeComponent() {
        foreach($this->_component_values as $property_name => $property_value) {
            $this->_setPropertyToTargetInstance($property_name, $property_value);
        }
    }
    
    
}
