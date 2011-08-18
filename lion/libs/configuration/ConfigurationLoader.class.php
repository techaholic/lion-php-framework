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
 * This is the class in charge of loading configurations.
 * 
 * The context container has associated a configuration loader in order to load the configuration before start dispatching each request.
 *
 */
class __ConfigurationLoader {
    
    /**
     * The identifier of the associated context
     *
     * @var string
     */
    protected $_context_id = null;
    
    /**
     * stores pair [configuration filename, last-modif timestamp]
     *
     * @var array
     */
    protected $_configuration_files_mtimes = array();
    
    /**
     * stores all the locator expressions used by the current configuration loader
     *
     * @var array
     */
    protected $_configuration_locators = array();
    
    public function __construct($context_id = null) {
        if($context_id == null) {
            $this->_context_id = __CurrentContext::getInstance()->getContextId();
        }
        else if(__ContextManager::getInstance()->hasContext($context_id)) {
            $this->_context_id = $context_id;
        }
        else {
            throw new __ConfigurationException('Context not found for specified identifier: ' . $context_id);
        }
    }
    
    /**
     * Loads the associated context's configuration
     * 
     * @return __Configuration The {@link __Configuration} instance as a result of reading the context configuration file
     */
    public function &loadConfiguration($context_configuration_file = null) {
        $return_value = null;
        $cache = __ApplicationContext::getInstance()->getCache();
        $configuration_info = $cache->getData('configuration_' . $this->_context_id);
        if(is_array($configuration_info)) {
            $return_value = $configuration_info['configuration']; //by default
            //It's important to note that configuration changes are detected on DEBUG_MODE enabled:
            if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
                $configuration_files = $configuration_info['configuration_files'];
                $configuration_locators = $configuration_info['configuration_locators'];
                if($this->_configurationHasChanged($configuration_files, $configuration_locators)) {
                    //clear the session + the cache:
                    __ContextManager::getInstance()->getContext($this->_context_id)->getSession()->clear();
                    __ContextManager::getInstance()->getContext($this->_context_id)->getCache()->clear();
                    $return_value = null;
                }
            }
        }
        if($return_value == null) {
            //read the configuration:
            $this->_configuration_files_mtimes = array();
            $this->_configuration_locators = array();
            if($context_configuration_file == null) {
                $context_configuration_file = __ContextManager::getInstance()->getContext($this->_context_id)->getBaseDir() . DIRECTORY_SEPARATOR . 'config.xml';
            }
            if(is_readable($context_configuration_file) && is_file($context_configuration_file)) {
                $return_value = $this->loadConfigurationFile($context_configuration_file);
                //$return_value = $this->_parseIncludes($return_value, dirname($context_configuration_file));
            }
            else {
                $return_value = $this->createConfiguration();
            }
            $return_value->merge($this->_loadDefaultContextConfiguration());
            //save the new configuration into the cache:
            $configuration_info = array();
            $configuration_info['configuration'] = $return_value;
            $configuration_info['configuration_files'] = $this->_configuration_files_mtimes;
            $configuration_info['configuration_locators'] = $this->_configuration_locators;
            $cache->setData('configuration_' . $this->_context_id, $configuration_info);
        }
        else {
            $configuration_directives = $return_value->getSection('configuration')->getSection('configuration-directives');
            if($configuration_directives != null) {
                $this->_readConfigurationDirectives($configuration_directives);
            }
        }
        return $return_value;
    }
    
    private function _configurationHasChanged(array $configuration_files_mtimes, array $configuration_locators) {
        //check that current included files has not changed:
        foreach($configuration_files_mtimes as $configuration_file => $filemtime) {
            $current_filemtime = @filemtime($configuration_file);
            if($current_filemtime != $filemtime) {
                return true;
            }
        }
        //check that there are not any new file matching any locator expression:
        foreach($configuration_locators as $configuration_locator) {
            $files_to_include = __FileResolver::resolveFiles($configuration_locator);
            foreach($files_to_include as $file_to_include) {
                if(!key_exists($file_to_include, $configuration_files_mtimes)) {
                    return true;
                }
            }
            
        }
        return false;
    }
    
    /**
     * Loads a given configuration content, returning a {@link __Configuration} instance representing it.
     *
     * @param string $configuration_content The content to load to
     * @param string @configuration_file_type The configuration type to parse to (i.e. "XML", "INI", ...)
     * @return __Configuration The loaded {@link __Configuration} instance
     */
    public function &loadConfigurationContent($configuration_content, $configuration_file_type) {
        if(empty($configuration_file_type)) {
            throw new __ConfigurationException('Need to specify the configuration_file_type parameter in order to let the framework know the content type to parse to');
        }
        $return_value = $this->createConfiguration();
        $configuration_storage = __ConfigurationStorageFactory::createConfigurationStorage($configuration_file_type);
        $configuration_storage->parse($configuration_content, $return_value);
        return $return_value;
    }
    
    /**
     * Load an given configuration file, returning a {@link __Configuration} instance representing it.
     *
     * The configuration file can be set as a relative path or absolute path.<br>
     * i.e.<br>
     * - config/controllers.xml (relative to the context root directory)<br>
     * - /var/www/myapp/config.xml (absolute path)<br>
     * 
     * @param string $configuration_file The configurl or physical configuration file
     * @return __Configuration The loaded {@link __Configuration} instance
     */
    public function &loadConfigurationFile($configuration_file) {
        $return_value = null;
        $configuration_file = __PathResolver::resolvePath($configuration_file);
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            $this->_configuration_files_mtimes[$configuration_file] = @filemtime($configuration_file);
        }
        $configuration_file_type = $this->_getConfigurationFileType($configuration_file);
        if(is_readable($configuration_file) && is_file($configuration_file)) {
            $return_value = $this->createConfiguration();
            $configuration_storage = __ConfigurationStorageFactory::createConfigurationStorage($configuration_file_type);
            $configuration_storage->load($configuration_file, $return_value);
            $return_value = $this->_parseIncludes($return_value, dirname($configuration_file));
        }
        else {
            throw new __ConfigurationException('Configuration file "' . $configuration_file . '" not found or not readable.');
        }
        return $return_value;
    }
    
    /**
     * Create an empty {@link __Configuration} instance
     *
     * @return __Configuration
     */
    public function &createConfiguration() {
        $return_value = new __Configuration($this->_context_id);
        return $return_value;
    }
    
    private function &_parseIncludes(__Configuration &$configuration, $basedir) {
        $configuration_section = $configuration->getSection('configuration');
        if($configuration_section != null) {
            //read configuration directives before read other sections:
            $configuration_directives = $configuration_section->getSection('configuration-directives');
            if($configuration_directives != null) {
                $this->_readConfigurationDirectives($configuration_directives);
            }
            //read the other sections:
            $sections = $configuration_section->getSections();
            foreach($sections as $section) {
                switch (strtoupper($section->getName())) {
                    case 'INCLUDE':
                        $expression = $section->getProperty('#text')->getContent();
                        //finally, resolve the path:
                        $expression = __PathResolver::resolvePath($expression, $basedir);
                        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE') &&
                          (strpos($expression, '...') !== false || strpos($expression, '*') !== false)) {
                            $this->_configuration_locators[$expression] = $expression;
                        }
                        $files_to_include = __FileResolver::resolveFiles($expression);
                        foreach($files_to_include as $file_to_include) {
                            $included_configuration = $this->loadConfigurationFile($file_to_include);
                            $configuration->merge($included_configuration);
                            unset($included_configuration);
                        }
                        break;
                }
            }
        }
        return $configuration;
    }
    
    private function _readConfigurationDirectives(__ConfigurationSection &$section) {
        $configuration_directives =& $section->getSections();
        foreach($configuration_directives as &$configuration_directive) {
            switch (strtoupper($configuration_directive->getName())) {
                case 'SECTION-HANDLER':
                    $this->_processSectionHandler($configuration_directive);
                    break;
                case 'AUTOLOAD':
                    $this->_processAutoload($configuration_directive);
                    break;
            }
        }
    }
    
    private function _processSectionHandler($section_handler) {
        __SectionHandlerFactory::getInstance()->registerSectionHandlerClass($section_handler->getAttribute('name'), $section_handler->getAttribute('handler-class'));
    }
  
    private function _processAutoload($section_handler) {
        if($section_handler->hasAttribute('class') && $section_handler->hasAttribute('method')) {
            spl_autoload_register(array($section_handler->getAttribute('class'), $section_handler->getAttribute('method')));
        }
        else {
            throw new __ConfigurationException('Missing information to register the autoload method. It was expected a class and a method attribute.');
        }
    }     
    
    private function &_loadDefaultContextConfiguration() {
        $return_value = $this->createConfiguration();
        $default_configuration_dir = DEFAULT_CONFIGURATION_DIR;
        $default_configuration_files = dir($default_configuration_dir);
        while (false !== ($default_configuration_file = $default_configuration_files->read())) {
            $configuration_filename = $default_configuration_dir . DIRECTORY_SEPARATOR . $default_configuration_file;
            if( is_readable($configuration_filename) && is_file($configuration_filename) ) {
                $return_value->merge( $this->loadConfigurationFile($configuration_filename) );
            }
        }
        return $return_value;        
    }    
    
    private function _getConfigurationFileType($configuration_file_name) {
        $return_value = null;
        $configuration_file_info = pathinfo($configuration_file_name);
        $extension = $configuration_file_info['extension'];
        switch (strtoupper($extension)) {
            case 'XML':
                $return_value = CONFIGURATION_TYPE_XML;
                break;
            case 'INI':
            default:
                $return_value = CONFIGURATION_TYPE_INI;
                break;
        }
        return $return_value;
    }             
    
}

