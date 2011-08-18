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
 * @package    ActionController
 * 
 */


class __ActionDispatcher {
    
	private static $_instance = null;

	private $_request_stack = array();
	private $_response_stack = array();
    private $_action_identity_stack = array();	
	
    private function __construct() {
    }
    
    /**
     * This method return a singleton instance of __ActionDispatcher instance
     *
     * @return __ActionDispatcher a reference to the current __ActionDispatcher instance
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ActionDispatcher();
        }
        return self::$_instance;
    }    
    
    public function &getRequest() {
        $return_value = end($this->_request_stack);
        return $return_value;
    }
    
    /**
     * Enter description here...
     *
     * @return __IResponse
     */
    public function &getResponse() {
        $return_value = end($this->_response_stack);
        return $return_value;
    }
    
    public function setResponse(__IResponse &$response) {
        if(count($this->_response_stack) > 0) {
            $this->_response_stack[count($this->_response_stack) - 1] =& $response;
        }
        else {
            $this->_response_stack[] =& $response;
        }
    }    
    
    public function &getActionIdentity() {
        $return_value = end($this->_action_identity_stack);
        return $return_value;
    }

    private function _pushRequest(__IRequest &$request = null) {
        end($this->_request_stack);
        if($request != null) {
            $this->_request_stack[] =& $request;
        }
        else if(count($this->_request_stack) > 0) {
            $this->_request_stack[] =& $this->_request_stack[count($this->_request_stack) - 1];
        }
        else {
            $this->_request_stack[] = __FrontController::getInstance()->getRequest();
        }
    }
    
    private function _pushResponse(__IResponse &$response = null) {
        end($this->_response_stack);
        if($response != null) {
            $this->_response_stack[] =& $response;
        }
        else if(count($this->_response_stack) > 0) {
            $this->_response_stack[] =& $this->_response_stack[count($this->_response_stack) - 1];
        }
        else {
            $this->_response_stack[] = __FrontController::getInstance()->getResponse();
        }
    }

    private function _pushActionIdentity(__ActionIdentity &$action_identity) {
        end($this->_action_identity_stack);
        $this->_action_identity_stack[] =& $action_identity;
    }
    
    private function &_popRequest() {
        $return_value =& $this->_request_stack[count($this->_request_stack) - 1];
        array_pop($this->_request_stack);
        return $return_value;
    }

    private function &_popResponse() {
        $return_value =& $this->_response_stack[count($this->_response_stack) - 1];
        array_pop($this->_response_stack);
        return $return_value;
    }

    private function &_popActionIdentity() {
        $return_value =& $this->_action_identity_stack[count($this->_action_identity_stack) - 1];
        array_pop($this->_action_identity_stack);
        return $return_value;
    }
    
    
    /**
     * Dispatch an action. If the action code is not specified, it will be used the default (ACTION_CODE_ON_ACTION_NOT_SPECIFIED).
     * 
     * If the default one does not correspond with any action controller method (remember that the method name is the action code 
     * with the suffix 'Action'), the ACTION_CODE_ON_ACTION_NOT_FOUND will be used instead of.<br>
     * <br>
     * The dispatch method, once has the action controller, performes the following tasks:<br>
     * <p>1. Call to {@link __ActionController::preProcess} method in order to execute a 'pre-logic' before the execution of the action controller logic (i.e. check preconditions, execute another action, ...)<br>
     * <p>2. Call to the action method in order to execute the action logic. The action method should return a {@link __ModelAndView} instance.
     *    In that case, the __ActionDispatcher will resolve a {@link __View} and will execute it (by calling to the {@link __View::execute} method).<br>
     * <p>3. Finally, call to {@link __ActionController::postProcess} method in order to execute a 'post-logic' (i.e. check postconditions, execute another action, ...)<br>
     * <br>
     * The {@link preProcess} and {@link postProcess} methods can be specialized in subclasses in order to define concrete actions and behaviors.<br>
     * 
     *
     * @param string $action_controller The action controller code (aka the module code)
     * @param string $action_code The action to execute.
     * @param __IRequest &$request The request
     * @param __IResponse &$response The response
     * @return mixed The response or the value returned by the controller if it's not a {@link __ModelAndView}
     */
    public function &dispatch(__ActionIdentity $action_identity, __IRequest &$request = null, __IResponse &$response = null) {
        $return_value =& $response; //by default we'll return the response
        $this->_pushRequest($request);
        $this->_pushResponse($response);
        $this->_pushActionIdentity($action_identity);
        //get the __ActionController class:
        $controller_code   = $action_identity->getControllerCode();
        $action_code       = $action_identity->getActionCode();
        $action_controller = __ActionControllerResolver::getInstance()->getActionController($controller_code);
        if( $action_controller instanceof __IActionController ) {
    		$I18n_resource_groups = $action_controller->getI18nResourceGroups();
		    $resources = array();
    		foreach($I18n_resource_groups as $I18n_resource_group) {
    		    $resources_action_identity = new __ActionIdentity($I18n_resource_group);
    		    $resources = array_merge($resources, __ResourceManager::getInstance()->loadActionResources($resources_action_identity));
    		}
            $front_controller_request = __FrontController::getInstance()->getRequest();
            if($front_controller_request != null) {
                $valid_request_method = $action_controller->getValidRequestMethod();
                if(($valid_request_method & $front_controller_request->getMethod()) == 0) {
                    throw __ExceptionFactory::getInstance()->createException('ERR_INVALID_REQUEST_METHOD', array($action_identity->getControllerCode()));
                }
            }
            //1. Execute the action's pre-logic:
            $action_controller->preExecute();
            //2. Execute the action logic:
            $controller_result = $action_controller->execute($action_code);
            if( $controller_result instanceof __ModelAndView ) {
                $view = $controller_result->getView();
                if($view == null) {
                    $view_code = $controller_result->getViewCode();
                    if($view_code == null) {
                        $view_code =  ($action_code ? $action_code : $controller_code); 
                    }
                    $view = __ViewResolver::getInstance()->getView($view_code);
                }
                if($view instanceof __View) {
                    $model = $controller_result->getModel();
                    $view->assignModel($model);
                    $view->assignResources($resources);
                    $response = $this->getResponse();
                    $response->appendContent($view->execute());
                }
            }
            else {
                $return_value =& $controller_result;
            }
            
            //3. Finally will execute the action's post-logic:
            $action_controller->postExecute();
        }
        $this->_popActionIdentity();
        $this->_popResponse();
        $this->_popRequest();
        return $return_value;
    }
    
    
}