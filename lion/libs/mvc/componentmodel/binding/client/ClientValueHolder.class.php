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
 * @package    ComponentModel
 * 
 */

/**
 * This class represents a client value holder
 *
 */
abstract class __ClientValueHolder extends __ClientEndPoint implements __IValueHolder {
    
    const VALUE_DOMAIN_UNKNOWN = 0;
    const VALUE_DOMAIN_STRING  = 1;
    const VALUE_DOMAIN_BOOL    = 2;
    const VALUE_DOMAIN_INTEGER = 3;
    const VALUE_DOMAIN_FLOAT   = 4;
    
    protected $_instance = null;
    protected $_property = null;
    protected $_synchronized = true;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_ALL;  
    protected $_value_domain = self::VALUE_DOMAIN_UNKNOWN;
    
    public function __construct($instance, $property) {
        $this->setInstance($instance);
        $this->setProperty($property);
    }
    
    public function setInstance($instance) {
        $this->_instance = $instance;
    }
    
    public function setValueDomain($value_domain) {
        $this->_value_domain = $value_domain;
    }
    
    public function getValueDomain() {
        return $this->_value_domain;
    }
    
    /**
     * Enter description here...
     *
     * @return string
     */
    public function getInstance() {
        return $this->_instance;
    }
    
    /**
     * Enter description here...
     *
     * @param string $property
     */
    public function setProperty($property) {
        $this->_property = $property;
    }
    
    /**
     * Enter description here...
     *
     * @return string
     */
    public function getProperty() {
        return $this->_property;
    }

    /**
     * Returns the current client value
     *
     */
    public function getValue() {
        return $this->_value;
    } 

    /**
     * Sets a new value for client value-holder
     *
     * @param mixed $value The value to set to
     */
    public function setValue($value) {
        if($this->_value_domain != self::VALUE_DOMAIN_UNKNOWN) {
            $value = $this->_toDomainValue($value);
        }
        if($value !== $this->getValue()) {
            $this->_value = $value;
            $this->_ui_binding->synchronizeServer();
        }
    }
    
    public function reset() {
        $this->setValue(null);
    }
    
    public function getSetupCommand() {
        $data = array();
        $data['code']     = $this->getUIBinding()->getId();
        $data['receiver'] = $this->_instance;
        $data['property'] = $this->_property;
        $data['value']    = $this->_value;
        if($this->getBoundDirection() == __IEndPoint::BIND_DIRECTION_S2C) {
            $data['syncServer'] = false;
        }
        $data['valueHolderClass'] = $this->getClientValueHolderClass();
        $command_data = array('valueHolderData' => $data);
        $return_value = new __AsyncMessageCommand();
        $return_value->setClass('__RegisterValueHolderCommand');
        $return_value->setData($command_data);
        $this->setAsSynchronized();          
        return $return_value;
    }

    public function getCommand() {
        $return_value = null;
        if($this->isUnsynchronized()) {
            $data = array();
            $data['code']  = $this->getUIBinding()->getId();
            $data['value'] = $this->_value;
            $return_value  = new __AsyncMessageCommand();
            $return_value->setClass('__UpdateValueHolderCommand');
            $return_value->setData($data);
            $this->setAsSynchronized();
        }
        return $return_value;
    }
    
    public function _toDomainValue($value) {
        switch ($this->_value_domain) {
            case self::VALUE_DOMAIN_BOOL:
                if(is_string($value)) {
                    switch(strtoupper($value)) {
                        case 'TRUE':
                        case 'YES':
                        case 'ON':
                            $value = true;
                            break;
                        default:
                            $value = false;
                            break;
                    }
                }
                else {
                    $value = (bool) $value;
                }
                break;
            case self::VALUE_DOMAIN_FLOAT:
                $value = (float) $value;
                break;
            case self::VALUE_DOMAIN_INTEGER:
                $value = (int) $value;
                break;
            case self::VALUE_DOMAIN_STRING:
                $value = '' . $value;
                break;
        }
        return $value;
    }
    
    
    abstract public function getClientValueHolderClass();
    
}