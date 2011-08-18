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
 * @package    I18n
 * 
 */


/**
 * This is the class in charge of manage I18n resources, being text, error messages, images an so on.
 * 
 * The application context has his own resource manager, which can be retrieved by calling the {@link __Context::getResourceManager()} method
 * 
 * <code>
 * //Retrieve the __ResourceManager instance associated to the application context:
 * $resource_manager = __ApplicationContext::getInstance()->getResourceManager();
 * 
 * </code>
 * <br>
 */
class __ResourceManager {
    
    private $_context_id = null;
    
    static private $_instance = null;
    
    static private $_context_instances = array();
    
    /**
     * This is the hash that will store all {@link __ResourceProviders} instances handled by the __ResourceManager
     *
     * @var array
     */
    private $_resource_providers = array( PERSISTENCE_LEVEL_SESSION => array(),
                                          PERSISTENCE_LEVEL_ACTION  => array() );
    
    private $_supported_languages = array( );
                                           
    /**
     * This is the table where all the resources will be stored into
     *
     * @var unknown_type
     */
    private $_resource_table = null;

    /**
     * This is the constructor method. It will load all resource providers used by the __ResourceManager to serve Resources
     */
    private function __construct($context_id = null) {
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }
        $this->_context_id = $context_id;
        $context = __ContextManager::getInstance()->getContext($context_id);
        $session = $context->getSession();
        $this->_resource_table = $session->getData('__ResourceManager::_resource_table');
        if($this->_resource_table == null) {        
            //initialize the resource table:
            $this->_resource_table = new __ResourceTable();
            //load resource dictionaries:
            $resource_providers = $context->getConfiguration()->getSection('configuration')->getSection('resource-providers');
            if(is_array($resource_providers)) {
                $this->_resource_providers = &$resource_providers;
            }
            //load supported languages:
            $supported_languages = $context->getConfiguration()->getSection('configuration')->getSection('supported-languages');
            if(is_array($supported_languages )) {
                $this->_supported_languages = &$supported_languages;
            }
            else {
                $this->_supported_languages = array( $context->getPropertyContent('DEFAULT_LANG_ISO_CODE') );
            }
            //store in session all variables:
            $session->setData('__ResourceManager::_resource_table',      $this->_resource_table);
            $session->setData('__ResourceManager::_resource_providers',  $this->_resource_providers);
            $session->setData('__ResourceManager::_supported_languages', $this->_supported_languages);
        }
        else {
            $this->_resource_providers  = $session->getData('__ResourceManager::_resource_providers');
            $this->_supported_languages = $session->getData('__ResourceManager::_supported_languages');
        }
        $this->_unloadActionResources();
    }
    
    /**
     * Maintained for back-compatibility, but deprecated.
     * Use {@link __Context::getResourceManager()} method instead of
     *
     * @return __ResourceManager
     * @deprecated Use {@link __Context::getResourceManager()} method instead of
     */
    static public function &getInstance() {
        $return_value = __CurrentContext::getInstance()->getResourceManager();
        return $return_value;
    }
    
    /**
     * This method return a singleton instance of __ResourceManager associated to a context id
     *
     * @return __ResourceManager
     */
    static public function &getContextInstance($context_id) {
        if (!key_exists($context_id, self::$_context_instances)) {
            // Use "Lazy initialization"
            self::$_context_instances[$context_id] = new __ResourceManager($context_id);
        }
        return self::$_context_instances[$context_id];
    }

    public function addSupportedLanguage($language_iso_code) {
        if(!in_array($language_iso_code, $this->_supported_languages)) {
            $this->_supported_languages[] = $language_iso_code;
        }
    }
    
    public function isSupportedLanguage($language_iso_code) {
        return in_array($language_iso_code, $this->_supported_languages);
    }
    
    public function loadActionResources(__ActionIdentity $action_identity, $language_iso_code = null) {
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLastLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            $this->loadResources($language_iso_code);
        }
        $action_resources = array();
        foreach($this->_resource_providers[PERSISTENCE_LEVEL_ACTION] as &$resource_provider) {
            $resources = $resource_provider->loadResources($language_iso_code, $action_identity);
            $action_resources = array_merge($action_resources, $resources);
            unset($resources);
        }
        $this->_resource_table->addActionResources($action_resources, $action_identity, $language_iso_code);
        return $action_resources;
    }
    
    private function _unloadActionResources() {
        $this->_resource_table->unloadActionResources();
    }

    /**
     * This method loads all the session level resources for an specific language.
     * Note that action specific resources are loaded dinamically by each resource dictionary
     *
     * @param string The language iso code to load resources from
     */
    private function loadResources($language_iso_code = null) {
        $context = __ContextManager::getInstance()->getContext($this->_context_id);
        $cache_id = $this->_context_id . '_resources::' . $language_iso_code;
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLastLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            $resources = $context->getCache()->getData($cache_id);
            if($resources == null) {
                $resources = array();
                //Now will iterate throught the Resources, appending all in the return array
                foreach($this->_resource_providers[PERSISTENCE_LEVEL_SESSION] as &$resource_provider) {
                    $resources = array_merge($resources, $resource_provider->loadResources($language_iso_code));
                }
                $context->getCache()->setData($cache_id, $resources);
            }
            $this->_resource_table->addResources($resources, $language_iso_code);
        }
    }
    
    /**
     * This method append a new {@link __ResourceDictionary} instance.
     * __ResourceDictionary are objects that group Resources
     *
     * @param __ResourceDictionary The resource dictionary to add to
     */
    public function addResourceProvider(__ResourceProvider &$resource_provider) {
        $persistence_level = $resource_provider->getPersistenceLevel();
        if(!in_array($resource_provider, $this->_resource_providers[$persistence_level])) {
            $this->_resource_providers[$persistence_level][] =& $resource_provider;
        }
    }
       
    /**
     * This method will returns a resource identified by a key.
     * It will ask to all {@link __ResourceDictionary} instances if any of them has the resource, and will return it if it's found.
     *
     * @param string The resource's key
     * 
     * @return __ResourceBase The requested resource or null if it's not found.
     * 
     */
    public function getResource($resource_key, $language_iso_code = null) {
        $return_value = null;
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLastLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            $this->loadResources($language_iso_code);
        }
        if($this->_resource_table->hasResource($resource_key, $language_iso_code)) {
            $return_value = $this->_resource_table->getResource($resource_key, $language_iso_code);
        }
        else {
            $default_resource_class = __ContextManager::getInstance()->getContext($this->_context_id)->getPropertyContent('DEFAULT_RESOURCE_CLASS');
            $return_value = new $default_resource_class();
            $return_value->setKey($resource_key);
            $return_value->setValue("???" . $resource_key . "???");            
        }
        return $return_value;
    }

    public function hasResource($resource_key, $language_iso_code = null) {
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLastLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            $this->loadResources($language_iso_code);
        }
        return $this->_resource_table->hasResource($resource_key, $language_iso_code);
    }
    
    public function addResource(__ResourceBase &$resource, $language_iso_code = null) {
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLastLocale()->getLanguageIsoCode();
        }
        $this->_resource_table->addResource($resource, $language_iso_code);
    }
    
    public function removeResource($resource_id) {
        $this->_resource_table->removeResource($resource_id);
    }
    
}



