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

class __ErrorTable {
    
    private $_error_codes = array();
    private $_error_ids   = array();
    private $_error_groups = array();
    private $_exception_classes = array();
    
    /**
     * Register the exception that should be used for errors in an error group
     *
     * @param string $group_id The group identifier
     * @param string $exception_class The class name of an {@link __LionException} subclass
     */
    public function registerExceptionClass($group_id, $exception_class) {
        $this->_exception_classes[$group_id] = $exception_class;
    }
    
    /**
     * Register an error code, the associated error id and (optionally) the group that belong to
     *
     * @param integer $error_code The error code (i.e. 55710)
     * @param string $error_id The error id (i.e. 'ERR_CLASS_NOT_FOUND')
     * @param string $group_id The group id that the error id belong to (i.e. 'ERR_GROUP_CORE')
     */
    public function registerErrorCode($error_code, $error_id, $group_id = null) {
        $this->_error_codes[$error_id] = $error_code;
        $this->_error_ids[$error_code] = $error_id;
        if($group_id != null) {
            $this->_error_groups[$error_code] = $group_id;
        }
    }
        
    /**
     * Returns the group identifier that an error code belong to
     *
     * @param integer $error_code The error code to retrieve the group identifier
     * @return string The group identifier if found, or null.
     */
    public function getErrorGroup($error_code) {
        $return_value = null;
        if(key_exists($error_code, $this->_error_groups)) {
            $return_value = $this->_error_groups[$error_code];
        }
        return $return_value;
    }
    
    /**
     * Returns the error id associated to an error code
     *
     * @param integer $error_code The error code (i.e. 55710)
     * @return string The associated error id (i.e. 'ERR_CLASS_NOT_FOUND')
     */
    public function getErrorId($error_code) {
        $return_value = null;
        if(key_exists($error_code, $this->_error_ids)) {
            $return_value = $this->_error_ids[$error_code];
        }
        return $return_value;
    }
    
    /**
     * Returns the error code associated to an error id
     *
     * @param string $error_id The error identifier (i.e. 'ERR_CLASS_NOT_FOUND')
     * @return integer The associated error code (i.e. 55710)
     */
    public function getErrorCode($error_id) {
        $return_value = null;
        if(key_exists($error_id, $this->_error_codes)) {
            $return_value = $this->_error_codes[$error_id];
        }
        return $return_value;
    }
    
    /**
     * Returns the {@link __LionException} subclass that should be used for an error code
     *
     * @param integer $error_code The error code (i.e. 55710)
     * @return string The {@link __LionException} class name (i.e. '__CoreException')
     */
    public function getExceptionClass($error_code) {
        $error_group = $this->getErrorGroup($error_code);
        $error_group = strtoupper($error_group);
        if(key_exists($error_group, $this->_exception_classes)) {
            $return_value = $this->_exception_classes[$error_group];
        }
        else {
            $return_value = '__UnknowException';
        }
        return $return_value;
    }    
    
    /**
     * Returns a readable message associated to an error code, if defined. 
     * Otherwise returns the associated error id
     *
     * @param string $error_code The error code (i.e. 55710)
     * @return string A readable message associated to the error code (i.e. 'Class not found')
     */
    public function getErrorMessage($error_code, array $parameters = array()) {
        $error_id = $this->getErrorId($error_code);
        $return_value = $error_id;
        if(__ResourceManager::getInstance()->hasResource($error_id)) {
            $return_value = __ResourceManager::getInstance()->getResource($error_id)->setParameters($parameters)->getValue();
        }
        return $return_value;
    }
    
    /**
     * Returns an descriptive name for the error group that an error code belong to.
     * If the error code does not belong to any group, the ERR_GROUP_UNKNOW group will be used to retrieve the title.
     *
     * @param string $error_code The error code (i.e. 55710)
     * @return string A readable title for the error group that the error code belong to (i.e. 'Internal core error')
     */
    public function getErrorTitle($error_code) {
        $group_id = $this->getErrorGroup($error_code);
        if($group_id != null) {
            $return_value = __ResourceManager::getInstance()->getResource($group_id)->getValue();
        }
        else {
            $return_value = __ResourceManager::getInstance()->getResource('ERR_GROUP_UNKNOW')->getvalue();
        }
        return $return_value;
    }    
    
}