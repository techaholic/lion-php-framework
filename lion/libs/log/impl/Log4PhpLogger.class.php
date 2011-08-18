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

if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', LION_DIR . '/libs/thrdparty/log4php');

class __Log4PhpLogger implements __ILogger {
    
    private $_log4php_instance = null;
    
    const DEFAULT_CONFIGURATION_TYPE = 'PROPERTIES';
    
    public function __construct($log_id) {
        $this->_loadLog4PhpConf($log_id);
    }
    
    private function _loadLog4PhpConf($log_id) {
        $lion_runtime_directives = __Lion::getInstance()->getRuntimeDirectives();
        if(!defined('LOG4PHP_CONFIGURATION') && 
           $lion_runtime_directives->hasDirective('LOG4PHP_CONFIG_FILE')) {
            $log4php_config_file = __PathResolver::resolvePath($lion_runtime_directives->getDirective('LOG4PHP_CONFIG_FILE'));
            define('LOG4PHP_CONFIGURATION', $log4php_config_file);
        }
        if(!defined('LOG4PHP_CONFIGURATOR_CLASS')) {
            if($lion_runtime_directives->hasDirective('LOG4PHP_CONFIGURATION_TYPE')) {
                $configuration_type = $lion_runtime_directives->getDirective('LOG4PHP_CONFIGURATION_TYPE');
            }
            else {
                $configuration_type = __Log4PhpLogger::DEFAULT_CONFIGURATION_TYPE;
            }
            switch(strtoupper($configuration_type)) {
                case 'XML':
                    define('LOG4PHP_CONFIGURATOR_CLASS', LOG4PHP_DIR.'/xml/LoggerDOMConfigurator');
                    break;
            }
        }
        $this->_log4php_instance = LoggerManager::getLogger($log_id);
    }
    
    public function debug($string) {
        $this->_log4php_instance->debug($string);
    }

    public function info($string) {
        $this->_log4php_instance->info($string);
    }
    
    public function warn($string) {
        $this->_log4php_instance->warn($string);
    }

    public function error($string) {
        $this->_log4php_instance->error($string);
    }
    
    public function fatal($string) {
        $this->_log4php_instance->fatal($string);
    }
    
}
