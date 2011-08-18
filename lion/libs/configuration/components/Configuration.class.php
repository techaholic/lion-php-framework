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
 * @package    Configuration
 * 
 */


/**
 * Represents a configuration file applicable to a particular application.
 * This class cannot be inherited.
 * 
 * The __Configuration class represents the merged view of the configuration settings that are 
 * applied to a logical entity, such as an application, or a Web site.
 * 
 */
final class __Configuration extends __ComplexConfigurationComponent {
    
    /**
     * A list of all properties located on current configuration hierarchy at any level.
     * This list is useful for quickly access to any list without know the level/route for each one.
     *
     * @var array
     */
    private $_registered_properties = array();
    
    /**
     * The __Context owner instance
     *
     * @var __Context
     */
    private $_context_id = null;
    
    public function __construct($context_id = null) {
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }
        $this->_context_id = $context_id;
        parent::__construct();
    }
    
    /**
     * Merge a __Configuration into the current one.
     *
     * @param __Configuration $configuration A __Configuration to merge into.
     */
    public function merge(__Configuration &$configuration) {
        $this->_registered_properties = array_merge($configuration->_registered_properties, $this->_registered_properties);
        $this->_mergeComponent($configuration);
    }
            
    /**
     * This method returns the __Context instance that contains the current __Configuration.
     *
     * @return __Context
     */
    public function &getContext() {
        $return_value = __ContextManager::getInstance()->getContext($this->_context_id);
        return $return_value;
    }

    /**
     * Register a __ConfigurationProperty in cache for quickly access without know the route/level where the property
     *
     * @param __ConfigurationProperty $property
     */
    protected function registerProperty(__ConfigurationProperty &$property) {
        $property_name = $property->getName();
        if(!key_exists($property_name, $this->_registered_properties)) {
            $this->_registered_properties[$property->getName()] =& $property;
            __ConfigurationValueResolver::addSettingValue($property->getName(), $property->getContent());
        }
        else {
            if(!is_array($this->_registered_properties[$property_name])) {
                $stored_property =& $this->_registered_properties[$property_name];
                unset($this->_registered_properties[$property_name]);
                $this->_registered_properties[$property_name] = array($stored_property);
             }
            $this->_registered_properties[$property_name][] =& $property;
        }
    }

    /**
     * Checks if a property has been set within the current configuration settings
     *
     * @param string $name the property name
     * @return bool true if the property is part of the current configuration settings, otherwise false
     */
    public function hasProperty($name) {
        $return_value = false;
        if(key_exists($name, $this->_registered_properties)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * This method search for a __ConfigurationProperty instance filtering by name, content, attributes and/or index.
     * If this method matched more than one instance, it will return the first occurrence.
     *
     * @param string $name  The name of the property
     * @param string $content The content of the property
     * @param array $attributes Property's attributes
     * @param integer $index The index of the property
     * @return __ConfigurationProperty The requested property
     */
    public function &getProperty($name = null, $content = null, array $attributes = null, $index = -1) {
        $return_value = null;
        if( $name != null && key_exists($name, $this->_registered_properties) ) {
            if(is_array($this->_registered_properties[$name])) {
                $return_value =& $this->_filterComponent($this->_registered_properties[$name], 'property', $name, $content, $attributes, $index);
            }
            else {
                $return_value =& $this->_registered_properties[$name];
            }
        }
        else {
            $return_value =& parent::getProperty( $name, $content, $attributes, $index );
        }
        return $return_value;
    }
    
    /**
     * Returns all the registered properties (settings) within the current configuration instance
     *
     * @return array
     */
    public function getSettings() {
        return $this->_registered_properties;
    }
    
    /**
     * Returns a key/value pair array of the container and its children.
     *
     * Format : section[property][index] = value
     * If the container has attributes, it will use '@' and '#'
     * index is here because multiple propertys can have the same name.
     *
     * @param    bool    $use_attributes        Whether to return the attributes too
     * @return array
     */
    function toArray($use_attributes = true)
    {
        $return_value = parent::toArray();
        $return_value = $return_value[$this->_name];
        return $return_value;
    }     
    
}