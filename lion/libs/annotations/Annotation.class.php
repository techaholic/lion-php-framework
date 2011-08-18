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
 * @package    Annotation
 * 
 */

/**
 * This class represents a comment-based annotation
 * 
 */
class __Annotation {

    protected $_class = null;
    protected $_method = null;
    protected $_name = null;
    protected $_arguments = array();
    
    public function __construct($class, $method, $name, $arguments = array()) {
        $this->setClass($class);
        $this->setMethod($method);
        $this->setName($name);
        $this->setArguments($arguments);
    }
    
    /**
     * Set the class where the annotation is located in
     * 
     * @param string The class name
     */
    public function setClass($class) {
        $this->_class = $class;
    }
    
    /**
     * Get the class where the annotation is located in
     * @return string The class name
     */
    public function getClass() {
        return $this->_class;
    }
    
    /**
     * Set the method where the annotation is located in
     * 
     * @param string The method name
     */
    public function setMethod($method) {
        $this->_method = $method;
    }
    
    /**
     * Get the method name where the annotation is located in
     * @return string The method name
     */
    public function getMethod() {
        return $this->_method;
    }
    
    public function setName($name) {
        $this->_name = $name;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function setArguments(array $arguments) {
        $this->_arguments = $arguments;
    }
    
    public function getArguments() {
        return $this->_arguments;
    }
    
    public function hasArgument($argument_name) {
        $return_value = false;
        $argument_name = strtolower($argument_name);
        if(key_exists($argument_name, $this->_arguments)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function getArgument($argument_name) {
        $return_value = null;
        $argument_name = strtolower($argument_name);
        if(key_exists($argument_name, $this->_arguments)) {
            $return_value = $this->_arguments[$argument_name];
        }
        return $return_value;
    }
    
}

