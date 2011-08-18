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

/**
 * This class is useful to call a model's service by checking access permissions
 *
 */
final class __ModelProxy {
    
    private $_model_service_definitions = array();

    /**
     * This is a singleton reference to the current __ModelProxy instance
     *
     * @var __ModelProxy
     */
    static protected $_instance = null;
        
    /**
     * This is the constructor method of the __ModelProxy.
     * First time a __ModelProxy instance is created, it loads the model's service xml specification (lazy load)
     *
     */
    public function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        $model_service_definitions = $session->getData('__ModelProxy::_model_service_definitions');
        if ($model_service_definitions == null) {
            $model_service_definitions = __CurrentContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('model-services');
        }
        if($model_service_definitions != null) {
            $this->_model_service_definitions =& $model_service_definitions;
        }
    }
    
    /**
     * This method return a singleton reference to the current instance
     *
     * @return __ModelProxy A singleton reference to the current __ModelProxy
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ModelProxy();
        }
        return self::$_instance;
    }    

    /**
     * This overloading method permit to the ModelProxy to be called for inexistent method (i.e. GetUserName).
     * ModelProxy will resolve the real class and method to call, will instanciate it and reroute the call.
     * Of course, it will return the returned value to the caller.
     * This overloading method is an alias of executeModelService that makes transparent where is the real method that is called.
     *
     * @param string $ The alias of the method
     * @param array $ The parameters to use with the real method
     * @return mixed The returned value/s of the real method
     */
    public function __call($alias, $parameters) {
        $ref_parameters = array();
        foreach($parameters as $parameter_name => &$parameter_value) {
            $ref_parameters[$parameter_name] =& $parameter_value;
        }
        return $this->executeModelService($alias, $ref_parameters);
    }
    
    /**
     * This is the main proxy method that redirect calls to the model tier.
     * This method resolves the real class and method to call, will instanciate it and reroute the call.
     * Of course, it will return the returned value to the caller.
     *
     * @param string $ The alias of the method
     * @param array $ The parameters to use with the real method
     * @return mixed The returned value/s of the real method
     */
    public function &executeModelService($alias, &$parameters) {
        if($this->hasModelService($alias)) {
            $model_service = $this->getModelService($alias);
            return $model_service->call($parameters);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_MODEL_SERVICE_NOT_EXISTS', array($alias));
        }
    }
    
    /**
     * Gets if a service has been declared as remote, which means that the service can be called remotelly
     * 
     * @param string $ The alias of the service
     * @return bool
     */
    public function isRemoteService($alias) {
        $return_value = false;
        if($this->hasModelService($alias)) {
            $model_service = $this->getModelService($alias);
            $return_value = $model_service->isRemote();
        }
        return $return_value;
    }
    
    /**
     * Alias of {$link __ModelProxy::isRemoteService()}
     * 
     * @param string $ The alias of the service
     * @return bool
     */
    public function isRemote($alias) {
        return $this->isRemoteService($alias);
    }
    
    /**
     * This method checks if a model's service exists, and return true if found.
     *
     * @param string The alias of the service to check
     * @return boolean true if the model service exists, else false
     */
    public function hasModelService($alias)
    {
        return key_exists($alias, $this->_model_service_definitions);
    }
    
    /**
     * Gets the instance representing a given service
     * 
     * @param $alias
     * @return __ModelService
     */
    public function &getModelService($alias)
    {
        if(key_exists($alias, $this->_model_service_definitions)) {
            $model_service_definition =& $this->_model_service_definitions[$alias];
            return $model_service_definition->getModelService();
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_MODEL_SERVICE_NOT_EXISTS', array($alias));
        }
    }

}