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

/**
 * Runtime directives are the most basic settings in which the framework works with.
 *
 */
final class __RuntimeDirectives {
    
    private $_directives = array();
    const DIRECTIVES_FILENAME = 'lion.ini';
    
    public function __construct() {
        $this->_loadDirectives();
    }
    
    private function _loadDirectives() {
        if(defined('DIRECTIVES_PATH')) {
            $lion_ini_file = DIRECTIVES_PATH . DIRECTORY_SEPARATOR . self::DIRECTIVES_FILENAME;
            if(file_exists($lion_ini_file)) {
                $this->_directives = parse_ini_file($lion_ini_file);
            }
            else {
                throw new Exception('Can not find lion.ini file on ' . DIRECTIVES_PATH . ' (defined as DIRECTIVES_PATH constant)');
            }
        }
        else {
            $directive_search_paths = array(APP_DIR, DEFAULT_CONFIGURATION_DIR);
            
            foreach($directive_search_paths as $directive_search_path) {
                $lion_ini_file = $directive_search_path . DIRECTORY_SEPARATOR . self::DIRECTIVES_FILENAME;
                if(file_exists($lion_ini_file)) {
                    $this->_directives = parse_ini_file($lion_ini_file);
                    return;
                }
            }
            
            throw new Exception('Can not find lion.ini (search paths: ' . implode(', ', $directive_search_paths) . ')');
        }
            
    }
    
    public function getDirective($key) {
        $return_value = null;
        if(key_exists($key, $this->_directives)) {
            $return_value = $this->_directives[$key];
        }
        return $return_value;
    }
    
    public function getDirectives() {
        return $this->_directives;
    }
    
    public function hasDirective($key) {
        return key_exists($key, $this->_directives);
    }
    
    public function getCacheDirectory() {
        $cache_dir = $this->getDirective('CACHE_FILE_DIR') . DIRECTORY_SEPARATOR;
        if( preg_match( '/^\//', $cache_dir ) || preg_match('/^\w+:/', $cache_dir)) {
            $return_value = $cache_dir;
        }
        else {
            $return_value = APP_DIR . DIRECTORY_SEPARATOR . $cache_dir;
        }
        return $return_value;
    }
        
}