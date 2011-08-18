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
 * @package    Context
 * 
 */

/**
 * Represents the definition of a context instance.
 * Each attribute within the context instances XML specification corresponds to a setter in this class
 *
 */
class __InstanceDefinition {
    
    const SCOPE_SINGLETON = 'singleton';
    const SCOPE_PROTOTYPE = 'prototype';
    const SCOPE_REQUEST   = 'request';
    const SCOPE_SESSION   = 'session';
    const SCOPE_ALL       = 'all';
    
    protected $_constructor_arguments = null;
    protected $_class               = null;
    protected $_id                  = null;
    protected $_context_id          = null;
    protected $_shutdown_method     = null;
    protected $_factory_instance_id = null;
    protected $_factory_method      = null;
    protected $_startup_method      = null;
    protected $_properties          = null;
    protected $_is_singleton        = true;
    protected $_is_lazy             = false;
    protected $_scope               = self::SCOPE_SESSION;
    
    final public function __construct($id) {
        $this->_id = $id;
        $this->_properties = new __PropertiesCollection();
        $this->_constructor_arguments = new __ConstructorArgumentsCollection();
    }

    public function setContext(__Context &$context) {
        $this->_context_id = $context->getContextId();
    }
    
    public function &getContext() {
        return __ContextManager::getInstance()->getContext($this->_context_id);
    }
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function getClass() {
        return $this->_class;
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function addConstructorArgument(&$argument_value, $index = null) {
        $this->_constructor_arguments->add($argument_value, $index);
    }
    
    public function &getConstructorArguments() {
        return $this->_constructor_arguments;
    }
    
    public function setDependsOn(array $depends_on) {
        $this->_depends_on = $depends_on;        
    }
    
    public function getDependsOn() {
        return $this->_depends_on;
    }
    
    public function setShutdownMethod($shutdown_method) {
        $this->_shutdown_method = $shutdown_method;
    }
    
    public function getShutdownMethod() {
        return $this->_shutdown_method;
    }
    
    public function setFactoryInstanceId($factory_instance_id) {
        $this->_factory_instance_id = $factory_instance_id;
    }
    
    public function getFactoryInstanceId() {
        return $this->_factory_instance_id;
    }
    
    public function setFactoryMethod($factory_method) {
        $this->_factory_method = $factory_method;
    }

    public function getFactoryMethod() {
        return $this->_factory_method;
    }
    
    public function setStartupMethod($startup_method) {
        $this->_startup_method = $startup_method;
    }
    
    public function getStartupMethod() {
        return $this->_startup_method;
    }
    
    public function setProperties(__PropertiesCollection &$properties) {
        $this->_properties =& $properties;
    }

    public function addProperty($property_name, &$property_value) {
        $this->_properties->add($property_value, $property_name);
    }
        
    public function getProperties() {
        return $this->_properties;
    }
    
    public function &getProperty($property_name) {
        $return_value = null;
        if( $this->_properties->hasKey($property_name) ) {
            $return_value = $this->_properties->get($property_name);
        }
        return $return_value;
    }
    
    public function setLazy($is_lazy) {
        $this->_is_lazy = (bool) $is_lazy;
    }
    
    public function isLazy() {
        return $this->_is_lazy;
    }
    
    public function setSingleton($is_singleton) {
        $this->_is_singleton = (bool) $is_singleton;
    }
    
    public function isSingleton() {
        return $this->_is_singleton;
    }
    
    public function setScope($scope) {
        switch($scope) {
            case self::SCOPE_PROTOTYPE:
                $this->_scope = self::SCOPE_PROTOTYPE;
                $this->setSingleton(false);
                break;
            case self::SCOPE_REQUEST:
                $this->_scope = self::SCOPE_REQUEST;
                break;
            case self::SCOPE_SESSION:
                $this->_scope = self::SCOPE_SESSION;
                break;
            case self::SCOPE_SINGLETON:
                $this->_scope = self::SCOPE_SINGLETON;
                $this->setSingleton(true);
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Unknown scope type: ' . $scope);
                break;                
        }
    }
    
    public function getScope() {
        return $this->_scope;
    }
    
    public function validate() {
        $return_value = true;
        if($this->_class == null && $this->_factory_instance_id == null) {
            throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_CLASS_REQUIRED', array($this->_id));
        }
        if($this->_class != null && !class_exists($this->_class)) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CLASS_NOT_FOUND', array($this->_class));
        }        
        return $return_value;
    }
    
}