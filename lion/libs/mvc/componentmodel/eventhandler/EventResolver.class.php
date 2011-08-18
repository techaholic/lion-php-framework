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

final class __EventResolver {
    
    static private $_instance = null;
    
    private $_event = null;
    
    private function __construct() {
        
    }
    
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventResolver();
        }
        return self::$_instance;
    }
    
    public function resolveEvent() {
        if($this->_event == null) {
            $request = __FrontController::getInstance()->getRequest();
            if($request->hasParameter('event')) {
                $event_json_string = stripslashes($request->getParameter('event'));
                $event = json_decode($event_json_string, true);
                $component_id = $event['componentId'];
                $event_name   = $event['eventName'];
                $extra_info   = $event['extraInfo'];
                if(__ComponentPool::getInstance()->hasComponent($component_id)) {
                    $component    = __ComponentPool::getInstance()->getComponent($component_id);
                    //create the event instance:
                    $this->_event = new __UIEvent($event_name, $extra_info, $component);
                }
            }
        }
        return $this->_event;
    }
    
}