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
 * @package    Exception
 * 
 */

class __ExceptionFactory {
    
    static private $_instance = null;
    private $_error_table = null;
    
    /**
     * Constructor method
     */
    private function __construct()
    {
        $configuration = __ContextManager::getInstance()->getCurrentContext()->getConfiguration();
        if($configuration != null) {
            $configuration_section = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration');
            if($configuration_section != null) {
                $this->_error_table = $configuration_section->getSection('errors');
            }
        }
    }
    
    /**
     * This method return a singleton instance of __ExceptionFactory
     *
     * @return __ExceptionFactory A singleton reference to the __ExceptionFactory
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ExceptionFactory();
        }
        return self::$_instance;
    }
    
    public function &getErrorTable() {
        return $this->_error_table;
    }    
    
    /**
     * Creates a new __LionException children instance (depending on the error_id parameter).
     *
     * @param string $error_id
     * @param mixed $parameters Error message parameters
     * @return __LionException
     */
    public function &createException($error_id, $parameters = array()) {
        if(!is_array($parameters)) {
            $parameters = array($parameters);
        }
        if($this->_error_table != null) {
            if(is_numeric($error_id)) {
                $error_code = $error_id;
                $error_id   = $this->_error_table->getErrorId($error_code);
            }
            else {
                $error_code = $this->_error_table->getErrorCode($error_id);
            }
            if($error_code != null) {
                $exception_class = $this->_error_table->getExceptionClass($error_code);
                $error_message   = $this->_error_table->getErrorMessage($error_code, $parameters);
                $return_value = new $exception_class($error_message, $error_code);
            }
            else {
                $error_code = 0;
                if(__ResourceManager::getInstance()->hasResource($error_id)) {
                    $error_message = __ResourceManager::getInstance()->getResource($error_id)->setParameters($parameters)->getValue();
                }
                else {
                    $error_message = $error_id;                    
                }
                $return_value  = new __UnknowException($error_message, $error_code);
            }
        }
        else {
            $return_value = new __UnknowException($error_id, 0);
        }
        if( $return_value instanceof __LionException ) {
            $return_value->setErrorMessageResourceId($error_id);
            $return_value->setErrorMessageParameters($parameters);
            if( $this->_error_table != null ) {
                $return_value->setErrorTitle( $this->_error_table->getErrorTitle($error_code) );
            }
        }
        return $return_value;
    }
    
}