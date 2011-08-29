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
 * @package    Event
 * 
 */


/**
 * This class handles events and notify to all subscribed observers when an event is raised
 * 
 */
class __EventDispatcher {
    
    private $_event_listeners = array();

    private static $_instance = null;
        
    private function __construct() {
        
    }
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventDispatcher();
        }
        return self::$_instance;
    }
    
    /**
     * This method perform a broadcast for a concrete event
     *
     */
    public function broadcastEvent(__Event &$event)
    {
        $event_type = $event->getEventType();       
        if (key_exists($event_type, $this->_event_listeners)) {
            foreach ($this->_event_listeners[$event_type] as &$event_listener) {
                $event_listener->receiveEvent($event);
            }
        }
    }
  
    public function registerEventCallback($event_type, __Callback &$callback, $context_id = null) {
        if($context_id == null) {
            $event_listener = new __EventListener($event_type, $callback);
        }
        else {
            $event_listener = new __ContextEventListener($event_type, $callback, $context_id);
        }
        $this->registerEventListener($event_listener);
    }
    
    /**
     * This method registers an observer for specified events notifications.
     * A reference to an observer will be stored for each event type to observe
     *
     * @param __EventListener $event_listener An {@link __EventListener} to register
     */
    public function registerEventListener(__EventListener &$event_listener) {
        $event_type = $event_listener->getEventToListen();
        if(!key_exists($event_type, $this->_event_listeners)) {
            $this->_event_listeners[$event_type] = array();
        }
        $this->_event_listeners[$event_type][] =& $event_listener;
    }
    
    /**
     * This method unregister a observer. Next events won't be notified to unregister observer
     *
     * @param __EventListener $event_listener The {@link __EventListener} to be unregistered
     */
    static public function unregisterEventListener(__EventListener &$event_listener) {
        $event_type = $event_listener->getEventToListen();
        if(key_exists($event_type, $this->_event_listeners)) {
            for ($i = 0; $i < count($this->_event_listeners[$event_type]); $i++) {
                if($this->_event_listeners[$event_type][$i] === $event_listener) {
                    array_splice($this->_event_listeners[$event_type], $i, 1);
                    return;
                }
            }
        }
    }
    
}