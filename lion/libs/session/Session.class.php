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
 * @package    Session
 * 
 */


/**
 * This class represents the session.
 * 
 * You can get a reference to the __Session instance by calling the {@link __Context::getSession()} method
 * <code>
 * 
 * //Get the session:
 * $session = __ApplicationContext::getInstance()->getSession();
 * 
 * //Retrieve information from the session by reference:
 * $my_data = $session->getData('my_data');
 * 
 * </code>
 *
 */
class __Session implements __IDataContainer {
    
    protected $_session_data = null;
    
    public function __construct($context_id)
    {
        $this->_context_id = $context_id;
        $this->_createContextSession();
    }
    
    protected function _createContextSession() {
        if(!key_exists($this->_context_id, $_SESSION)) {
            $_SESSION[$this->_context_id] = array();
        }
        $this->_session_data =& $_SESSION[$this->_context_id];
    }
    
    
    /**
     * Returns an identifier for current session
     *
     * @return string
     */
    public function getId() {
        return session_id();
    }
    
    /**
     * Returns if there is any data on session with the given key
     *
     * @param string $key The key to check if there is any data on session
     * @return bool true if there is any data on session with the given key, otherwise false
     */
    public function hasData($key) {
        $key = $this->_parseKey($key);
        return key_exists($key, $this->_session_data);
    }
    
    /**
     * Get data from the session
     *
     * @param string $key The key that identify the information to retrieve from
     * @return mixed The requested session information
     * 
     * @throws __SessionException If the session hasn't been started
     */
    public function &getData($key) {
        $return_value = null;
        $key = $this->_parseKey($key);
        if (key_exists($key, $this->_session_data)) {
            $return_value =& $this->_session_data[$key];
        }
        return $return_value;
    }

    /**
     * Stores data into the session
     * 
     * @param string $key The key that identify the information to store to
     * @param mixed &$data The information to store into the session
     * 
     * @throws __SessionException If the session hasn't been started
     */
    public function setData($key, &$data) {
        $key = $this->_parseKey($key);    	
        $this->_session_data[$key] =& $data;
    }

    /**
     * Removes data from the session
     * 
     * @param string $key The key that identify the information to remove from
     * 
     * @throws __SessionException If the session hasn't been started
     */
    public function removeData($key) {
        $key = $this->_parseKey($key);    	
        if (key_exists($key, $this->_session_data)) {
            unset($this->_session_data[$key]);
        }
    }    
    
    private function _parseKey($key) {
    	$return_value = $key;
    	$return_value = str_replace('::', '_', $return_value);
    	return $return_value;
    }
    
    /**
     * Alias of clear
     *
     */
    public function destroy() {
        $this->clear();
    }
    
    public function clear() {
        if(key_exists($this->_context_id, $_SESSION)) {
            foreach($_SESSION[$this->_context_id] as $key => $dummy) {
                $_SESSION[$this->_context_id][$key] = null;
            }
            unset($_SESSION[$this->_context_id]);
        }
        $this->_createContextSession();
    }
        
}
