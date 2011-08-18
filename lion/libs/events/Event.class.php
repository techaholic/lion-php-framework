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


class __Event {
    protected $_raiser_object = null;
    protected $_event_type    = null;
    protected $_parameters    = array();
    
    /**
     * This is the constructor of __Event instance.
     * A __Event instance represent an event (normally it's created when it's needed to be raised).<br>
     * Native LION event types are:<br>
     *  - EVENT_ON_SESSION_START: It's raised when a session start
     *  - EVENT_ON_SESSION_FINISH: It's raised when a session finish
     *  - EVENT_ON_REQUEST_START: It's raised for each new request
     *  - EVENT_ON_REQUEST_FINISH: It's raised when a request execution has been finished
     *  - EVENT_ON_LOCALE_CHANGE: It's raised when Locale configuration has been altered.<br>
     * 
     * NOTE: An application can create and also handle his owns event types (listed events are handled by LION).<br>
     * 
     * @param mixed &$raiser_object The object that raiser the event
     * @param integer $event_type The event type
     * @param array $parameters
     */
    public function __construct(&$raiser_object, $event_type) {
        $this->_raiser_object =& $raiser_object;
        $this->_event_type    = $event_type;
    }
    
    /**
     * This method returns a reference to the object that has raised current event.
     * Normally, this is the object that has created current instance
     *
     * @return mixed The object that has raised current event
     */
    public function &getRaiserObject() {
        return $this->_raiser_object;
    }
    
    /**
     * This method returns a code that identify the event type
     *
     * @return integer A code that identify the event type
     */
    public function getEventType() {
        return $this->_event_type;
    }

    public function setParameters(array $parameters) {
        $this->_parameters = $parameters;
    }
    
    /**
     * This method returns all parameters associated to current event.
     *
     * @return array An array with all associated parameters
     */
    public function getParameters() {
        return $this->_parameters;     
    }
    
}