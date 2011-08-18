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
 * Handles a set of components created because of a view rendering.
 * 
 * @see __ComponentHandlerManager
 *
 */
class __ComponentHandler {
    
    private $_view_code;
        
    /**
     * A hash with a correspondence between component names and their internal unique identifiers.
     * Component identifiers are usefull to retrieve components directly from the component pool.
     *
     * @var array
     */
    private $_component_names = array();
    
    /**
     * Non poolable components are maintained in this array, which won't be stored into the session.
     * During the request, a non-poolable component must allow to be retrieved in the same way as poolable components
     *
     * @var array
     */
    private $_non_poolable_component_names = array();
    
    /**
     * Components to expire are components that already exists in the session but for any reason have not been rendered, 
     * which means that will be removed (expired) from the component pool
     *
     * @var array
     */
    private $_components_to_expire = array();
    
    private $_dirty = false;
    
    protected function _initialize() {
        $this->_components_to_expire = $this->_component_names;
        $this->_non_poolable_component_names = array();
    }
    
    public function __wakeup() {
        $this->_initialize();
    }
    
    /**
     * Constructor method
     *
     * @param string $view_code The view code associated to the current __ComponentHandler instance
     */
    public function __construct($view_code) {
        $this->setViewCode($view_code);
        $this->_initialize();
    }
        
    /**
     * Sets a view code for current __ComponentHandler instance
     *
     * @param string $view_code The view code
     */
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    /**
     * Gets the view code associated to the current __ComponentHandler instance
     *
     * @return string
     */
    public function getViewCode() {
        return $this->_view_code;
    }
    
    /**
     * Mark a component as rendered in the current view instance. All components that are not rendered
     * are not marked for expiration, which means that they will be removed once the view is rendered
     *
     * @param string $component_name
     * @param mixed $index (optional, the component index in case of array of components)
     */
    public function markComponentAsRendered($component_name, $index = null) {
        if(key_exists($component_name, $this->_components_to_expire)) {
            if($index === null) {
                unset($this->_components_to_expire[$component_name]);
            }
            else if(is_array($this->_components_to_expire[$component_name]) && key_exists($index, $this->_components_to_expire[$component_name])) {
                unset($this->_components_to_expire[$component_name][$index]);            
            }
        }
    }
    
    public function expireNotRenderedComponents() {
        foreach($this->_components_to_expire as $component_name => $component_id_or_component_items) {
            if(is_array($component_id_or_component_items)) {
                foreach($component_id_or_component_items as $component_index => $component_id) {
                    $component = __ComponentPool::getInstance()->getComponent($component_id);
                    if( !$component instanceof __INonExpirable ) {
                        __ComponentPool::getInstance()->unregisterComponent($component_id); 
                        unset($this->_component_names[$component_name][$component_index]);
                    }                    
                }
            }
            else {
                $component = __ComponentPool::getInstance()->getComponent($component_id_or_component_items);
                if( !$component instanceof __INonExpirable ) {
                    __ComponentPool::getInstance()->unregisterComponent($component_id_or_component_items);                
                    unset($this->_component_names[$component_name]);
                }                    
            }
        }
        //empty components to expire (to remove non expirable components):
        $this->_components_to_expire = array();
    }
    
    /**
     * Checks if current page contains component with the given name.
     *
     * @param string $component_name The component name
     * @param string $index In case of array, the component index within the array
     * @return bool
     */
    public function hasComponent($component_name, $index = null) {
        $return_value = false;
        $component_id = $this->getComponentId($component_name, $index);
        if($component_id !== null) {
            //getComponentId returns an array of ids if no index was specified for an array of component
            //in that case, just will check if the first identifier within the array exists
            if(is_array($component_id)) {
                $component_id = reset($component_id);
            }
            if(__ComponentPool::getInstance()->hasComponent($component_id)) {
                $return_value = true;
            }
            else {
                //if the component pool does not have the component, remove it from internal mapping hash:
                if($index !== null && is_array($this->_component_names[$component_name])) {
                    unset($this->_component_names[$component_name][$index]);
                }
                else {
                    unset($this->_component_names[$component_name]);
                }
            }
        }
        return $return_value;
    }
    
    /**
     * Gets a component by name.
     *
     * @param string $component_name The component name
     * @param string $index In case of array, the component index within the array
     * @return __IComponent
     */
    public function &getComponent($component_name, $index = null) {
        $component_id = $this->getComponentId($component_name, $index);
        if($component_id == null) {
            if($index !== null) {
                throw __ExceptionFactory::getInstance()->createException('ERR_COMPONENT_INDEX_NOT_FOUND', array($component_name, $index));
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_COMPONENT_NOT_FOUND', $component_name);
            }
        }
        else if(is_array($component_id)) {
            $return_value = array();
            foreach($component_id as $component_index => $component_item_id) {
                $return_value[$component_index] = __ComponentPool::getInstance()->getComponent($component_item_id);
            }
        }
        else {
            $return_value = __ComponentPool::getInstance()->getComponent($component_id);
        }
        return $return_value;
    }

    /**
     * Gets all the components associated to current component handler
     *
     * @return array
     */
    public function &getComponents() {
        $return_value = array();
        $component_ids = $this->getComponentIds();
        foreach($component_ids as $component_id) {
            $return_value[$component_id] =& __ComponentPool::getInstance()->getComponent($component_id);
        }
        return $return_value;
    }
    
