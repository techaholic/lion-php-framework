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
 * @package    Response
 * 
 */

class __ResponseWriterManager {
    
    static private $_instance = null;
    
    private $_response_writers = array();
    
    private function __construct() {
    }
    
    /**
     * Gets a reference to the singleton {@link __ResponseWriterManager} instance
     *
     * @return __ResponseWriterManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ResponseWriterManager();
        }
        return self::$_instance;
    }    
    
    public function hasResponseWriter($id) {
        $return_value = false;
        if(key_exists($id, $this->_response_writers)) {
            $return_value = true;
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                if($response_writer->hasResponseWriter($id)) {
                    return true;
                }
            }
        }
        return $return_value;
    }
    
    public function &getResponseWriter($id) {
        $return_value = null;
        if(key_exists($id, $this->_response_writers)) {
            $return_value =& $this->_response_writers[$id];
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                $return_value = $response_writer->getResponseWriter($id);
                if($return_value != null) {
                    return $return_value;
                }
            }
        }
        return $return_value;
    }
    
    public function addResponseWriter(__IResponseWriter $response_writer) {
        $this->_response_writers[$response_writer->getId()] = $response_writer;
    }
    
    public function write(__IResponse &$response) {
        foreach($this->_response_writers as $response_writer) {
            $response_writer->write($response);
        }
    }
    
    public function clear() {
        $this->_response_writers = array();
    }
    
}