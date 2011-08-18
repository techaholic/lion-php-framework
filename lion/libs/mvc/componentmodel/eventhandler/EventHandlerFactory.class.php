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

class __EventHandlerFactory {
    
    private static $_instance = null;
    
    private function __construct() {
    }
        
    /**
     * Enter description here...
     *
     * @return __EventHandlerFactory
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventHandlerFactory();
        }
        return self::$_instance;
    }
    
    public function &createEventHandler($view_code) {
        $return_value = null;
        $view = __ViewResolver::getInstance()->getView($view_code);
        $event_handler_class = $view->getEventHandlerClass();
        if($event_handler_class != null) {
            $return_value = new $event_handler_class();
            if($return_value instanceof __IEventHandler) {
                $return_value->setViewCode($view_code);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Wrong event handler class: ' . $event_handler_class . '. Must implement the __IEventHandler interface.');
            }
        }
        return $return_value;
    }
    
}