    public function &getComponentsByClass($component_class) {
        $components = $this->getComponents();
        $return_value = array();
        foreach($components as &$component) {
            if($component instanceof $component_class) {
                $return_value[] =& $component;
            }
        }
        return $return_value;
    }
    
    /**
     * Register a component as part of the current page
     *
     * @param __IComponent $component The component to register to
     */
    public function registerComponent(__IComponent &$component) {
        if( $component instanceof __IPoolable && $component->getPersist() ) {
            $names_array =& $this->_component_names;
        }
        else {
            $names_array =& $this->_non_poolable_component_names;
        }
        if($component->getIndex() !== null) {
            if(!key_exists($component->getName(), $names_array)) {
                $names_array[$component->getName()] = array();
            }
            else if(!is_array($names_array[$component->getName()])) {
                throw __ExceptionFactory::getInstance()->createException('ERR_INDEX_NOT_EXPECTED_FOR_COMPONENT', array($component->getIndex(), $component->getName()));
            }
            $names_array[$component->getName()][$component->getIndex()] = $component->getId();
        }
        else {
            $names_array[$component->getName()] = $component->getId();
        }
        //set the view code:
        $component->setViewCode($this->_view_code);
        //pool the component:
        __ComponentPool::getInstance()->registerComponent($component);
    }
    
    public function unregisterComponent($component_name, $index = null) {
        if(key_exists($component_name, $this->_component_names)) {
            if($index === null) {
                $component_id = $this->_component_names[$component_name];
                __ComponentPool::getInstance()->unregisterComponent($component_id);                
                unset($this->_component_names[$component_name]);
            }
            else if(is_array($this->_component_names[$component_name]) && key_exists($index, $this->_component_names[$component_name])) {
                $component_id = $this->_component_names[$component_name][$index];
                __ComponentPool::getInstance()->unregisterComponent($component_id);                
                unset($this->_component_names[$component_name][$index]);
            }
        }
    }
    
    /**
     * Gets the component identifier that corresponds with a given component name.
     *
     * @param string $component_name The component name
     * @param string $index In case of array, the component index within the array
     * @return string The component identifier
     */
    public function getComponentId($component_name, $index = null) {
        $return_value = null;
        if(key_exists($component_name, $this->_component_names)) {
            if($index === null) {
                $return_value = $this->_component_names[$component_name];
            }
            else if(is_array($this->_component_names[$component_name]) && key_exists($index, $this->_component_names[$component_name])) {
                $return_value = $this->_component_names[$component_name][$index];            
            }
        }
        else if(key_exists($component_name, $this->_non_poolable_component_names)) {
            if($index === null) {
                $return_value = $this->_non_poolable_component_names[$component_name];
            }
            else if(is_array($this->_non_poolable_component_names[$component_name]) && $index != null && key_exists($index, $this->_non_poolable_component_names[$component_name])) {
                $return_value = $this->_non_poolable_component_names[$component_name][$index];            
            }
        }
        return $return_value;
    }

    public function updateComponentIndex($component_name, $old_index, $new_index) {
        if(key_exists($component_name, $this->_component_names) && key_exists($old_index, $this->_component_names[$component_name])) {
            $component_id = $this->_component_names[$component_name][$old_index];
            unset($this->_component_names[$component_name][$old_index]);
            $this->_component_names[$component_name][$new_index] = $component_id;
        }
    }
    
    public function hasPoolableComponents() {
        $return_value = true; //by default
        if(count($this->_component_names) == 0) {
            $return_value = false;
        }
        return $return_value;
    }
    
    /**
     * Gets all the component identifiers associated to current component handler
     *
     * @return array
     */
    public function getComponentIds() {
        return $this->_array_values_recursive($this->_component_names);
    }
    
    /**
     * Removes all the component referenced by the current instance
     * from the component pool. Also removes the references
     *
     */
    public function freeComponents() {
        foreach($this->_component_names as $component) {
            if(is_array($component)) {
                foreach($component as $component_item) {
                    __ComponentPool::getInstance()->unregisterComponent($component_item);
                }
            }
            else if(is_string($component)) {
                __ComponentPool::getInstance()->unregisterComponent($component);
            }
        }
        $this->_component_names = array();
    }
    
    
    
    /**
     * Resets values for components implementing {@link __IValueHolder}
     * Also reset any validation against them
     *
     */
    public function resetValueHolders() {
        foreach($this->_component_names as $component) {
            if(is_array($component)) {
                foreach($component as $component_item) {
                    $component = __ComponentPool::getInstance()->getComponent($component_item);
                    if( $component instanceof __IValueHolder ) {
                        $component->reset();
                    }
                    unset($component);
                }
            }
            else if(is_string($component)) {
                $component = __ComponentPool::getInstance()->getComponent($component);
                if( $component instanceof __IValueHolder ) {
                    $component->reset();
                }
                unset($component);                
            }
        }
    }
    
    /**
     * This method gets a flat array of all the values contained in a given array
     *
     * @param array $array
     * @return array
     */
    private function _array_values_recursive(array $array) {
       $return_value = array();
       foreach ($array as $value) {
               if (is_array($value)) {
                   $return_value = array_merge($return_value, $this->_array_values_recursive($value));
               }
               else {
                   $return_value[] = $value;
               }
       }
       return $return_value;
    }
    
    public function isDirty() {
        return $this->_dirty;
    }
    
    public function setDirty($dirty) {
        $this->_dirty = (bool) $dirty;
    }
    
    
}