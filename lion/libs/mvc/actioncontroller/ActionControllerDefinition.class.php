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
 * @package    ActionController
 * 
 */


class __ActionControllerDefinition extends __SystemResourceDefinition {
    
    private $_code  = null;
    private $_class = null;
    private $_is_historiable = true;
    private $_is_requestable = true;
    private $_valid_request_method = REQMETHOD_ALL;
    private $_require_ssl    = false;
    private $_I18n_resource_groups = array();
    
    public function setCode($code) {
        $this->_code = $code;
    }
    
    public function getCode() {
        return $this->_code;
    }
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function getClass() {
        return $this->_class;
    }
    
    public function setHistoriable($is_historiable) {
        $this->_is_historiable = (bool) $is_historiable;
    }
    
    public function isHistoriable() {
        return $this->_is_historiable;
    }
    
    public function setRequestable($is_requestable) {
        $this->_is_requestable = (bool) $is_requestable;
    }
    
    public function isRequestable() {
        return $this->_is_requestable;
    }
    
    public function setValidRequestMethod($valid_request_method) {
        $this->_valid_request_method = $valid_request_method;
    }

    public function getValidRequestMethod()
    {
        return $this->_valid_request_method;
    }
        
    public function setRequireSsl($require_ssl) {
        $this->_require_ssl = (bool) $require_ssl;
    }
    
    public function requireSsl() {
        return $this->_require_ssl;
    }
    
    public function setI18nResourceGroups(array $I18n_resource_groups) {
        $this->_I18n_resource_groups = $I18n_resource_groups;
    }
    
    public function getI18nResourceGroups() {
        return $this->_I18n_resource_groups;
    }
    
    public function isValidForControllerCode($controller_code) {
        $return_value = false;
        if(strpos($this->_code, '*') !== false) {
            if(preg_match('/' . str_replace('*', '(.+?)', $this->_code) . '/i', $controller_code)) {
                $return_value = true;
            }
        }
        else if($this->_code == $controller_code) {
            $return_value = true;
        }
        return $return_value;        
    }
    
    public function &getActionController($controller_code = null) {
        $return_value = null;
        $controller_code_substring = null;
        if(strpos($this->_code, '*') !== false) {
            if($controller_code != null) {
                $controller_code_substring_array = array();
                if(preg_match('/^' . str_replace('*', '(.+?)', $this->_code) . '$/i', $controller_code, $controller_code_substring_array)) {
                    $controller_code_substring = $controller_code_substring_array[1];
                }
                else {
                    return null;
                }
            }
        }
        $controller_class_name = $this->getClass();
        if($controller_code_substring != null) {
            $controller_class_name = str_replace('*', $controller_code_substring, $controller_class_name);
            if(!class_exists($controller_class_name)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_CONTROLLER', array($controller_code));
            }
        }
        if(class_exists($controller_class_name)) {
            $return_value = new $controller_class_name();
            if(! $return_value instanceof __IActionController ) {
                throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_CONTROLLER_CLASS', array(get_class($return_value)));
            }
            $return_value->setCode($controller_code ? $controller_code : $this->_code);
            $return_value->setHistoriable($this->isHistoriable());
            $return_value->setValidRequestMethod($this->getValidRequestMethod());
            $return_value->setRequestable($this->isRequestable());
            $return_value->setRequireSsl($this->requireSsl());
            $return_value->setI18nResourceGroups($this->getI18nResourceGroups());
            if($this->getRequiredPermissionId() != null) {
                $required_permission = __PermissionManager::getInstance()->getPermission($this->getRequiredPermissionId());
                $return_value->setRequiredPermission($required_permission);
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_CLASS_NOT_FOUND', array($controller_class_name));
        }
        return $return_value;
    }    
        
}