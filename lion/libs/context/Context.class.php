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
 * Class representing a context container
 * 
 * The application context can be retrieved by calling the {@link __ApplicationContext::getInstance()} singleton method.
 * 
 */
class __Context {
    
    protected $_context_id = null;
    
    protected $_uniq_code  = null;
    
    protected $_session_scope_instances = array();
    
    protected $_request_scope_instances = array();
    
    protected $_instance_definitions = array();
    
    protected $_request_scope_instance_definitions = array();
    
    protected $_instances_requested = array();
    
    protected $_configuration = null;
    
    protected $_configuration_loader = null;
    
    protected $_context_base_dir = null;
    
    protected $_instance_factory = null;
    
    public function __construct($context_id, $context_base_dir) {
        $this->_context_id = $context_id;
        if(is_readable($context_base_dir) && is_dir($context_base_dir)) {
            $this->_context_base_dir = $context_base_dir;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Error trying to set unreadable or unexistent directory (' . $context_base_dir . ') as base directory for context ' . $context_id);
        }
    }
    
    public function __destruct() {
//        $this->_executeShutdownMethods();
    }

    public function loadConfiguration($configuration_file = null) {
        //read context configuration:
        $this->_configuration_loader = new __ConfigurationLoader($this->_context_id);
        $this->_configuration = $this->_configuration_loader->loadConfiguration($configuration_file);

        //process and classify the instance definitions:
        $instance_definitions = $this->_configuration->getSection('configuration')->getSection('context-instances');
        if(is_array($instance_definitions)) {
            $this->_instance_definitions = $instance_definitions[__InstanceDefinition::SCOPE_ALL];
            $this->_request_scope_instance_definitions = $instance_definitions[__InstanceDefinition::SCOPE_REQUEST];
        }
        
    }
    
    /**
     * Gets the session associated to current context
     *
     * @return __Session
     */
    public function &getSession() {
        return __SessionManager::getInstance()->getSession($this->_context_id);
    }
    
    /**
     * Gets the cache associated to current context
     *
     * @return __Cache
     */
    public function &getCache() {
        //cache is shared between all contexts:
        return __CacheManager::getInstance()->getCache();
    }
    
    /**
     * Gets the logger associated to current context
     *
     * @return __Logger
     */
    public function &getLogger($appender = null) {
        return __LogManager::getInstance()->getLogger($this->_context_id, $appender);
    }
    
    /**
     * Get the {@link __ResourceManager} associated to current context
     *
     * @return __ResourceManager
     */
    public function &getResourceManager() {
        return __ResourceManager::getContextInstance($this->_context_id);
    }
    
    /**
     * Get a I18n {@link __Resource}
     *
     * @return __Resource
     */
    public function &getResource($resource_id, $language_iso_code = null) {
        $return_value = null;
        $resource_manager = __ResourceManager::getContextInstance($this->_context_id);
        if($resource_manager instanceof __ResourceManager) {
            $return_value = $resource_manager->getResource($resource_id, $language_iso_code);
        }
        return $return_value;
    }    
    
    /**
     * Gets the context instances factory
     * 
     * @return __InstanceFactory
     */
    public function &getInstanceFactory() {
        if($this->_instance_factory == null) {
            $this->_instance_factory = new __InstanceFactory($this);
        }
        return $this->_instance_factory;
    }
    
    /**
     * Get the current context configuration loader
     *
     * @return __ConfigurationLoader
     */
    public function &getConfigurationLoader() {
        return $this->_configuration_loader;
    }
    
    /**
     * This method is reserved for internal usage by lion.
     * 
     * Do not call
     *
     */
    public function startup() {
        //get context instances:
        $session = $this->getSession();
        if($session->hasData('__Context::_session_scope_instances')) {
            $this->_session_scope_instances = $session->getData('__Context::_session_scope_instances');
            //create REQUEST scope non-lazy instances:
            $this->_createNonLazyInstances($this->_request_scope_instance_definitions);
        }
        else {
            //create all non-lazy instances:
            $this->_createNonLazyInstances($this->_instance_definitions);
            $session->setData('__Context::_session_scope_instances', $this->_session_scope_instances);
        }
    }
    
    /**
     * Executes all the startup method on all the registered instances when applicable
     *
     */
    protected function _executeStartupMethods() {
        foreach( $this->_instance_definitions as $instance_id => $instance_definition ) {
            if( key_exists($instance_id, $this->_session_scope_instances ) ) {
                $instance = $this->_session_scope_instances[$instance_id];
                //Execute startup method if any:
                $startup_method = $instance_definition->getStartupMethod();
                if($startup_method != null && method_exists($instance, $startup_method)) {
                    $instance->$startup_method();
                }
            }
        }
    }
    
    /**
     * Executes all the shutdown method on all the registered instances when applicable
     *
     */
    protected function _executeShutdownMethods() {
        foreach( $this->_instance_definitions as $instance_id => $instance_definition ) {
            if( key_exists($instance_id, $this->_session_scope_instances ) ) {
                $instance = $this->_session_scope_instances[$instance_id];
                //Execute shutdown method if any:
                $shutdown_method = $instance_definition->getShutdownMethod();
                if($shutdown_method != null && method_exists($instance, $shutdown_method)) {
                    $instance->$shutdown_method();
                }
            }
        }
    }    
    
