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

class __HtmlSelectiveElementCallback extends __HtmlElementCallback {

    protected $mapping_callbacks = array();
    
    public function __construct($instance) {
        $this->setInstance($instance);
    }
    
    public function &addMappingCallback($value, $callback) {
        $value = $this->_normalizeValue($value);
        $this->_mapping_callbacks[$value] = $callback;
        return $this;
    }
    
    public function getCommand() {
        $return_value = null;
        if($this->isUnsynchronized()) {
            $parameter = $this->_normalizeValue($this->getValue());
            if(key_exists($parameter, $this->_mapping_callbacks)) {
                $data = array();
                $data['parameter'] = null;
                $data['receiver']  = $this->_instance;
                $data['method']    = $this->_mapping_callbacks[$parameter];
                $return_value = new __AsyncMessageCommand();
                $return_value->setClass($this->getClientCommandClass());
                $return_value->setData($data);
            }
            $this->setAsSynchronized();
        }
        return $return_value;                              
    }     
    
    protected function _normalizeValue($value) {
        if(is_bool($value)) {
            if($value == true) {
                $value = 1;
            }
            else {
                $value = 0;
            }
        }
        return $value;        
    }
    
}
