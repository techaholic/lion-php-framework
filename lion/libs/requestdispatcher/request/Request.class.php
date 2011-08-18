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
 * @package    Request
 * 
 */


/**
 * Represents a client request and provides common methods to all request types.
 * 
 * Note that children classes represent concrete request types (requests by command line, by http, ...)
 * 
 * <code>
 * 
 * //Get the request that the front controller is dispatching (usually, the client request):
 * $request = __FrontController::getInstance()->getRequest();
 * 
 * //Get the request that the __ActionDispatcher is ussing to execute the current action (can differ from the client request):
 * $request = __ActionDispatcher::getInstance()->getRequest();
 * 
 * //Check if page_number parameter has been specified:
 * if($request->hasParameter('page_number')) {
 *    //Get the page number:
 *    $page_number = $request->getParameter('page_number');
 * }
 * 
 * </code>
 * 
 */
abstract class __Request implements __IRequest {

    protected $_request_method = REQMETHOD_NONE;
    protected $_requested_parameters = array( REQMETHOD_ALL => array() );
        
    public function setRequestMethod($request_method) {
        $this->setMethod($request_method);
    }
    
    public function setMethod($request_method) {
        $this->_request_method = $request_method;
    }
    
    public function getRequestMethod() {
        return $this->getMethod();
    }
    
    /**
     * Returns the last used requested method. i.e. GET or POST
     *
     * @return integer A numeric value that represent a valid request method
     */
    public function getMethod() {
    	return $this->_request_method;
    }    
    
    /**
     * Alias of addParameter
     *
     * @param string $parameter_name A name that identify the parameter to set to
     * @param string $parameter_value The value of the parameter
     */
    public function setParameter($parameter_name, $parameter_value, $request_method = null) {
        $this->addParameter($parameter_name, $parameter_value, $request_method);
    }
    
    /**
     * Adds a request parameter (as a pair [name, value]) to the current Request instance
     * 
     * @param string $parameter_name A name that identify the parameter to set to
     * @param string $parameter_value The value of the parameter
     */
    public function addParameter($parameter_name, $parameter_value, $request_method = null) {
        $parameter_name = strtolower($parameter_name);
        $this->_requested_parameters[REQMETHOD_ALL][$parameter_name] = $parameter_value;
        if($request_method == null) {
            $request_method = $this->getMethod();
        }
        if(!key_exists($request_method, $this->_requested_parameters)) {
            $this->_requested_parameters[$request_method] = array();
        }
        $this->_requested_parameters[$request_method][$parameter_name] = $parameter_value;
    }

    /**
     * Populates request parameters from a given associated array
     *
     * @param array $parameters
     */
    public function fromArray(array $parameters) {
        $this->_requested_parameters = array();
        $parameters = array_change_key_case($parameters, CASE_LOWER);
        $this->_requested_parameters[REQMETHOD_ALL] = $parameters;
        $request_method = $this->getMethod();
        $this->_requested_parameters[$request_method] = $parameters;
    }
    
    public function toArray($request_method = null) {
        if($request_method != null) {
            if(key_exists($request_method, $this->_requested_parameters)) {
                $return_value = $this->_requested_parameters[$request_method];
            }
            else {
                throw __ExceptionFactory::createException('Unknow request method: ' . $request_method);
            }
        }
        else {
            $return_value = $this->_requested_parameters[REQMETHOD_ALL];
        }
        return $return_value;
    }
    
    /**
     * Returns a specified requested parameter value
     *
     * @param string $parameter_name The parameter name
     * @param integer $request_method The request method to retrieve the parameter from
     * @return string The value of the parameter or null if doesn't exist
     */
    public function getParameter($parameter_name, $request_method = null) {
        $parameter_name = strtolower($parameter_name);
        if($request_method == null) {
            $request_method = REQMETHOD_ALL;
        }
    	$return_value = null;
    	if(key_exists($request_method, $this->_requested_parameters) && key_exists($parameter_name, $this->_requested_parameters[$request_method])) {
        	$return_value = $this->_requested_parameters[$request_method][$parameter_name];
    	}
    	return $return_value;
    }
    
    /**
     * Returns an array with all parameters for current Request instance
     *
     * @return array An array of all parameters
     */
    public function getAllParameters() {
        return $this->_requested_parameters[REQMETHOD_ALL];
    }

    /**
     * This method is an alias of {@link getAllParameters} method
     *
     * @return array An array of all parameters
     */
    public function getParameters($request_method = null) {
        $return_value = null;
        if($request_method == null) {
            $return_value = $this->getAllParameters();
        }
        else {
            if(key_exists($request_method, $this->_requested_parameters)) {
                $return_value = $this->_requested_parameters[$request_method];
            }
        }
        return $return_value;
    }

    /**
     * Unset (remove from current __Request) a specified parameter
     * 
     * @param string $parameter_name The parameter name to unset to
     */
    public function unsetParameter($parameter_name) {
        $parameter_name = strtolower($parameter_name);
        if(key_exists($parameter_name, $this->_requested_parameters[REQMETHOD_ALL])) {
            unset($this->_requested_parameters[REQMETHOD_ALL][$parameter_name]);
        }
        $request_method = $this->getMethod();
        if( key_exists($request_method, $this->_requested_parameters) && key_exists($parameter_name, $this->_requested_parameters[$request_method]) ) {
            unset($this->_requested_parameters[$request_method][$parameter_name]);
        }
    }    
    
    /**
     * Checks if exists a parameter associated to the current __Request instance
     *
     * @param string $parameter_name The parameter's name to check to
     * @return bool true if exists, else false
     */
    public function hasParameter($parameter_name) {
        $parameter_name = strtolower($parameter_name);
        return key_exists($parameter_name, $this->_requested_parameters[REQMETHOD_ALL]);
    }

    /**
     * Set the parameters for controller + action by specifying an action identity (which contains both values)
     *
     * @param __ActionIdentity $action_identity
     */
    public function setActionIdentity(__ActionIdentity $action_identity) {
        $this->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE'), $action_identity->getControllerCode());
        $this->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE'), $action_identity->getActionCode());
    }    
    
    /**
     * Enter description here...
     *
     * @return __ActionIdentity
     */
    public function getActionIdentity() {
        $return_value = new __ActionIdentity();
        if($this->hasParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE'))) {
            $return_value->setControllerCode($this->getParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE')));
        }
        if($this->hasParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE'))) {
            $return_value->setActionCode($this->getParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE')));
        }
        return $return_value;
    }
    
    /**
     * Returns the action code (if it has been specified as a parameter) associated to
     * the current request
     *
     * @return string The requested action code
     */
    public function getActionCode() {
        $request_action_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_ACTION_CODE');
        return $this->getParameter($request_action_code);
    }

    /**
     * Returns the action code (if it has been specified as a parameter) associated to
     * the current request
     *
     * @return string The requested action code
     */
    public function getControllerCode() {
        $request_controller_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_CONTROLLER_CODE');
        return $this->getParameter($request_controller_code);
    }
    
    /**
     * Returns the submit code (if it has been specified as a parameter) associated to
     * the current request
     *
     * @todo remove this method
     * 
     * @return string The submit code
     */
    public function getSubmitCode()
    {
        return $this->getParameter(__ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_SUBMIT_CODE'));        
    }
    
    /**
     * Populate current instance with all values extracted from client request
     *
     */
    abstract public function readClientRequest();
    
}