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
 * @package    WebFlow
 * 
 */

class __FlowDefinition {

    const SCOPE_FLOW = 1;
    const SCOPE_REQUEST = 2;
    const SCOPE_SESSION = 3;
    
    protected $_id = null;
    protected $_states = array();
    protected $_start_state = null;
    
    public function __construct($id = null) {
        $this->setId($id);
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        if($this->_id === null) {
            $this->_id = uniqid('flow_');
        }
        return $this->_id;
    }
    
    public function clearStates() {
        unset($this->_states);
        $this->_states = array();
    }
    
    public function hasState($state_id) {
        $return_value = false;
        if(key_exists($state_id, $this->_states)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function addState(__IFlowState &$state) {
        $this->_states[$state->getId()] =& $state;
        if($state instanceof __StartFlowState) {
            if($this->_start_state == null) {
                $this->_start_state =& $state;
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Multiple start states in flow ' . $this->_id);
            }
        }
    }
    
    public function setStates(array $states) {
        $this->clearStates();
        foreach($states as &$state) {
            $this->addState($state);
        }
    }
    
    public function &getState($id) {
        $return_value = null;
        if(key_exists($id, $this->_states)) {
            $return_value =& $this->_states[$id];
        }
        return $return_value;
    }    
    
    public function &getStartState() {
        return $this->_start_state;
    }
    
}
