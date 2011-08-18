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
 * @package    Log
 * 
 */

class __LogManager {
    
    private static $_instance = null;
    private $_loggers = array();
    
    const DEFAULT_LOGGER_CLASS = '__DummyLogger';
    const DEFAULT_LOG_ID = 'default';
    const DEFAULT_APPENDER = 'default';
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __LogManager();
        }
        return self::$_instance;
    }
    
    public function &getLogger($log_id = null, $appender = null) {
        if(empty($log_id)) {
            $log_id = __LogManager::DEFAULT_LOG_ID;
        }
        if(!key_exists($log_id, $this->_loggers)) {
            $this->_loggers[$log_id] = $this->_createLoggerInstance($log_id);
        }
        return $this->_loggers[$log_id];
    }
    
    private function &_createLoggerInstance($log_id) {
        $logger_class = __LogManager::DEFAULT_LOGGER_CLASS; //by default
        $lion_runtime_directives = __Lion::getInstance()->getRuntimeDirectives();
        if($lion_runtime_directives->hasDirective('LOG_ENABLED') && 
           $lion_runtime_directives->getDirective('LOG_ENABLED')) {
            if($lion_runtime_directives->hasDirective('LOGGER_CLASS')) {
                $logger_class = $lion_runtime_directives->getDirective('LOGGER_CLASS');            
            }        
        }
        if(class_exists($logger_class)) {
            $logger = new $logger_class($log_id);
        }
        else {
            throw new __ConfigurationException('Class not found: ' . $logger_class);
        }
        return $logger;
    }
    
}