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

class __ContextEventListener extends __EventListener {
    
    protected $_context_id = null;
    
    public function __construct($event_type, __Callback &$callback, $context_id = null) {
        parent::__construct($event_type, $callback);
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }        
        $this->_context_id = $context_id;
    }  

    public function getContextId() {
        return $this->_context_id;
    }    
    
    /**
     * This method is called by the {@link __EventDispatcher} when a new event is raised and the current {@link __EventListener} is suscribed to it.
     *
     * @param __Event $event The raised event reference
     */
    public function receiveEvent(__Event &$event) {
        if( $event instanceof __ContextEvent && $event->getContextId() == $this->getContextId() ) {
            if($this->_send_event_to_callback == true) {
                $parameters = array();
                $parameters[] =& $event;
                $this->_callback->execute($parameters);
            }
            else {
                $this->_callback->execute();
            }
        }
    }    
    
}