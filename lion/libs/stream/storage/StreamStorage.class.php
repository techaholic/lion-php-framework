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
 * @package    Stream
 * 
 */

abstract class __StreamStorage {
    
    protected $_required_storage_parameters = array();
    protected $_storage_parameters = null;
    
    final public function __construct(array $storage_parameters = array()) {
        $storage_parameters = array_change_key_case($storage_parameters, CASE_UPPER);
        foreach ($this->_required_storage_parameters as $required_storage_parameter) {
            if(!key_exists($required_storage_parameter, $storage_parameters)) {
                throw new __StreamException("Missing required parameter to build an instance of '" . get_class($this) . "': '" . $required_storage_parameter . "'");
            }
        }
        $this->_storage_parameters = $storage_parameters;
    }
        
    final public function getStorageParameter($parameter_name) {
        $return_value = null;
        $parameter_name = strtoupper($parameter_name);
        if(key_exists($parameter_name, $this->_storage_parameters)) {
            $return_value = $this->_storage_parameters[$parameter_name];
        }
        return $return_value;
    }
    
    final public function hasStorageParameter($parameter_name) {
        return key_exists($parameter_name, $this->_storage_parameters);
    }
    
    abstract public function open($mode);
    
    abstract public function read($length);

    abstract public function write($data, $length = null);
    
    abstract public function close();

    abstract public function tell();
    
    abstract public function flush();

    abstract public function eof();
    
    abstract public function lock($operation);
    
    abstract public function seek($offset, $whence = null);
    
    abstract public function stat();

    abstract public function url_stat();
    
}