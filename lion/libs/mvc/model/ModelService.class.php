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

class __ModelService extends __SystemResource {
    
    private $_class = null;
    private $_instance = null;
    private $_service = null;
    private $_alias = null;
    private $_cache = false;
    private $_cache_ttl = null;
    private $_remote = false;
    private $_arguments = array();
    
    public function __construct($alias) {
        $this->_alias = $alias;
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
    
    public function setRemote($remote) {
        $this->_remote = $remote;
    }
    
    public function getRemote() {
        return $this->_remote;
    }
    
    public function isRemote() {
        return $this->_remote;
    }

    public function addArgument(__ModelServiceArgument &$argument) {
        $this->_arguments[$argument->getIndex()] = $argument;
    }
    
    public function setArguments(array $arguments) {
        $this->_arguments = $arguments;
    }
    
    public function getArguments() {
        return $this->_arguments;
    }    
    
    protected function _validate() {
        if($this->_class == null && $this->_instance == null) {
            throw __ExceptionFactory::getInstance()->createException('Need to specify either a class name or a context instance identifier for __ModelService ' . $this->_alias);
        }
        if($this->_service == null) {
            throw __ExceptionFactory::getInstance()->createException('Need to specify a service name for __ModelService ' . $this->_alias);
        }
    }
    
    
    protected function _resolveParameters($parameters) {
        if($parameters instanceof __IRequest) {
            $required_parameters = $this->getRequiredParameters();
            foreach($required_parameters as $required_parameter_name => $empty_value) {
                if($request->hasParameter($required_parameter_name)) {
                    $json_string = $request->getParameter($required_parameter_name);
                    $parameter_value = json_decode($json_string, true);
                    $required_parameters[$parameter_name] = $parameter_value;
                }
            }
        }
    }
    
    public function &callAsRemoteService(__IRequest &$request) {
        $mapped_parameters = array();
        foreach($this->_arguments as $argument) {
            $argument_name = $argument->getName();
            if($request->hasParameter($argument_name)) {
                $parameter_value = $request->getParameter($argument_name);
                if($argument->isJson()) {
                    $parameter_value = json_decode($parameter_value, true);
                }
                $mapped_parameters[$argument->getIndex()] =& $parameter_value; 
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Error calling remote service ' . $this->_service . ': missing argument ' . $argument_name);
            }
        }
        ksort($mapped_parameters);
        return $this->call($mapped_parameters);        
    }
    
    /**
     * This method performs the call to the selected model service.
     *
     * @param array A set of parameters to send in the service call (optional, if not defined, the specified in the constructor call will be used by default)
     * @return mixed The value returned by the model service
     * @exception __ModelException if a non-existent service is trying to called
     */
    public function &call(array &$parameters = null) {
        $return_value = null;
        $this->_validate();
        try {
            if($this->_cache) {
                $service_id = md5($this->_class . '::' . $this->_service . '::' . serialize($parameters));
                $cache = __ApplicationContext::getInstance()->getCache();
                $data = $cache->getData($service_id, $this->_cache_ttl);
                if($data !== null) {
                    return $data;
                }
            }
            if($this->_class != null) {
                $class_name  = $this->_class;
                $model_instance = new $class_name();
            }
            else if($this->_instance != null) {
                $model_instance = __CurrentContext::getInstance()->getContextInstance($this->_instance);
                if($model_instance == null) {
                    throw __ExceptionFactory::getInstance()->createException('Unknown context instance: ' . $this->_instance);
                }
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Unknown model receiver to dispatch the model service ' . $this->_service);
            }
            $return_value = call_user_func_array (array($model_instance, $this->_service), $parameters);
            if($this->_cache) { 
                $cache->setData($service_id, $return_value, $this->_cache_ttl);           
            }
        }
        catch (Exception $e) {
            if( $e instanceof __LionException ) {
                throw $e;
            }
            else {
                throw new __ModelException($e->getMessage(), $e->getCode());
            }
        }
        return $return_value;
    }    
    
}