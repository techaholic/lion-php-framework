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
 * The RemoteService component is used to allow your javascript code to call to method defined within the server's event handler.
 * <br>
 * The remote service tag is <b>remoteservice</b><br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:remoteservice name = "client_foo" 
 *                     method = "server_foo"/>
 * 
 * </code>
 * 
 * In this example, <b>name</b> is the client function name that will be generated in order to call to the server method.<br>
 * <b>method</b> (or <b>eventHandlerMethod</b>) is the name of the method already defined within the event handler that will be executed as a result of calling the client function <i>foo</i>.<br>
 * <br>
 * We may need to define a method <i>foo</i> within our event handler:
 * i.e.
 * <code>
 *   
 *   class MyEventHandler extends __EventHandler {
 * 
 *   ...
 * 
 *       public function server_foo() {
 *           return 'hi, there';
 *       }
 * 
 *   ...
 * 
 *   }
 * 
 * </code>
 * 
 * In order to call to this method from the client side, just call to the client_foo function from your javascript code:
 * 
 * i.e.
 * <code>
 * 
 *   <script language='javascript'>
 * 
 *       //call to the remote service from the client side:
 *       client_foo();
 * 
 *   </script>
 * 
 * </code>
 * 
 * in the other hand, you are able to define a callback to be executed when the client receives the response from the server.<br>
 * This callback will receive as parameter the value returned by your server-side event handler method.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:remoteservice name = "client_foo" 
 *                     method = "server_foo" 
 *                   callback = "callback_foo"/>
 * 
 *   <script language='javascript'>
 * 
 *       function callback_foo(parameter) {
 *           alert(parameter);
 *       }
 * 
 *   </script>
 * 
 * </code>
 * This callback will show an alert popup with the value returned by the server event handler method. In our example, it will show the 'hello, there' string.<br>
 * <br>
 * <br>
 * Our recomendation is to use the javascript component in the client side, so the example could be finally as following:<br>
 * <br>
 * <code>
 * 
 *   <comp:remoteservice name = "client_foo" 
 *                     method = "server_foo" 
 *                   callback = "callback_foo"/>
 * 
 *   <comp:javascript>
 *   {literal}
 * 
 *       function callback_foo(parameter) {
 *         alert(parameter);
 *       }
 * 
 *   {/literal}
 *   </comp:javascript>
 * 
 *   <input type = "button" caption = "Call to server_foo method" 
 *                          onclick = "client_foo();"/>
 * 
 * </code>
 * 
 * See this example in action at {@link http://www.lionframework.org/components/remoteservice.html}
 * 
 * @see __JavascriptComponent
 *   
 *
 */
class __RemoteServiceComponent extends __UIComponent implements __IPoolable, __INonExpirable {

    protected $_event_handler_method = null;
    protected $_client_response_callback = null;
    protected $_last_response = null;

    public function setMethod($event_handler_method) {
        return $this->setEventHandlerMethod($event_handler_method);
    }
    
    public function setEventHandlerMethod($event_handler_method) {
        $this->_event_handler_method = $event_handler_method;
    }
    
    public function getEventHandlerMethod() {
        if($this->_event_handler_method != null) {
            $return_value = $this->_event_handler_method;
        }
        else {
            $return_value = $this->getName();
        }
        return $return_value;
    }
    
    public function setCallback($client_response_callback) {
        return $this->setClientResponseCallback($client_response_callback);
    }
    
    public function setClientResponseCallback($client_response_callback) {
        $this->_client_response_callback = $client_response_callback;
    }
    
    public function getClientResponseCallback() {
        return $this->_client_response_callback;
    }
    
    public function getLastResponse() {
        return $this->_last_response;
    }
    
    public function execute(&$parameters = null) {
        if($parameters == null) {
            $parameters = array();
        }
        else if(!is_array($parameters)) {
            $parameters = array($parameters);
        }
        $return_value = null;
        $event_handler = __EventHandlerManager::getInstance()->getEventHandler($this->_view_code);
        $event_handler_method = $this->getEventHandlerMethod();
        if(method_exists($event_handler, $event_handler_method)) {
            $return_value = call_user_func_array(array($event_handler, $event_handler_method), $parameters);
        }
        return $return_value;
    }
    
    public function isEventHandled($event_name) {
        $return_value = false;
        if(strtoupper($event_name) == 'REMOTECALL') {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function getHandledEvents() {
        return array('remotecall');
    }
    
    public function handleEvent(__UIEvent &$event) {
        $this->_last_response = $this->execute($event->getExtraInfo());
    }
    
        
}
