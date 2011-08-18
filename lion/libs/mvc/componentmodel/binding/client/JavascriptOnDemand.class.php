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
 * This end-point class is used to execute javascript ondemand
 *
 */
class __JavascriptOnDemand extends __ClientEndPoint {
    
    protected $_javascript_code = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C; 
    protected $_value = null;  
    
    /**
     * Constructor method
     *
     * @param string $javascript_code The javascript code to set to
     */
    public function __construct($javascript_code) {
        $this->setJavascriptCode($javascript_code);
    }
    
    /**
     * Set a javascript code associated to current end-point
     *
     * @param string $javascript_code The javascript code to set to
     */
    public function setJavascriptCode($javascript_code) {
        $this->_javascript_code = $javascript_code;
    }
    
    /**
     * Gets the javascript code associated to current end-point
     *
     * @return string
     */
    public function getJavascriptCode() {
        return $this->_javascript_code;
    }
    
    /**
     * Callbacks are not executed on startup
     *
     * @return null
     */
    public function getSetupCommand() {
        $this->setAsSynchronized();
        return null;
    }

    /**
     * Get a command representing the current end-point, just in case the client is unsynchronized
     *
     * @return __AsyncMessageCommand
     */
    public function getCommand() {
        $return_value = null;
        if($this->isUnsynchronized()) {
            $data = array();
            $js_code = $this->_javascript_code;
            if($this->_value !== null) {
                $js_code = str_replace('{value}', $this->_normalizeValue($this->_value), $js_code);
            }
            $data['code']  = $js_code;
            $return_value = new __AsyncMessageCommand();
            $return_value->setClass('__ExecuteJavascriptOnDemandCommand');
            $return_value->setData($data);
            $this->setAsSynchronized();
        }
        return $return_value;                       
    }    
    
    protected function _normalizeValue($value) {
        if(is_bool($value)) {
            if($value == true) {
                $value = 1;
            }
            else {
                $value = 0;
            }
        }
        return $value;        
    }    
    
}

