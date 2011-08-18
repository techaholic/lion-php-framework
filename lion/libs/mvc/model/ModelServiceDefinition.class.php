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
 * @package    Model
 * 
 */

class __ModelServiceDefinition extends __SystemResourceDefinition {
    
    protected $_class     = null;
    protected $_instance  = null;
    protected $_service   = null;
    protected $_alias     = null;
    protected $_remote    = false;
    protected $_cache     = false;
    protected $_cache_ttl = null;    
    protected $_arguments = array();
    
    public function __construct($alias) {
        $this->_alias = $alias;
    }
    
    public function getAlias() {
        return $this->_alias;
    }
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function getClass() {
        return $this->_class;
    }
    
    public function setInstance($instance) {
        $this->_instance = $instance;
    }
    
    public function getInstance() {
        return $this->_instance;
    }
    
    public function setService($service) {
        $this->_service = $service;
    }
    
    public function setRemote($remote) {
        $this->_remote = __ConfigurationValueResolver::resolveValue($remote);
    }

    public function getRemote() {
        return $this->_remote;
    }
    
    public function getService() {
        return $this->_service;
    }
    
    public function setCache($cache) {
        $this->_cache = (bool) $cache;
    }
    
    public function getCache() {
        return $this->_cache;
    }
    
    public function setCacheTtl($cache_ttl) {
        $this->_cache_ttl = $cache_ttl;
    }
    
    public function getCacheTtl() {
        return $this->_cache_ttl;
    }    
    
    public function addArgument(__ModelServiceArgument &$argument) {
        $argument_index = $argument->getIndex();
        if(empty($argument_index)) {
            $argument_index = count($this->_arguments);
            $argument->setIndex($argument_index);
        }
        $this->_arguments[$argument_index] = $argument;
    }
    
    public function getArguments() {
        return $this->_arguments;
    }
    
    public function &getModelService() {
        $return_value = new __ModelService($this->getAlias());
        $class = $this->getClass();
        $instance = $this->getInstance();
        if($class != null) {
            $return_value->setClass($class);
        }
        else if($instance != null) {
            $return_value->setInstance($instance);
        }
        $return_value->setService($this->getService());
        $return_value->setCache($this->getCache());
        $return_value->setCacheTtl($this->getCacheTtl());
        $return_value->setRemote($this->getRemote());
        $return_value->setArguments($this->getArguments());
        if($this->getRequiredPermissionId() != null) {
            $required_permission = __PermissionManager::getInstance()->getPermission($this->getRequiredPermissionId());
            $return_value->setRequiredPermission($required_permission);
        }
    	return $return_value;
        
    }
    
}