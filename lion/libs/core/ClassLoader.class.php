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
 * @package    Core
 * 
 */

spl_autoload_register(array(__ClassLoader::getInstance(), 'loadClassFile'));

/**
 * The class loader has the relationship between classes and the .php files where they are defined in.
 * 
 * @author aparraga
 *
 */
class __ClassLoader {

    static private $_instance = null;
    private $_class_file_locators   = array();
    private $_mapping = array();

    private function __construct() {
    }

    /**
     * This method return a singleton instance of __ClassLoader
     *
     * @return __ClassLoader A singleton reference to the __ClassLoader
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ClassLoader();
        }
        return self::$_instance;
    }

    public function addClassFileLocator(__ClassFileLocator $class_file_locator) {
        array_unshift($this->_class_file_locators, $class_file_locator);
        $this->_mapping = array_merge($this->_mapping, $class_file_locator->getMapping());
        $class_file_locator->registerAutoloaders();
    }


    /**
     * Load a class definition file associated with a class name.
     *
     * @param string $class_name The class name to load his class definition file
     * @return true if the class file has been loaded correctly, else false
     */
    public function loadClassFile($class_name) {
        $class_name = strtoupper($class_name);
        //$class_file = $this->getClassFile($class_name);
        if(key_exists($class_name, $this->_mapping)) {
            $class_file = $this->_mapping[$class_name];
            include_once($class_file);
            $return_value = true;
        }
        else {
            $return_value = false;
        }
        return $return_value;
    }

    public function getClassFile($class_name) {
        foreach($this->_class_file_locators as $class_file_locator) {
            $class_file = $class_file_locator->getClassFile($class_name);
            if( $class_file != null ) {
                return $class_file;
            }
        }
        return null;
    }

}

/**
 * This is the class in charge to locate a class definition file and return the complete rute to access to it.
 * 
 * __ClassLoader delegates to this class the .php file searching.
 *
 */
class __ClassFileLocator {

    const INCLUDE_PATH_FILENAME = 'includepath.xml';
    
    private $_metadata = null;
    private $_mapping = null;
    private $_autoloaders = null;
    private $_base_dir = null;
    private $_include_dir = null;
    private $_id = null;

