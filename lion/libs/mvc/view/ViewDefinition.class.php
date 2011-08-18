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
 * @package    View
 * 
 */


class __ViewDefinition {

    private $_view_code  = null;
    private $_view_class = null;
    private $_properties = array();

    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }

    public function getViewCode() {
        return $this->_view_code;
    }

    public function setViewClass($view_class) {
        $this->_view_class = $view_class;
    }

    public function getViewClass() {
        return $this->_view_class;
    }
    
    public function addProperty($property_name, $property_value) {
        $this->_properties[$property_name] = $property_value;
    }
    
    public function getProperties() {
        return $this->_properties;
    }

    public function isValidForViewCode($view_code) {
        $return_value = false;
        if(strpos($this->_view_code, '*') !== false) {
            if(preg_match('/' . str_replace('*', '(.+?)', strtoupper($this->_view_code)) . '/', strtoupper($view_code))) {
                $return_value = true;
            }
        }
        else if($this->_view_code == $view_code) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function &getView($view_code = null) {
        $return_value = null;
        $view_code_substring = null;
        if(strpos($this->_view_code, '*') !== false) {
            if($view_code != null) {
                if(preg_match('/^' . str_replace('*', '(.+?)', $this->_view_code) . '$/', $view_code, $view_code_substring_array)) {
                    $view_code_substring = $view_code_substring_array[1];
                }
                else {
                    $return_value = null;
                    return $return_value;
                }
            }
        }
        $view_class_name = $this->getViewClass();
        $return_value = new $view_class_name();
        $return_value->setCode($view_code ? $view_code : $this->_view_code);
        foreach($this->_properties as $property_name => $property_value) {
            if($view_code_substring != null) {
                $property_value = str_replace('*', $view_code_substring, $property_value);
            }
            $method_name = 'set' . ucfirst($property_name);
            if(method_exists($return_value, $method_name)) {
                $return_value->$method_name($property_value);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_SETTER_NOT_FOUND_FOR_PROPERTY', array($view_class_name, $property_name));
            }
        }
        return $return_value;
    }

}