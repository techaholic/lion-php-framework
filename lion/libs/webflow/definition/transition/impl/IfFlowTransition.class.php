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

class __IfFlowTransition implements __IConditionalFlowTransition {

    protected $_condition = null;
    protected $_next_state_if_true = null;
    protected $_next_state_if_false = null;
    protected $_attributes_collection = null;
    
    public function __construct() {
        $this->_attributes_collection = new __FlowAttributesCollection();
    }
    
    public function setCondition($condition) {
        $this->_condition = $condition;
    }
    
    public function getCondition() {
        return $this->_condition;
    }
    
    public function setNextStateIfTrue($next_state) {
        $this->_next_state_if_true = $next_state;
    }
    
    public function getNextStateIfTrue() {
        return $this->_next_state_if_true;
    }
    
    public function setNextStateIfFalse($next_state) {
        $this->_next_state_if_false = $next_state;
    }
    
    public function getNextStateIfFalse() {
        return $this->_next_state_if_false;
    }    
    
    public function addAttribute($attribute) {
        $this->_attributes_collection->add($attribute);
    }
    
    public function getAttributes() {
        return $this->_attributes_collection->toArray();
    }    
    
    public function evaluateCondition() {
        
    }
    
}