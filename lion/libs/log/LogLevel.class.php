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
 * @package    Log
 * 
 */

class __LogLevel {
    
    private $_up_to  = null;
    private $_levels = null;
    
    const NOT_LOGABLE = 0x00000000;
    const CRITICAL    = 0x00000001;
    const ALERT       = 0x00000010;
    const ERROR       = 0x00000100;
    const WARNING     = 0x00001000;
    const NOTICE      = 0x00010000;
    const INFO        = 0x00100000;
    const DEBUG       = 0x01000000;
    
    private $_level_strings = array(
        self::CRITICAL    => "Critical",
        self::ALERT       => "Alert",
        self::ERROR       => "Error",
        self::WARNING     => "Warning",
        self::NOTICE      => "Notice",
        self::INFO        => "Info",
        self::DEBUG       => "Debug",
        self::NOT_LOGABLE => "Not-logable"
    );
    
    public function getLogLevelAsString($log_level) {
        $return_value = "";
        if(key_exists($log_level, $this->_level_strings)) {
            $return_value = $this->_level_strings[$log_level];
        }
        return $return_value;
    }
    
    public function setUpTo($up_to) {
        $this->_up_to  = $up_to;
        $this->_levels = null;
    }
    
    public function setLevels($levels) {
        $this->_levels = $levels;
        $this->_up_to  = null;
    }
    
    public function checkLevel($level) {
        $return_value = false;
        if( $level != self::NOT_LOGABLE ) {
            if($this->_up_to !== null) {
                if($level <= $this->_up_to) {
                    $return_value = true;
                }
            }
            else if($this->_levels !== null) {
                if( ($level & $this->_levels) != 0) {
                    $return_value = true;
                }
            }
        }
        return $return_value;
    }
    
}
