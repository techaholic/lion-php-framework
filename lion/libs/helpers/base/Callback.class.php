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
 * @package    Base
 * 
 */

class __Callback {
    
    private $_class_instance = null;
    private $_method_name    = null;
    private $_parameters     = null;
    
    public function __construct(&$class_instance, $method_name, array &$parameters = null) {
        $this->setClassInstance($class_instance);
        $this->setMethodName($method_name);
        if($parameters != null) {
            $this->setParameters($parameters);
        }
    }
    
    public function setClassInstance(&$class_instance) {
        $this->_class_instance =& $class_instance;
        
    }
    
    public function getClassInstance() {
        return $this->_class_instance;
    }
    
    public function setMethodName($method_name) {
        $this->_method_name = $method_name;
    }
    
    public function getMethodName() {
        return $this->_method_name;
    }
    
    public function setParameters(array &$parameters) {
        $this->_parameters =& $parameters;
    }
    
    public function getParameters() {
        return $this->_parameters;
    }
    
    public function execute(array &$parameters = null) {
        if(!is_array($parameters)) {
            if($parameters != null) {
                $parameters =& $this->_parameters;
            }
            else {
                $parameters = array();
            }
        } 
        return call_user_func_array (array($this->_class_instance, $this->_method_name), $parameters);
    }
    
}