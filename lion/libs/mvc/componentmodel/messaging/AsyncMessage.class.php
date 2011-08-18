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

class __AsyncMessage {
    
    protected $_header = array();
    protected $_commands = array();
    
    public function __construct() {
        $this->_header = new __AsyncMessageHeader();
    }
    
    public function setHeader(__MessageHeader $message_header) {
        $this->_header = $message_header;
    }
    
    /**
     * Enter description here...
     *
     * @return __AsyncMessageHeader
     */
    public function &getHeader() {
        return $this->_header;
    }
    
    public function setCommands(array $commands) {
        $this->_commands = $commands;
    }
    
    public function addCommand(__AsyncMessageCommand &$command) {
        if($command != null) {
            $this->_commands[] =& $command;
        }
    }
    
    public function hasPayload() {
        return count($this->_commands) > 0;
    }
    
    public function &getCommands() {
        return $this->_commands;
    }
    
    public function toArray() {
        $return_value = array();
        //set the header:
        $return_value['header'] = $this->_header->toArray();
        $return_value['commands'] = array();
        foreach($this->_commands as $command) {
            $return_value['commands'][] = $command->toArray();
        }
        return $return_value;        
    }
    
    public function toJson() {
        $async_message_array = $this->toArray();        
        if(function_exists('json_encode')) {
            $return_value = json_encode($async_message_array);
        }
        else {
            $services_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $return_value = $services_json->encode($async_message_array);
        }
        return $return_value;
    }
    
    public function __toString() {
        return $this->toJson();
    }    
    
}