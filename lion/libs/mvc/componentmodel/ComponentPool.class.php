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

final class __ComponentPool {
    
    static private $_instance = null;
    
    /**
     * Components array
     *
     * @var array
     */
    private $_components = array();
    
    private $_non_poolable_components = array();
    
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__ComponentPool::_components')) {
            $this->_components =& $session->getData('__ComponentPool::_components');
        }
        else {
            $session->setData('__ComponentPool::_components', $this->_components);
        }
    }

    /**
     * Gets a reference to the singleton {@link __ComponentPool} instance
     *
     * @return __ComponentPool
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ComponentPool();
        }
        return self::$_instance;
    }
    
    /**
     * Register a component in the current pool. 
     * If there is already a component with same identifier in the pool, it will be unregister and reasigned.
     * 
     * @param __IComponent &$component The component to set to.
     */
    public function registerComponent(__IComponent &$component) {
        if($component instanceof __IPoolable && $component->getPersist()) {
            $this->_poolComponent($component);
        }
        else {
            $this->_non_poolable_components[$component->getId()] =& $component;
        }
    }

    /**
     * Pools a component. This is a private method calls by the {@link registerComponent} after
     * validating that the component is poolable
     *
     * @param __IComponent $component The component to pool to
     */
    private function _poolComponent(__IComponent &$component) {
        $component_id = $component->getId();
        $this->unregisterComponent($component_id);
        $this->_components[$component_id] =& $component;
        __ClientNotificator::getInstance()->setDirty($component);
    }
    
    /**
     * Unregister a given component
     *
     * @param __IComponent $component The component to unregister from
     */
    public function unregisterComponent($component_id) {
        if(key_exists($component_id, $this->_components)) {
            $component = $this->_components[$component_id];
            if( $component instanceof __ICompositeComponent && __EventHandlerManager::getInstance()->hasEventHandler($component_id)) {
                __EventHandlerManager::getInstance()->getEventHandler($component_id)->free();
            }
            if($component->hasContainer()) {
                $container = $component->getContainer();
                $container->removeComponent($component_id);
            }
            unset($this->_components[$component_id]);
            unset($component);
        }
    }
    
    /**
     * Checks if exists a component with a given identifier
     *
     * @param string $component_id The component identifier
     * @return bool
     */
    public function hasComponent($component_id) {
        return key_exists($component_id, $this->_components) || key_exists($component_id, $this->_non_poolable_components);
    }

    /**
     * Gets a component associated with a given identifier
     *
     * @param string $component_id The component identifier
     * @return __IComponent
     */
    public function &getComponent($component_id) {
        $return_value = null;
        if(key_exists($component_id, $this->_components)) {
            $return_value =& $this->_components[$component_id];
            if($return_value != null) {
                __ClientNotificator::getInstance()->setDirty($return_value);
            }
        }
        else if(key_exists($component_id, $this->_non_poolable_components)) {
            $return_value =& $this->_non_poolable_components[$component_id];
        }
        return $return_value;
    }
    
}

