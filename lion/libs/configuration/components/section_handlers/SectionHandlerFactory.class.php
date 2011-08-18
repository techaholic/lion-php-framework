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
 * This class is the factory for section handlers.
 * 
 * When the configuration is requested for a given section, it ask the section handler factory to get a section handler for that section.
 * If the factory returns a section handler, the configuration will delegate on it to get the configuration processed, otherwise it will return the configuration section as it.
 *
 */
final class __SectionHandlerFactory {
    
    private static $_instance = null;
    
    private $_section_handler_classes = array();
    
    private function __construct() {
        //Set predefined configuration sections:
        $this->registerSectionHandlerClass('STREAM-WRAPPERS',         '__StreamWrappersSectionHandler');
        $this->registerSectionHandlerClass('UI-COMPONENT-TAGS',       '__UIComponentTagsSectionHandler');
        $this->registerSectionHandlerClass('PERMISSION-DEFINITIONS',  '__PermissionDefinitionsSectionHandler');
        $this->registerSectionHandlerClass('ERRORS',                  '__ErrorCodesSectionHandler');
        $this->registerSectionHandlerClass('CONTEXT-INSTANCES',       '__InstancesSectionHandler');
        $this->registerSectionHandlerClass('ROLE-DEFINITIONS',        '__RoleDefinitionsSectionHandler');
        $this->registerSectionHandlerClass('ROUTES',                  '__RoutesSectionHandler');
        $this->registerSectionHandlerClass('FILTERS',                 '__FiltersSectionHandler');
        $this->registerSectionHandlerClass('CONTROLLER-DEFINITIONS',  '__ControllerDefinitionsSectionHandler');
        $this->registerSectionHandlerClass('VIEW-DEFINITIONS',        '__ViewDefinitionsSectionHandler');
        $this->registerSectionHandlerClass('RESOURCE-PROVIDERS',      '__ResourceProvidersSectionHandler');
        $this->registerSectionHandlerClass('SUPPORTED-LANGUAGES',     '__SupportedLanguagesSectionHandler');
        $this->registerSectionHandlerClass('MODEL-SERVICES',          '__ModelServicesSectionHandler');
        $this->registerSectionHandlerClass('WEBFLOW',                 '__WebFlowSectionHandler');
    }
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __SectionHandlerFactory();
        }
        return self::$_instance;
    }
    
    public function registerSectionHandlerClass($section_name, $section_class) {
        $section_name = strtoupper($section_name);
        if(class_exists($section_class)) {
            $this->_section_handler_classes[$section_name] = $section_class;
        }
        else {
            throw new __ConfigurationException("Section handler class '$section_class' not found for section '$section_name'");
        }
    }
    
    public function unregisterSectionHandlerClass($section_name) {
        $section_name = strtoupper($section_name);
        if(key_exists($section_name, $this->_section_handler_classes)) {
            unset($this->_section_handler_classes[$section_name]);
        }
    }
    
    public function createSectionHandler($section_name) {
        $return_value = null;
        $section_name = strtoupper($section_name);
        if(key_exists($section_name, $this->_section_handler_classes)) {
            $section_handler_class = $this->_section_handler_classes[$section_name];
            $return_value = new $section_handler_class();
            if( !($return_value instanceof __ISectionHandler) ) {
                throw new __ConfigurationException("Wrong class '$section_handler_class' for handle section '$section_name'. A section handler must implement __ISectionHandler.");
            }
        }
        return $return_value;
    }

    
    
}