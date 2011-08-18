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
 * This is the class in charge of initialize the session according to the configuration settings.
 * 
 * It will start the session and setup (if defined) an specific session handler to manage the session storage.
 * 
 * Note: This class in used internally by Lion.
 * 
 * @see __Session
 *
 */
class __SessionManager {

    static private $_instance       = null;
    private $_session_handler_class = null;
    private $_sessions      = array();
    private $_session = null;
    private $_new_session = false;
    
    //Session status constants:
    const STATUS_STOPPED   = 0;
    const STATUS_STARTED   = 2;
    
    private $_status = __SessionManager::STATUS_STOPPED;
    
    private function __construct()
    {
    }
    
    public function __destruct() {
        $this->endSession();
    }

    /**
     * This method return a singleton instance of __SessionManager
     *
     * @return __SessionManager A singleton reference to the __SessionManager
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __SessionManager();
        }
        return self::$_instance;
    }

    /**
     * Sets the session handler class for setup the session
     *
     * @param string $session_handler_class The session handler class name
     */
    public function setSessionHandlerClass($session_handler_class) {
        if(class_exists($session_handler_class)) {
            $this->_session_handler_class = $session_handler_class;
        }
    }
    
    /**
     * Set the session handler, that is the class in charge of handling session storage.
     *
     * @param __ISessionHandler &$session_handler The session handler instance:
     */
    public function setSessionHandler(__ISessionHandler &$session_handler) {
        session_set_save_handler(
              array($session_handler, 'open'),      
              array($session_handler, 'close'),      
              array($session_handler, 'read'),      
              array($session_handler, 'write'),      
              array($session_handler, 'destroy'),      
              array($session_handler, 'gc')
        );
    }
    
    /**
     * Starts the session.
     * 
     * This method raises 2 events:<br>
     *  - EVENT_ON_SESSION_START: If the session has been started successful.
     * 
     * @throws __SessionException if the session has already been started
     *
     */
    public function startSession() {
        if( $this->getSessionStatus() != __SessionManager::STATUS_STOPPED ) {
            throw new __SessionException('An atempt to start the session has been detected while the session was already started.');
        }
        try {
            session_start();
            $session = $this->getSession();
            $app_name = __Lion::getInstance()->getRuntimeDirectives()->getDirective('APP_NAME');
            if(!$session->hasData($app_name)) {
                $dummy_value = true;
                $session->setData($app_name, $dummy_value);
                $this->_new_session = true;
            }
            __EventDispatcher::getInstance()->broadcastEvent(new __Event($this, EVENT_ON_SESSION_START));
        }
        catch (Exception $e) {
            throw new __SessionException($e->getMessage(), $e->getCode());
        }
    }
    
    public function endSession() {
        session_write_close();
        $this->_status = __SessionManager::STATUS_STOPPED;
    }
    
    public function destroySession() {
        $this->getSession()->clear();
        session_destroy();
    }
    
    public function isSessionStarted() {
        $return_value = false;
        if( $this->getSessionStatus() == __SessionManager::STATUS_STARTED ) {
            $return_value = true;
        }
        return $return_value;
    }

    public function getSessionStatus() {
        if (session_id() == "") {
            $return_value = __SessionManager::STATUS_STOPPED;            
        }
        else {
            $return_value = __SessionManager::STATUS_STARTED;
        }
        return $return_value;        
    }    
    
    public function isNewSession() {
        return $this->_new_session;
    }
    
    /**
     * Get the session
     *
     * @param string $context_id
     * @return __Session
     */
    public function &getSession($context_id = null) {
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }
        if(!key_exists($context_id, $this->_sessions)) {
            if(!$this->isSessionStarted()) {
                $this->startSession();
            }
            $session = new __Session($context_id);
            $this->_sessions[$context_id] =& $session;
        }
        return $this->_sessions[$context_id];
    }
    
}
