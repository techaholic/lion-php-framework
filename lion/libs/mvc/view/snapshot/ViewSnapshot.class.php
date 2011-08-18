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
 * @package    View
 * 
 */

class __ViewSnapshot {

    protected $_component_handler = null;
    protected $_event_handler = null;
    protected $_components = null;
    
    public function __construct($view_code = null) {
        if($view_code != null) {
            $this->setViewCode($view_code);
        }
    }
    
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
        $component_handler = __ComponentHandlerManager::getInstance();
        if($component_handler->hasComponentHandler($this->_view_code)) {
            $this->_component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
            $this->_event_handler = __EventHandlerManager::getInstance()->getEventHandler($this->_view_code);
            $this->_components = $this->_component_handler->getComponents();
        }
    }
    
    public function getViewCode() {
        return $this->_view_code;
    }
    
    public function restoreView() {
        if($this->_component_handler != null) {
            __ComponentHandlerManager::getInstance()->addComponentHandler($this->_component_handler);
            __EventHandlerManager::getInstance()->addEventHandler($this->_event_handler);
            $component_pool = __ComponentPool::getInstance();
            foreach($this->_components as $component) {
                $component_pool->registerComponent($component);
                unset($component);
            }
        }
            
    }
    
}