    /**
     * This is the constructor of the class.
     * It search for all includepath.xml files recursivelly to load to
     */
    public function __construct($base_dir) {
        $this->_id = MD5($base_dir);
        $this->_base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR);
        $debug_mode = __Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE');
        if(!$debug_mode) {
            $this->_metadata = __CacheManager::getInstance()->getCache()->getData('classloader_' . $this->_id);
        }
        if($this->_metadata == null) {
            $this->_metadata = $this->_loadMetadata();
            if(!$debug_mode) {
                __CacheManager::getInstance()->getCache()->setData('classloader_' . $this->_id, $this->_metadata);
            }
        }
        $this->_mapping = $this->_metadata['mapping'];
        $this->_autoloaders = $this->_metadata['autoloaders'];
    }
    
    public function registerAutoloaders() {
        if(count($this->_autoloaders) > 0) {
            foreach($this->_autoloaders as $class => $method) {
                spl_autoload_register(array($class, $method));
            }
        }
    }

    public function hasClassFile($class_name) {
        return key_exists(strtoupper($class_name), $this->_mapping);
    }

    public function getMapping() {
        return $this->_mapping;
    }
    
    /**
     * This function will return an array with all necessaries files to include to load a class
     *
     * @param string $ The name of the class to load
     * @return array An array with all necessaries files to include
     */
    public function getClassFile($class_name)
    {
        $return_value = null;
        $class_name = strtoupper($class_name);
        if (key_exists($class_name, $this->_mapping)) {
            $return_value = $this->_mapping[$class_name];
        }
        return $return_value;
    }

    /**
     * Load the metadata xml specification
     *
     * @param string The base path to read the xml file from
     * @return boolean (True if all ok, False in other case)
     */
    private function _loadMetadata()
    {
        $return_value = array('mapping' => array(),
                              'autoloaders' => array()); #by default
        //Find any include path files recursively, under the base directory's lib directories
        if(is_dir($this->_base_dir . DIRECTORY_SEPARATOR . 'libs')) {
            $includepath_files = __FileResolver::
                                 resolveFiles($this->_base_dir . DIRECTORY_SEPARATOR . 
                                              'libs' . DIRECTORY_SEPARATOR . 
                                              '...' . DIRECTORY_SEPARATOR . self::INCLUDE_PATH_FILENAME);
        }
        else {
            $includepath_files = array();
        }
		//Do an explicit check in the baseDir/config directory
        if(is_file($this->_base_dir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . self::INCLUDE_PATH_FILENAME)) {
            $includepath_files[] = $this->_base_dir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . self::INCLUDE_PATH_FILENAME;
        }
        //Go through each include file that we found, and parse the information.
        foreach ($includepath_files as $includepath_file) {
            libxml_use_internal_errors(true);
            $content = file_get_contents($includepath_file);
            $dom = new DomDocument("1.0");
            $dom->loadXml($content);
            if( $dom->documentElement != null )  {
                foreach ($dom->documentElement->childNodes as $child) {
                    if ($child->nodeName == 'cluster') {
                        if(substr($child->getAttribute('path'), 0, 1) == '/') {
                            $current_dir = $this->_base_dir . DIRECTORY_SEPARATOR;
                        }
                        else {
                            $current_dir = rtrim(dirname($includepath_file), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                        }
                        $path = trim($child->getAttribute('path'));
                        $path = preg_replace('/\/\.\.\.$/', '', $path, 1, $count);
                        if($count == 1) {
                            $recursive = true;
                        }
                        else {
                            $recursive = false;
                        }
                        $current_dir .= ltrim($path, '/');
                        foreach ($child->childNodes as $class_child) {
                            if ($class_child->nodeName == 'class' || $class_child->nodeName == 'interface') {
                                if(strpos($class_child->getAttribute('name'), '*') !== false) {
                                    $class_data = $this->_loadMetaClassesFromMappingRule($class_child, $current_dir, $recursive);
                                    $return_value['mapping'] = array_merge($return_value['mapping'], $class_data);
                                }
                                else {
                                    $class_data = $this->_loadMetaClass($class_child, $current_dir);
                                    $return_value['mapping'][strtoupper($class_child->getAttribute('name'))] = $class_data;
                                }
                            }
                        }
                    }
                    else if($child->nodeName == 'autoload') {
                        if($child->hasAttribute('class') && $child->hasAttribute('method')) {
                            $return_value['autoloaders'][$child->getAttribute('class')] = $child->getAttribute('method');
                        }
                        else {
                            throw new Exception('Missing information to register the autoload method. It was expected a class and a method attribute.');
                        }
                    }
                }
            }
            else {
                /**
                 * @todo extract error loading classes location by ussing libxml_get_errors()
                 */
                throw new Exception("Error parsing includepath file: " . $includepath_file);
            }
        }
        return $return_value;
    }
    
    private function _loadMetaClass($child, $current_dir)
    {
        $return_value = null;
        if($child->hasAttribute('file')) {
            $return_value = $current_dir . DIRECTORY_SEPARATOR . $child->getAttribute('file');
        }
        return $return_value;
    }

    private function _loadMetaClassesFromMappingRule($child, $current_dir, $recursive = false) {
        $return_value = array();
        if($child->hasAttribute('file')) {
            $file_pattern  = $child->getAttribute('file');
            $class_pattern = $child->getAttribute('name');
            if(is_readable($current_dir) && is_dir($current_dir)) {
                $dir = dir($current_dir);
                while (false !== ($class_file = $dir->read())) {
                    $current_file = $current_dir . DIRECTORY_SEPARATOR . $class_file;
                	$position_of_dot = strpos($class_file, ".");
                	//check if current file is valid to be include
                	//also exclude files like .htaccess
                	if(is_readable($current_file) && $position_of_dot !== 0) {
                        if(is_file($current_file)) {
                            $class_name_matched = array();
                            if(preg_match('/^' . str_replace('*', '(.+?)', $file_pattern) . '$/', $class_file, $class_name_matched)) {
                                $return_value[strtoupper(str_replace('*', $class_name_matched[1], $class_pattern))] = $current_file;
                            }
                        }
                        else if(is_dir($current_file) && $recursive) {
                            $return_value = array_merge($this->_loadMetaClassesFromMappingRule($child, $current_file, true), $return_value);
                        }
                    }
                }
            }
            else {
                throw new Exception('The directory ' . $current_dir . ' specified in the includepath does not exists or is not readable.');
            }

        }
        return $return_value;

    }

}

