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
 * @package    I18n
 * 
 */

/**
 * This class is the base class for all resources provided by the i18n's __ResourceManager instance.
 *  
 */
abstract class __ResourceBase {
    
    protected $_key          = null;
    protected $_value        = null;
    protected $_description  = null;
    protected $_metadata     = null;
    protected $_parameters   = array();
    
    protected $_has_access_permissions = true;
    
    public function __construct($key = null, $value = null) {
        if($key != null && $value != null) {
            $this->setKey($key);
            $this->setValue($value);
        }
    }
    
    public function setParameters(array $parameters) {
        $this->_parameters = $parameters;
        return $this;
    }
    
    public function setKey($key) {
        $this->_key = $key;
        return $this;
    }
    
    public function getKey() {
        $return_value = $this->_key;
        return $return_value;
    }
    
    public function setValue($value) {
        $this->_value = $value;
        return $this;
    }
    
    public function getValue() {
        $return_value = null;
        if($this->_has_access_permissions && $this->_value !== null) {
            $return_value = $this->_value;
            foreach($this->_parameters as $parameter_key => $parameter_value) {
                $return_value = str_replace('{' . $parameter_key . '}', $parameter_value, $return_value);                
            }
        }
        return $return_value;
    }
    
    public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }
    
    public function getDescription() {
        $return_value = $this->_description;
        return $return_value;
    }
    
    public function getMetadata() {
        $return_value = $this->_metadata;
        return $return_value;
    }

    public function __toString() {
        return $this->_value;
    }
    
    public function onAccessError() {
        $this->_has_access_permissions = false;
    }    
    
    abstract public function display();

    
}