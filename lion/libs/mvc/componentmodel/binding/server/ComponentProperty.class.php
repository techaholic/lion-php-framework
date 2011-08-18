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

/**
 * Represents a component's property as a server end-point
 * 
 * @see __IServerEndPoint, __IEndPoint, __UIBinding
 *
 */
class __ComponentProperty extends __ServerEndPoint implements __IValueHolder {
    
    protected $_property = null;
    protected $_binding_value = null;
    protected $_mapping_values = array();
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_ALL;    
    
    /**
     * Constructor method
     *
     * @param __IComponent $component The component associated to this end-point
     * @param string $property The component property
     */
    public function __construct(__IComponent &$component, $property, $binding_value = null) {
        $this->setComponent($component);
        $this->setProperty($property);
        $this->setBindingValue($binding_value);
    }
    
    public function addMappingValue($component_value, $mapping_value) {
        $this->_mapping_values[$component_value] = $mapping_value;
    }
    
    public function setBindingValue($binding_value) {
        $this->_binding_value = $binding_value;
    }
    
    public function getBindingValue() {
        return $this->_binding_value;
    }
    
    public function unsetValue() {
        $this->_binding_value = null;
    }
    
    /**
     * Sets the property
     *
     * @param string $property The property
     */
    public function setProperty($property) {
        $this->_property = $property;
    }
    
    /**
     * Gets the property
     *
     * @return string
     */
    public function getProperty() {
        return $this->_property;
    }

    /**
     * Gets the bound direction allowed by this end-point
     *
     * @return integer
     */
    public function getBoundDirection() {
        $return_value = $this->_bound_direction;
        if($this->_binding_value !== null && $this->_binding_value !== $this->getValue()) {
            $return_value = $return_value & __IEndPoint::BIND_DIRECTION_C2S;
        }
        return $return_value;
    }
    
    /**
     * Sets a value to this end-point. 
     * 
     * If the value is different to the current one, will set the new value to the client end-point.
     *
     * @param mixed $value The value to set to
     */
    public function setValue($value) {
        if($this->_updateComponentProperty($value)) {
            $this->_ui_binding->synchronizeClient();
        }
    }
    
    public function reset() {
        $this->setValue(null);
    }
    
    /**
     * Gets the value associated to current end-point
     *
     * @todo add mapping values capability (translation rules for values from server to client end-point)
     * 
     * @return mixed
     */
    public function getValue() {
        $return_value = null;
        $component = $this->getComponent();
        if($component != null) {
            $property  = $this->getProperty();
            if(property_exists($component, $property)) {
                $return_value = $component->$property;
            }
            else if(method_exists($component, 'get' . ucfirst($property))) {
                $return_value = call_user_func(array($component, 'get' . ucfirst($property)));
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Unknow ' . get_class($component) . ' property: ' . $property);
            }
        }
        return $return_value;
    }

    /**
     * Synchronize server end-point value according to the client end-point
     *
     * @todo add mapping values capability (translation rules for values from server to client end point)
     * 
     * @param __IClientEndPoint $client_end_point
     */
    public function synchronize(__IClientEndPoint &$client_end_point) {
        $value = $client_end_point->getValue();
        return $this->_updateComponentProperty($value);     
    }
    
    protected function _updateComponentProperty($value) {
        $return_value = false;
        $component = $this->getComponent();
        if($component != null) {
            $property  = $this->getProperty();
            if($component->$property !== $value) {
                $component->$property = $value;
                $return_value = true;
            }
        }
        return $return_value;
    }
    
}