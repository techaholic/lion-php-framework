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
 * @package    History
 * 
 */


class __HistoryManager
{
    private $_requests     = null;
    
    static private $_instance = null;

    private function __construct() {
        $session = __ApplicationContext::getInstance()->getSession();
        $this->_requests =& $session->getData('__HistoryManager::_requests');
        if($this->_requests === null) {
            $this->_requests = array();
            $session->setData('__HistoryManager::_requests', $this->_requests);
        }
        else {
            if(key_exists('current_request', $this->_requests)) {
                if(key_exists('last_request', $this->_requests)) {
                    $this->_requests['previous_request'] = $this->_requests['last_request'];
                    unset($this->_requests['last_request']);
                }
                $this->_requests['last_request'] =& $this->_requests['current_request'];
                unset($this->_requests['current_request']);
            }
        }
    }

    /**
     * This method return a singleton instance of __HistoryManager instance
     *
     * @return __HistoryManager
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __HistoryManager();
        }
        return self::$_instance;
    }
        
    /**
     * This method creates a new record in the history.
     * The new record will contain at least a copy of current request:
     *
     * @param __Request The requeste to append to the request's history   
     * @return boolean true if the {@link Request} instance was added, else false
     * 
     */
    public function addRequest(__IRequest &$request) {
        $this->_current_request = $request;
        $this->_requests['current_request'] =& $request;
    }
    
    /**
     * Get the last user request
     *
     * @return __IRequest
     */
    public function &getLastRequest() {
        $return_value = null;
        if(key_exists('last_request', $this->_requests)) {
            $return_value =& $this->_requests['last_request'];
        }
        return $return_value;
    }
    
    /**
     * Get the previous user request
     *
     * @return __IRequest
     */
    public function &getPreviousRequest() {
        $return_value = null;
        if(key_exists('previous_request', $this->_requests)) {
            $return_value =& $this->_requests['previous_request'];
        }
        return $return_value;
    }
    
}

