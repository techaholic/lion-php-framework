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
 * This end-point class is used to show validation messages to client
 *
 */
class __ShowErrorMessage extends __ClientEndPoint {
    
    protected $_instance = null;
    protected $_message = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C;    
    
    public function __construct($instance) {
        $this->setInstance($instance);
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $instance
     */
    public function setInstance($instance) {
        $this->_instance = $instance;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getInstance() {
        return $this->_instance;
    }
    
    public function setErrorMessage($message) {
        $this->_message = $message;
    }
    
    public function getErrorMessage() {
        return $this->_message;
    }

    public function synchronize(__IServerEndPoint &$server_end_point) {
        if( $server_end_point instanceof __IValueHolder ) {
            $this->setErrorMessage( $server_end_point->getValue() );
        }
    }
    
    /**
     * Gets the startup command representing the current end-point
     *
     * @return __AsyncMessageCommand
     */
    public function getSetupCommand() {
        return null;
    }

    /**
     * Get a command representing the current end-point
     *
     * @return __AsyncMessageCommand
     */
    public function getCommand() {
        $return_value = null;
        $data = array();
        $data['message']  = $this->_message;
        $data['receiver'] = $this->_instance;
        $return_value = new __AsyncMessageCommand();
        $return_value->setClass('__ShowValidationErrorCommand');
        $return_value->setData($data);
        return $return_value;             
    }    
    
    
}

