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

class __HttpResponse extends __Response {
    
    protected $_headers = array();
    protected $_buffer_control = false;
    
    public function __construct() {
    }

    public function setBufferControl($buffer_control) {
        $this->_buffer_control = (bool) $buffer_control;
        if($this->_buffer_control) {
            ob_start(array($this, 'doFlush'));
        }
    }
    
    public function addHeader($header) {
        array_push($this->_headers, $header);
    }

    public function clearHeaders() {
        unset($this->_headers);
        $this->_headers = array();
    }
    
    public function flush() {
        if($this->_buffer_control) {
            @ob_end_flush();
            flush();
            ob_start(array($this, 'doFlush'));
        }
        else {
            print $this->doFlush();
        }
    }
    
    public function flushAll() {
        if($this->_buffer_control) {
            $level = ob_get_level();
            for($i = 0; $i < $level; $i++) {
                ob_end_flush();
            }
            flush();
        }
        else {
            print $this->doFlush();
        }
    }    
    
    public function doFlush($buffer = null) {
        //add the pending buffer to the current response content (if not empty)
        if(!empty($buffer)) {
            $this->appendContent($buffer);
        }
        if(!headers_sent()) {
            foreach($this->_headers as $header) {
                header($header);
            }
        }
        $content = $this->getContent();
        $this->clearContent();
        return $content;
    }
    
    public function __toString() {
        return $this->getContent();
    }
    
    public function addCookie(__Cookie $cookie)
    {   
        $cookie_array = array($cookie->getName(), 
                              $cookie->getValue(),
                              $cookie->getTtl(),
                              $cookie->getPath(),
                              $cookie->getDomain(),
                              $cookie->getSecure(),
                              $cookie->getHttpOnly());
        call_user_func_array('setcookie', $cookie_array);
    }
    
    public function addCookies(array $cookies) {
        foreach($cookies as $cookie) {
            $this->addCookie($cookie);
        }
    }
    
    
}

