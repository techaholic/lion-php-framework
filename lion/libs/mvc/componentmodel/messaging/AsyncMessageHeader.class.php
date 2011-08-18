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
 * Represents the header of an asynchronous message 
 *
 * @see __AsyncMessage, __AsyncMessageCommand
 */
class __AsyncMessageHeader {
    
    /**
     * The request has succeeded. 
     *
     */
    const ASYNC_MESSAGE_STATUS_OK       = 1;

    /**
     * The server encountered an unexpected condition which prevented it from fulfilling the request. 
     *
     */
    const ASYNC_MESSAGE_STATUS_ERROR    = -1;
    
    /**
     * The client should redirect to another URL
     *
     */
    const ASYNC_MESSAGE_STATUS_REDIRECT = 302;
    
    private $_id       = null;
    private $_status   = null;
    private $_location = null;
    private $_message  = null;

    
    public function __construct() {
        $this->_id = uniqid('m');
        $this->_status  = self::ASYNC_MESSAGE_STATUS_OK;
    }
    
    public function setStatus($status) {
        $this->_status = (int) $status;
    }
    
    public function getStatus() {
        return $this->_status;
    }

    /**
     * Set the URL to redirect to.
     * Setting the URL forces the status to ASYNC_MESSAGE_STATUS_REDIRECT
     *
     * @param string $location
     */
    public function setLocation($location) {
        $this->_location = $location;
        if(!empty($this->_location)) {
            $this->_status = self::ASYNC_MESSAGE_STATUS_REDIRECT;
        }
    }
    
    public function getLocation() {
        return $this->_location;
    }
    
    public function setMessage($message) {
        $this->_message = $message;
    }
    
    public function getMessage() {
        return $this->_message;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function toArray() {
        $return_value = array();
        $return_value['id']       = $this->_id;
        $return_value['status']   = $this->_status;
        $return_value['location'] = $this->_location; 
        $return_value['message']  = $this->_message; 
        return $return_value;
    }
    
    
}