    /**
     * Alias of {@link __Context::getContextId()}
     *
     * @return string the Id for current __Context instance
     */
    public function getId() {
        return $this->getContextId();
    }
    
    /**
     * Returns the Id for current __Context instance
     *
     * @return string The Id for current __Context instance
     */
    public function getContextId() {
        return $this->_context_id;
    }

    /**
     * Gets the root directory where the application represented by the context container is located at
     *
     * @return string
     */
    public function getBaseDir() {
        return $this->_context_base_dir;
    }
    
    public function setUniqCode($uniq_code) {
        $this->_uniq_code = $uniq_code;
    }
    
    public function getUniqCode() {
        return $this->_uniq_code;
    }
    
    protected function _createNonLazyInstances(array $instance_definitions) {
        foreach( $instance_definitions as $instance_id => &$instance_definition ) {
            //Will create just singleton instances that are not lazy:
            if(!$instance_definition->isLazy() && $instance_definition->isSingleton()) {
                if(!key_exists($instance_id, $this->_session_scope_instances)) {
                    $this->_createInstance($instance_id);
                }
            }
        }
        //$this->_executeStartupMethods();                    
    }
    
    public function hasInstance($instance_id){
        $return_value = false;
        if(key_exists($instance_id, $this->_session_scope_instances) || 
           key_exists($instance_id, $this->_request_scope_instances) || 
           key_exists($instance_id, $this->_instance_definitions)) {
            $return_value = true;
        }
        return $return_value;
    }

    public function &getInstanceDefinition($instance_id) {
        $return_value = null;
        if(key_exists($instance_id, $this->_instance_definitions)) {
            $return_value =& $this->_instance_definitions[$instance_id];
        }
        return $return_value;
    }

    /**
     * Get a context instance
     * 
     * @deprecated use getContextInstance instead of
     *
     * @param string $instance_id
     * @return mixed
     */
    public function &getInstance($instance_id) {
        $return_value = null;
        
        if(key_exists($instance_id, $this->_instance_definitions)) {
            $instance_definition = $this->_instance_definitions[$instance_id];
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_ID_NOT_FOUND', array($instance_id));
        }
        
        if(key_exists($instance_id, $this->_session_scope_instances)) {
            $instance =& $this->_session_scope_instances[$instance_id];
        }
        else if(key_exists($instance_id, $this->_request_scope_instances)) {
            $instance =& $this->_request_scope_instances[$instance_id];
        }        
        else {
            $instance = $this->_createInstance($instance_id);
        }
        
        if($instance_definition != null && !$instance_definition->isSingleton()) {
            $return_value = clone($instance);
        }
        else {
            $return_value =& $instance;
        }
        
        return $return_value;
    }
    
    protected function &_createInstance($instance_id) {
        
        $instance_definition = $this->_instance_definitions[$instance_id];
        //check if the requested instance is a resource:
        if(key_exists($instance_id, $this->_instances_requested)) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CIRCULAR_DEPENDENCY_INJECTION', array($instance_id));
        }
        $this->_instances_requested[$instance_id] = true;
        $return_value = $this->getInstanceFactory()->createInstance($instance_definition);
        //do not store non-serializable instances in the instances array:
        $scope = $instance_definition->getScope();
        switch($scope) {
            case __InstanceDefinition::SCOPE_REQUEST:
                $this->_request_scope_instances[$instance_id] =& $return_value;
                break;
            default:
                $this->_session_scope_instances[$instance_id] =& $return_value;
                break;
        }
        unset($this->_instances_requested[$instance_id]);

        return $return_value;
    }
    
    
    /**
     * Alias of getInstance
     * 
     * @param string $instance_id
     * @return mixed
     */
    public function &getContextInstance($instance_id) {
        $return_value = $this->getInstance($instance_id);
        return $return_value;
    }

    public function &getFlowScope() {
        return __FlowExecutor::getInstance()->getActiveFlowExecution();
    }
    
    public function &getRequestScope() {
        $return_value = __FrontController::getInstance()->getRequest();
        return $return_value;
    }
    
    public function &getSessionScope() {
        return $this->getSession();
    }    
    
    /**
     * Adds a __Configuration instance to the current configuration context.
     * It will be merged with the existent __Configuration instance
     *
     * @param __Configuration &$configuration The __Configuration to add to.
     * 
     */
    public function addConfiguration(__Configuration &$configuration) {
        if( !($this->_configuration instanceof __Configuration) ) {
            $this->_configuration =& $configuration;
        }
        else {
            $this->_configuration->merge($configuration);
        }
    }     
    
    /**
     * Get the {@link __Configuration} instance associated to the current context
     *
     * @return __Configuration The configuration instance associated to the current context
     */
    public function &getConfiguration() {
        return $this->_configuration;
    }
    
    /**
     * Retrieves a property content if defined
     *
     * @param string $property_name The name of the property
     * @return string The property content
     */
    public function getPropertyContent($property_name) {
        $return_value = null;
        if($this->_configuration != null) {
            $return_value = $this->_configuration->getPropertyContent($property_name);
        }
        return $return_value;
    }       
   
    /**
     * Check if exists a given property by name
     *
     * @param string $property_name
     * @return bool
     */
    public function hasProperty($property_name) {
        $return_value = false;
        if($this->_configuration != null) {
            $return_value = $this->_configuration->hasProperty($property_name);
        }
        return $return_value;
    }       

}