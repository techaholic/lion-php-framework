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

/**
 * This is the base class for all LION exceptions.
 * 
 */
abstract class __LionException extends Exception
{
    /**
     * This is the exception type associated to the current exception.
     * How the exception will be handled by LION will depend on the exception type.
     *
     * @var integer
     */
    protected $_exception_type = __ExceptionType::CRITICAL;

    protected $_extra_info = null;
    
    protected $_resource_id = null;
    
    protected $_exception_title = null;
    
    protected $_error_message_parameters = array();
    
    /**
     * The __LionException constructor.
     * 
     * 
     * 
     */
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
    }

    public function setExtraInfo($extra_info) {
        $this->_extra_info = $extra_info;
    }
    
    public function setErrorTitle($exception_title) {
        $this->_exception_title = $exception_title;
    }
    
    public function getErrorTitle() {
        $return_value = 'Core Error';
        if($this->_exception_title != null) {
            $return_value = $this->_exception_title;
        }
        return $return_value;
    }

    public function getExtraInfo() {
        return $this->_extra_info;
    }
    
    /**
     * All custom exception will have the getExceptionType method in order to clasify all exceptions
     * By default, the error will be an ERR_CRITICAL
     * 
     * @return constant The exception error type (ERR_CRITICAL, ERR_WARNING, etc...)
     */
    public function getExceptionType() {
        return $this->_exception_type;
    }

    public function getLocalizedMessage($language_iso_code) {
        if($this->_resource_id != null) {
            $resource_manager = __ResourceManager::getInstance();
            if($resource_manager->hasResource($this->_resource_id, $language_iso_code)) {
                $return_value = __ResourceManager::getInstance()->getResource($this->_resource_id, $language_iso_code)->setParameters($this->_error_message_parameters)->getValue();
            }
            else {
                $return_value = $this->_resource_id;
            }
        }
        else {
            $return_value = parent::getMessage();
        }
        return $return_value;
    }
    
    /**
     * This method will be called for all __LionException deriveds instances in order to execute custom actions depending the exception type
     */
    public function executeCustomAction() { return null; }

    public function getExtendedTrace() {
        return new __ExtendedTrace($this);
    }
    
    public function getLogLevel() {
        return __LogLevel::ERROR;
    }
    
    public function setErrorMessageResourceId($resource_id) {
        $this->_resource_id = $resource_id;
    }
    
    public function getErrorMessageResourceId() {
        return $this->_resource_id;
    }
    
    public function setErrorMessageParameters(array $error_message_parameters) {
        $this->_error_message_parameters = $error_message_parameters;
    }
    
    public function getErrorMessageParameters() {
        return $this->_error_message_parameters;
    }
    
}
