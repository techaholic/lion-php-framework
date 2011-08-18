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
 * This class contains the information to create and render a component.
 *
 */
final class __ComponentSpec {
	
    /**
     * The UI tag used to build the current instance
     *
     * @var string
     */
    protected $_tag = null;
    
    /**
     * The component class
     *
     * @var string
     */
    protected $_class = null;
    
    /**
     * The component writer class
     *
     * @var string
     */
    protected $_writer = null;
    
    /**
     * Component default values
     *
     * @var array
     */
    protected $_default_values = array();
    
    /**
     * An unique id
     *
     * @var string
     */
    protected $_id = null;
    
    protected $_is_array = false;
    
    protected $_index = null;
    
    protected $_ui_component_interface = null;
    
    /**
     * Constructor.
     *
     * @param string $tag_name The UI tag
     * @param string $component_class The component class
     */
    public function __construct($tag_name, $component_class) {
        if(!class_exists($component_class)) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CLASS_NOT_FOUND', array($component_class));
        }
        if(!is_subclass_of($component_class, '__IComponent')) {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNEXPECTED_CLASS', array($component_class, '__IComponent'));
        }
        $this->_tag   = $tag_name;
        $this->_class = $component_class;
        $this->_id    = uniqid();
    }
    
    /**
     * Set the component writer associated to the current instance
     *
     * @param __IComponentWriter &$component_writer The component writer class
     */
    public function setWriter(__IComponentWriter &$component_writer) {
        $this->_writer =& $component_writer;
    }

    public function &getWriter() {
        return $this->_writer;
    }
    
    /**
     * Gets an unique id for current instance
     *
     * @return string
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Gets the name property associated to the component, or
     * the component spec id if no names was set.
     *
     * @return string
     */
    public function getName() {
        if(key_exists('name', $this->_default_values)) {
            $return_value = $this->_default_values['name'];
        }
        else {
            $return_value = $this->getId();
        }
        return $return_value;
    }
    
    /**
     * Gets the tag used to build the current instance
     *
     * @return string
     */
    public function getTag() {
        return $this->_tag;
    }
    
    /**
     * Gets the component class associated to current instance
     *
     * @return string
     */
    public function getClass() {
        return $this->_class;
    }
        
    /**
     * Set a default value associated to the component represented by current instance
     *
     * @param string $property_name Property name
     * @param mixed $property_value Property value
     */
    public function addDefaultValue($property_name, $property_value) {
        if(strtoupper($property_name) == 'NAME') {
            if(preg_match('/^([^\[]+)\[\]$/', $property_value, $matched)) {
                $property_value = $matched[1];
                $this->_is_array = true;
            }
        }
        $this->_default_values[$property_name] = $property_value;
    }
    
    /**
     * Set all the default values associated to the component represented by current instance
     *
     * @param array $default_values An array of property pairs [name, value]
     */
    public function setDefaultValues(array $default_values) {
        foreach($default_values as $name => $value) {
            $this->addDefaultValue($name, $value);
        }
    }
    
    /**
     * Get the default value associated to the component represented by current instance
     *
     * @param string $property_name Property name
     * @return mixed The value
     */
    public function getDefaultValue($property_name) {
        $return_value = null;
        $property_name = strtolower($property_name);
        if(key_exists($property_name, $this->_default_values)) {
            $return_value = $this->_default_values[$property_name];
        }
        return $return_value;
    }

    public function getDefaultValues() {
        return $this->_default_values;
    }    
    
    public function __get($key) {
       return $this->getDefaultValue($key);
    }

    public function __set($key, $value) {
        $this->addDefaultValue($key, $value);
    }
    
    public function isArray() {
        return $this->_is_array;
    }
    
    public function setIndex($index) {
        $this->_index = $index;
    }
    
    public function getIndex() {
        return $this->_index;
    }    
    
    public function setComponentInterfaceSpec(__UICompositeComponentInterfaceSpec $ui_component_interface) {
        $this->_ui_component_interface = $ui_component_interface;
    }    
    
    public function getComponentInterfaceSpec() {
        return $this->_ui_component_interface;
    }
    
}
