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

class __ResponseWriter implements __IResponseWriter {
    
    const POSITION_TOP    = 1;
    const POSITION_BOTTOM = 2;
    
    protected $_id = null;
    protected $_position = null;
    protected $_regexp = null;
    protected $_content  = null;
    protected $_response_writers = array();
    
    public function __construct($id) {
        if(empty($id)) {
            throw __ExceptionFactory::getInstance()->createException('A valid id is required to instantiate a __ResponseWriter object');
        }
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setContent($content) {
        $this->_content = $content;
    }
    
    public function setPosition($position) {
        $this->_position = $position;
    }
    
    public function getContent() {
        $return_value = $this->_content;
        $return_value .= $this->_getChildrensContent();        
        return $return_value;
    }
    
    protected function _getChildrensContent() {
        $return_value = '';
        foreach($this->_response_writers as $response_writer) {
            $return_value .= $response_writer->getContent();
        }
        return $return_value;
    }    
        
    public function __toString() {
        return $this->getContent();
    }
    
    public function write(__IResponse &$response) {
        switch($this->_position) {
            case self::POSITION_TOP:
                $response->dockContentOnTop($this->getContent(), $this->getId());
                break;
            case self::POSITION_BOTTOM:
                $response->dockContentAtBottom($this->getContent(), $this->getId());
                break;
            default:
                $response->addContent($this->getContent(), $this->getId());
        }
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
    
    public function clear() {
        $this->_response_writers = array();
        $this->_content = null;
    }    

   
}