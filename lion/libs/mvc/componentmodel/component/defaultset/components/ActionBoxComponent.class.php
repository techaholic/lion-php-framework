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
 * ActionBox is a component containing the result of executing a controller action.
 * 
 * It accepts 3 main parameters: a controller, an action (optional, if not given, it executes the default controller's action) and a list of parameters (optional). 
 * The actionbox renders the result of executing the given action with the given parameters.<br>
 * <br>
 * The ActionBox tag is "actionbox"<br>
 * <br>
 * i.e.
 * <code>
 *   
 *   <comp:actionbox controller = "invoiceList" 
 *                   parameters = "list_type=simple&page_number=1" />
 * 
 * </code>
 * <br>
 * To improve the performance, by default an actionbox does not render the same action with the same parameters twice.<br>
 * It caches the latest response and serves it. However, we can force that execution by setting the autorefresh property to true.<br>
 * <br>
 * Autorefresh = true will force the action execution for each new request (ajax requests not included).<br>
 * <br>
 * Last, parameters set to the actionbox will be sent to the controller within the local request, not the global request.<br>
 * To recover the local request, we must ask the {@link __ActionDispatcher}<br>
 * <br>
 * i.e.
 * <code>
 *  
 *   //get the local request
 *   $request = __ActionDispatcher::getInstance()->getRequest();
 * 
 * </code>
 * <br>
 *
 */
class __ActionBoxComponent extends __UIComponent implements __IPoolable {
    
    protected $_action      = null;
    protected $_controller  = null;
    protected $_parameters  = array();
    protected $_autorefresh = true;
    protected $_dirty       = false;
    protected $_latest_response = "";
    protected $_synchronize_client = false;
    protected $_refresh_actionbox = false;
    
    public function __wakeup() {
        if (__Client::getInstance()->getRequestType() != REQUEST_TYPE_XMLHTTP && $this->_autorefresh == true) {
            $this->_refreshed = false; //set as not refreshed
        }
    }
    
    /**
     * Set the action code corresponding to the action to be executed to.
     *
     * @param string $action
     */
	public function setAction($action) {
	    if($this->_action != $action) {
	       $this->_action = $action;
	       $this->_dirty = true;
	    }
	}
	
	/**
	 * Get the action code to be executed to.
	 *
	 * @return string
	 */
	public function getAction() {
	    return $this->_action;
	}
	
	/**
	 * Set the autorefresh property
	 *
	 * @param bool $autorefresh
	 */
	public function setAutorefresh($autorefresh) {
	    $this->_autorefresh = $this->_toBool($autorefresh);
	}
	
    /**
     * Get the autorefresh property
     *
     * @return bool
     */	
	public function getAutorefresh() {
	    return $this->_autorefresh;
	}
	
	/**
	 * Set the controller code corresponding to the controller to be executed to
	 *
	 * @param string $controller
	 */
	public function setController($controller) {
	    if($this->_controller != $controller) {
	       $this->_controller = $controller;
	       $this->_dirty = true;
	    }
	}
	
	/**
	 * Get the controller code corresponding to the controller to be executed to
	 *
	 * @return string
	 */
	public function getController() {
	    return $this->_controller;
	}
	
	/**
	 * Add a parameter to be sent to the controller as part of the request
	 *
	 * @param string $parameter_name
	 * @param mixed $parameter_value
	 */
	public function addParameter($parameter_name, $parameter_value) {
	    if(!key_exists($parameter_name, $this->_parameters) || $this->_parameters[$parameter_name] !== $parameter_value) {
	       $this->_parameters[$parameter_name] = $parameter_value;
	       $this->_dirty = true;
	    }
	}
	
	/**
	 * Set all the parameters in one-time call. Parameters can be either an array of pair key,value, or a comma-separated list of pairs
	 *
	 * @param mixed $parameters
	 */
	public function setParameters($parameters) {
	    if(is_string($parameters)) {
	        $parameters_str = $parameters;
	        parse_str($parameters_str, $parameters);
	    }
	    if(is_array($parameters)) {
    	    //check if set dirty:
    	    if(count($parameters) != count($this->_parameters)) {
    	        $this->_dirty = true;
    	    }
    	    else {
        	    foreach($parameters as $parameter_name => $parameter_value) {
            	    if(!key_exists($parameter_name, $this->_parameters) || $this->_parameters[$parameter_name] !== $parameter_value) {
            	       $this->_dirty = true;
            	    }
        	    }
    	    }
    	    //sets the parameters:
    	    $this->_parameters = $parameters;
	    }
	}
	
	/**
	 * Get an array of parameters set to be sent within the local request to the action
	 *
	 * @return array
	 */
	public function getParameters() {
	    return $this->_parameters;
	}
	
	/**
	 * Reset the parameters associated to the action execution
	 *
	 */
	public function clearParameters() {
	    if(count($this->_parameters) > 0) {
	       $this->_parameters = array();
	       $this->_dirty = true;
	    }
	}
	
	/**
	 * Alias of {@link setResponse()}
	 *
	 * @param string $text
	 */
	public function setContent($text) {
	    $this->setResponse($text);
	}
	
	/**
	 * Alias of {@link getResponse()}
	 *
	 * @return string
	 */
	public function getContent() {
	    return $this->getResponse();
	}
	
	/**
	 * Set the response that the current actionbox must render to the client.
	 * This response is set as a result of the action execution, but can be set manually by calling this method
	 *
	 * @param string $text
	 */
	public function setResponse($text) {
	    $this->_action = null;
	    $this->_controller = null;
	    $this->_latest_response = $text;
	    $this->_dirty = false;
	}
	
	/**
	 * Get the response that the current actionbox will be render to the client
	 *
	 * @return string
	 */
	public function getResponse() {
        $this->executeAction();
	    return $this->_latest_response;
	}

	/**
	 * Executes the action associated to the current actionbox, setting the
	 * result to the actionbox response.
	 * 
	 * If autorefresh property is set to true (default value), the actionbox will be executed in each non-ajax request
	 * even if no new control values has been set (same action, same controller and same parameters)
	 * 
	 * @see __ActionBoxComponent::getResponse
	 *
	 */
	public function executeAction($force_execution = false) {
        if($force_execution || $this->_canRefresh()) {
            $action_identity = new __ActionIdentity($this->_controller, $this->_action);
            $request = new __HttpRequest();
            $request->fromArray($this->_parameters);
            $request->addParameter('PARENT_VIEW_CODE', $this->getViewCode());
            $request->addParameter('ACTIONBOX_ID', $this->getId());
            $response = new __HttpResponse();
            $response = __ActionDispatcher::getInstance()->dispatch($action_identity, $request, $response);
            $response_content = $response->getContent();
            if($this->_latest_response != $response_content) {
                $this->_latest_response = $response_content;
                $this->_synchronize_client = true;
            }
            $this->_dirty = false;
            $this->_refreshed = true;
        }
	}
	
	protected function _canRefresh() {
	    $return_value = false; //by default
	    //only refresh if at least it has been set a controller or action:
	    if($this->_controller != null || $this->_action != null) {
    	    if($this->_dirty == true) { //if dirty, refresh!
    	        $return_value = true;
    	    }
    	    else if(__Client::getInstance()->getRequestType() != REQUEST_TYPE_XMLHTTP && $this->_autorefresh == true && !$this->_refreshed) {
    	        $return_value = true;
    	    }
	    }
	    return $return_value;
	}
	
	/**
	 * Alias of executeAction
	 *
	 */
	public function execute($force_execution = false) {
	    $this->executeAction($force_execution);
	}
	
	/**
	 * Forces the execution of the current action, refreshing the results in the action box
	 *
	 */
	public function refresh() {
	    $this->executeAction(true);
	}
	
	/**
	 * For interal usage only, this method checks if the current response can be rendered to the client
	 *
	 * @return bool
	 */
	public function isUnsynchronized() {
	    $return_value = $this->_synchronize_client;
	    $this->_synchronize_client = false;
	    return $return_value; 
	}
	
	/**
	 * Clear the actionbox content
	 *
	 */
	public function clear() {
	    $this->_action = null;
	    $this->_controller = null;
	    $this->_latest_response = "";
	    $this->_dirty = false;
	}
	
}
