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

class __FileSystemStreamStorage extends __StreamStorage {
    
    protected $_file_handler = null;
    protected $_file_name    = null;
    protected $_required_storage_parameters = array('FILENAME');
    
    public function open($mode) {
        $filename = $this->getFileName();
        $this->_file_name = $filename;
        $this->_file_handler = fopen($this->_file_name, $mode);
    }
    
    private function getFileName() {
        $return_value = $this->getStorageParameter('FILENAME');
        if($this->hasStorageParameter('BASEDIR')) {
            $return_value = $this->getStorageParameter('BASEDIR') . '/' . $return_value;
        }
        return $return_value;        
    }
    
    public function read($length)
    {
        return fread($this->_file_handler, $length);
    }

    public function write($data, $length = null)
    {
        if($length === null) {
            return fwrite($this->_file_handler, $data);
        }
        else {
            return fwrite($this->_file_handler, $data, $length);
        }
    }
    
    public function close() {
        return fclose($this->_file_handler);
    }    

    public function tell()
    {
        return ftell($this->_file_handler);
    }

    public function flush() {
        return fflush($this->_file_handler);
    }
    
    public function eof()
    {
        return feof($this->_file_handler);
    }

    public function lock($operation) {
        return flock($this->_file_handler, $operation);
    }

    public function seek($offset, $whence = null)
    {
        return fseek($this->_file_handler, $offset, $whence);
    }
    
    public function stat() {
        return fstat($this->_file_handler);
    }
    
    public function url_stat() {
        $return_value = array();
        $filename = $this->getFileName();
        if(file_exists($filename)) {
            $return_value = stat($filename);
        }
        return $return_value;
    }
    
}