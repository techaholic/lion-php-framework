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
 * This class contains all the in-session {@link __ComponentHandler} instances.
 * 
 * It exposes a method to retrieve a {@link __ComponentHandler} instance associated to a given view
 *
 */
final class __ComponentHandlerManager {
    
    static private $_instance = null;
    
    private $_component_handlers = array();
        
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__ComponentHandlerManager::_component_handlers')) {
            $this->_component_handlers =& $session->getData('__ComponentHandlerManager::_component_handlers');
        }
        else {
            $session->setData('__ComponentHandlerManager::_component_handlers', $this->_component_handlers);
        }
    }
    
    /**
     * Gets the __ComponentHandlerManager singleton instance
     *
     * @return __ComponentHandlerManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ComponentHandlerManager();
        }
        return self::$_instance;
    }
    
    /**
     * Checks if there is contained a {@link __ComponentHandler} instance associated to a given view
     *
     * @param string $view_code The view code
     * @return bool
     */
    public function hasComponentHandler($view_code) {
        $view_code = strtoupper($view_code);
        return key_exists($view_code, $this->_component_handlers);
    }

    public function &createComponentHandler($view_code) {
        $view_code = strtoupper($view_code);
        $return_value = new __ComponentHandler($view_code);
        $this->_component_handlers[$view_code] =& $return_value;
        return $return_value;
    }
    
    public function &addComponentHandler(__ComponentHandler &$component_handler) {
        $view_code = strtoupper($component_handler->getViewCode());
        $this->_component_handlers[$view_code] =& $component_handler;
        return $component_handler;
    }    
    
    /**
     * Delete a component handler and all his components from the component pool.
     * It also removes the associated event handler if any
     *
     * @param string $view_code
     */
    public function freeComponentHandler($view_code) {
        $upper_case_view_code = strtoupper($view_code);
        if(key_exists($upper_case_view_code, $this->_component_handlers)) {
            $component_handler = $this->_component_handlers[$upper_case_view_code];
            $component_handler->freeComponents();
            unset($this->_component_handlers[$upper_case_view_code]);
            __EventHandlerManager::getInstance()->removesEventHandler($view_code);
        }
    }
    
    
    /**
     * Gets a {@link __ComponentHandler} instance corresponding to a given view code.
     * 
     * If the requested {@link __ComponentHandler} does not exists, it creates a new one.
     *
     * @param string $view_code The view code
     * @return __ComponentHandler
     */
    public function &getComponentHandler($view_code) {
        $return_value = null;
        $view_code = strtoupper($view_code);
        if(key_exists($view_code, $this->_component_handlers)) {
            $return_value = $this->_component_handlers[$view_code];        
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Component handler not found for view code: ' . $view_code);
        }
        return $return_value;
    }
    
}