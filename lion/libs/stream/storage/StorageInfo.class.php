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

class __StorageInfo {
    
    private $_file_id = null;
    private $_file_format = null;
    private $_storage_media = STREAM_STORAGE_FILE_SYSTEM;
    private $_storage_media_parameters = array();
    
    public function __construct($file_id) {
        $this->_file_id = $file_id;
    }
    
    public function getFileId() {
        return $this->_file_id;
    }
    
    public function setStorageMedia($storage_media) {
        $this->_storage_media = $storage_media;
    }
    
    public function getStorageMedia() {
        return $this->_storage_media;
    }
     
    public function setFormat($file_format) {
        $this->_file_format = $file_format;
    }
    
    public function getFormat() {
        return $this->_file_format;
    }
    
    public function setStorageParameters(array $storage_parameters) {
        $this->_storage_media_parameters = $storage_parameters;
    }
    
    public function addStorageParameter($parameter_name, $parameter_value) {
        $this->_storage_media_parameters[$parameter_name] = $parameter_value;
    }
    
    public function getStorageParameter($parameter_name) {
        $return_value = null;
        if(key_exists($parameter_name, $this->_storage_media_parameters)) {
            $return_value = $this->_storage_media_parameters[$parameter_name];
        }
        return $return_value;
    }
    
    public function getStorageParameters() {
        return $this->_storage_media_parameters;
    }
    
}
