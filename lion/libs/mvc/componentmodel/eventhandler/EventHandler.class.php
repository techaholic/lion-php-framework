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
 * This class is an implementation of the {@link __IEventHandler}, 
 * a class in charge of handling UI component events in a view.
 * 
 */
class __EventHandler implements __IEventHandler {

    protected $_view_code = null;
    protected $_parent_view_code = null;
    protected $_actionbox_id = null;
    
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    public function getViewCode() {
        return $this->_view_code;
    }
    
    /**
     * Sets the parent view code, corresponding to a parent viewport (if applicable)
     * i.e., if current view is contained in an actionbox, the parent viewcode is the view containing the actionbox
     *
     * @param string $parent_view_code
     */
    public function setParentViewCode($parent_view_code) {
        $this->_parent_view_code = $parent_view_code;
    }
    
    /**
     * Gets the parent view code associated to current event handler (if applicable)
     * 
     * @return string
     *
     */
    public function getParentViewCode() {
        return $this->_parent_view_code;
    }
    
    /**
     * Set the identifier of the {@link __ActionBoxComponent} where current view has been rendered in (if applicable)
     *
     * @param string $actionbox_id
     */
    public function setContainerActionBoxId($actionbox_id) {
        $this->_actionbox_id = $actionbox_id;
    }
    
    /**
     * Get the identifier of the {@link __ActionBoxComponent} where current view has been rendered in (if applicable)
     *
     * @return string
     */
    public function getContainerActionBoxId() {
        return $this->_actionbox_id;
    }
    
    /**
     * Get the container actionbox where the current view has been rendered in (if applicable)
     *
     * @return __ActionBoxComponent
     */
    public function &getContainerActionBox() {
        $return_value = null;
        if($this->_actionbox_id != null && __ComponentPool::getInstance()->hasComponent($this->_actionbox_id)) {
            $return_value = __ComponentPool::getInstance()->getComponent($this->_actionbox_id);
        }
        return $return_value;
    }
    
    public function refresh() {
        if($this->_actionbox_id != null && __ComponentPool::getInstance()->hasComponent($this->_actionbox_id)) {
            $actionbox = __ComponentPool::getInstance()->getComponent($this->_actionbox_id);
            $actionbox->refresh();
        }
    }
    
    
    /**
     * Get the event handler correponding to the parent view (if applicable)
     * 
     * @return __IEventHandler
     *
     */
    public function getParentEventHandler() {
        $return_value = null;
        if($this->_parent_view_code != null && __EventHandlerManager::getInstance()->hasEventHandler($this->_parent_view_code)) {
            $return_value = __EventHandlerManager::getInstance()->getEventHandler($this->_parent_view_code);
        }
    }

    /**
     * Get the component that corresponds with a given name
     *
     * @param string $component_name The component's name
     * @return __IComponent or null if not found
     */
    public function &getComponent($component_name, $component_index = null) {
        return __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code)->getComponent($component_name, $component_index);
    }
    
    public function &getComponentHandler() {
        return __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
    }
    
    public function hasComponent($component_name, $component_index = null) {
        return __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code)->hasComponent($component_name, $component_index);
    }
    
    public function &getComponents() {
        return __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code)->getComponents();
    }
    
    public function &getComponentsByClass($component_class) {
        return __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code)->getComponentsByClass($component_class);
    }    
    
    public function resetValueHolders() {
        __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code)->resetValueHolders();
    }
 
    /**
     * Handles an UI event by executing a method associated to the given event
     *
     * @param __UIEvent $event The event to handle
     * @return mixed
     */
    public function handleEvent(__UIEvent &$event) {
        $return_value    = null;
        //get event info:
        $component       = $event->getComponent();
        $event_name      = $event->getEventName();
        $extra_info      = $event->getExtraInfo();
        if($event_name != 'beforeRender' && $event_name != 'afterRender' && $event_name != 'create') {
            __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code)->setDirty(true);
        }
        try {
            //handle an event associated to a component:
            if($component != null) {
                //get component name:
                $component_name  = $component->getName();

                //if the component can handle the event, will call to the component event handler
                if($component->isEventHandled($event_name)) {
                    $component->handleEvent($event);            
                }
                //after that, even if the component has handle the event, will execute the event handler method: 
                //1. resolve the concrete handler method:
                $event_handler_method = $this->_getEventHandlerMethod($event_name, $component_name);
                //2. call to the concrete handler method:
                if(method_exists($this, $event_handler_method)) {
                    $return_value = call_user_func(array($this, $event_handler_method), $event);
                    switch(strtoupper($event_name)) {
                        case 'VALIDATE':
                            //first, let's return true if no __ValidationException has been sent
                            $return_value = true;
                            //and reset in case of, any validation component associated to the event's component
                            if(is_array($extra_info) && key_exists('validationRule', $extra_info)) {
                                $validation_rule = __ComponentPool::getInstance()->getComponent($extra_info['validationRule']);
                                $validation_rule->setErrorMessage(null);
                            }
                            break;
                    }
                }
            }
        }
        catch (__ValidationException $validation_exception) {
            $return_value = false; //set to false, because a validation exception has been raised
            if(is_array($extra_info) && key_exists('validationRule', $extra_info)) {
                $validation_rule = __ComponentPool::getInstance()->getComponent($extra_info['validationRule']);
                $validation_rule->setErrorMessage($validation_exception->getMessage());
            }
        }
        return $return_value;
    }
    
    public function isEventHandled($event_name, $component_name) {
        $component = $this->getComponent($component_name);
        if($component != null) {
            if(is_array($component)) {
                $component = reset($component);
            }
            if($component->isEventHandled($event_name)) {
                return true;
            }
        }
        $event_handler_method = $this->_getEventHandlerMethod($event_name, $component_name);
        if(method_exists($this, $event_handler_method)) {
            return true;
        }
        return false;
    }
    
    public function getComponentHandledEvents($component_name) {
        $return_value = array();
        $class_methods = get_class_methods($this);
        foreach($class_methods as $class_method) {
            $event_name = null;
            if(preg_match('/^' . $component_name . '_([^_]+)$/i', $class_method, $event_name)) {
                $return_value[] = $event_name[1];
            }
        }
        //now will merge with the component handled methods:
        $component = $this->getComponent($component_name);
        if($component != null) { 
            if(is_array($component)) {
                $component = reset($component);
            }
            $return_value = array_merge($return_value, $component->getHandledEvents());
        }
        return $return_value;
    }

    protected function _getEventHandlerMethod($event_name, $component_name) {
        $return_value = $component_name . '_' . $event_name;
        return $return_value;
    }
    
    public function free() {
        __ComponentHandlerManager::getInstance()->freeComponentHandler($this->getViewCode());
    }
    
    public function create() {
        //nothing to do
    }
    
    public function beforeRender() {
        //nothing to do
    }

    public function afterRender() {
        //nothing to do
    }
    
    
}