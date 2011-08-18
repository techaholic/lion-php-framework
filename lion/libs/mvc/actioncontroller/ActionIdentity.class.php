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
 * @package    ActionController
 * 
 */


/**
 * This class stores an action identity, it is, a pair [controller code, action code]
 * 
 * This class is able to resolve both the controller and action codes even if any of them is missing.
 *
 */
class __ActionIdentity {
    
    private $_action_code = null;
    private $_controller_code = null;
    
    public function __construct($controller_code = null, $action_code = null) {
        $this->setControllerCode($controller_code);
        $this->setActionCode($action_code);
    }

    public function setActionCode($action_code) {
        $this->_action_code = $action_code;
    }
    
    public function setAction($action_code) {
        return $this->setActionCode($action_code);
    }
    
    public function getActionCode() {
        return $this->_action_code;
    }
    
    public function setControllerCode($controller_code) {
        $this->_controller_code = $controller_code;
    }

    public function setController($controller_code) {
        return $this->setControllerCode($controller_code);
    }
    
    public function getControllerCode() {
        $return_value = null;
        if($this->_controller_code != null) {
            $return_value = $this->_controller_code;
        }
        else if($this->_action_code != null) {
            $return_value = $this->_action_code;
        }
        return $return_value;
    }
    
    
}