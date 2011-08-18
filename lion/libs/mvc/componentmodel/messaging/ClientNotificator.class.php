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
 * This is the class in charge of send to client all the async message to keep component status synchronization
 *
 */
class __ClientNotificator {

    static protected $_instance = null;
    
    private $_dirty_components = array();    

    
    protected function __construct() {
        
    }
    
    /**
     * Gets a singleton instance 
     *
     * @return __ClientNotificator
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ClientNotificator();
        }
        return self::$_instance;
    }
    
    /**
     * Sets as dirty a given component, which will be checked for client notifications
     *
     * @param __IComponent $component
     */
    public function setDirty(__IComponent &$component) {
        $this->_dirty_components[$component->getId()] =& $component;
    }
    
    /**
     * Gets an array of components that have been changed since last request
     *
     * @return array
     */
    public function &getDirtyComponents() {
        return $this->_dirty_components;
    }
    
    /**
     * Sets as undirty all the components (internally, reset the array of references to dirty components)
     *
     */
    public function clearDirty() {
        unset($this->_dirty_components);
        $this->_dirty_components = array();
    }
    
    /**
     * Gets the startup notification, which contains initial component states
     *
     * @param string $view_code
     * @return __AsyncMessage
     */
    public function getStartupNotification($view_code) {
        $return_value = null;
        if(__ComponentHandlerManager::getInstance()->hasComponentHandler($view_code)) {
            $components = __ComponentHandlerManager::getInstance()->getComponentHandler($view_code)->getComponents();
            $return_value = __AsyncMessageFactory::getInstance()->createComponentsAsyncMessage($components, true);
        }
        return $return_value;
    }
    
    public function notifyProgress(__IComponent &$component) {
        $async_message = __AsyncMessageFactory::getInstance()->createProgressAsyncMessage($component);
        $response = __FrontController::getInstance()->getResponse();
        $response->appendContent($async_message->toJson());
        $response->flush();
    }
    
    /**
     * Sends to client an async message containing all pending status change notifications
     *
     */
    public function notify() {
        $async_message = __AsyncMessageFactory::getInstance()->createComponentsAsyncMessage($this->_dirty_components);
        $response = __FrontController::getInstance()->getResponse();
        $response->appendContent($async_message->toJson());
        $response->flush();
    }
    
}
