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
 * This class represents an event fired from UI
 *
 */
final class __UIEvent {
    
    protected $_component_id = null;
    protected $_event_name = null;
    protected $_extra_info = null;
    
    public function __construct($event_name, $extra_info = null, __IComponent &$component = null) {
        $this->setEventName($event_name);
        if($extra_info != null) {
            $this->setExtraInfo($extra_info);
        }
        if($component != null) {
            $this->setComponent($component);
        }
    }
    
    public function setEventName($event_name) {
        if(!empty($event_name)) {
            $this->_event_name = $event_name;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Need an event name (an empty value as the event name has been received)');
        }
    }
    
    public function getEventName() {
        return $this->_event_name;
    }
    
    public function setExtraInfo($extra_info) {
        $this->_extra_info = $extra_info;
    }

    public function getExtraInfo() {
        return $this->_extra_info;
    }
        
    public function setComponent(__IComponent &$component) {
        $this->_component_id = $component->getId();
    }
    
    public function &getComponent() {
        $return_value = null;
        if(!empty($this->_component_id)) {
            $return_value = __ComponentPool::getInstance()->getComponent($this->_component_id);
        }
        return $return_value;
    }
    
}