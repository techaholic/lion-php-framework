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
 * This class represent an http request.
 * 
 * @see __Request
 *
 */
class __HttpRequest extends __Request {

    protected $_uri = null;
    protected $_files = array();
    protected $_unique_code = null;
    
    public function setUri(__Uri $uri) {
        $this->_uri = $uri;
        $this->setActionIdentity($uri->getActionIdentity());
        //Add all parameters to the current __Request instance
        $request_parameters = $uri->getParameters();
        foreach($request_parameters as $request_parameter_name => $request_parameter_value) {
            $this->addParameter($request_parameter_name, $request_parameter_value, REQMETHOD_GET);
        }
    }
    
    public function getUniqueCode() {
        if($this->_unique_code == null) {
            $get_parameters = $this->getParameters(REQMETHOD_GET);
            $unique_code = print_r($get_parameters, true);
            $this->_unique_code = md5($unique_code);
        }
        return $this->_unique_code;
    }
    
    /**
     * Returns the __Uri instance related with the current request
     *
     * @return __Uri The related __Uri instance
     */
    public function getUri() {
        if($this->_uri == null) {
            $request_controller_code = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE');
            $request_action_code     = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE');
            if($this->hasParameter($request_controller_code) || $this->hasParameter($request_action_code)) {
                $action_identity = new __ActionIdentity($this->getParameter($request_controller_code, $request_action_code));
                $this->_uri = __UriFactory::getInstance()->createUri();
                $this->_uri->setActionIdentity($action_identity);
                $this->_uri->setParameters($this->getParameters());
           }
        }
        return $this->_uri;
    }    
    
    public function &getFlowId() {
        $return_value = null;
        if($this->_uri != null) {
            $return_value = $this->_uri->getFlowId();
        }
        return $return_value;
    }
    
    public function getFrontControllerClass() {
        $return_value = null;
        if($this->_uri != null) {
            $return_value = $this->_uri->getFrontControllerClass();
        }
        if($return_value == null) {
            $return_value = __CurrentContext::getInstance()->getPropertyContent('HTTP_FRONT_CONTROLLER_CLASS');
        }
        return $return_value;
    }    
    
    public function &getFilterChain() {
        if($this->_uri != null) {
            $return_value = $this->_uri->getFilterChain();
            return $return_value;
        }
        else {
            return null;
        }
    }
    
    public function hasFilterChain() {
        $return_value = false;
        if($this->_uri != null) {
            $return_value = $this->_uri->hasFilterChain();
        }
        return $return_value;
    }
    
    public function readClientRequest() {
        $this->_readGlobalRequestParameters();
        $request_url   = $this->_getRequestUrl();
        if($request_url != null) {
            $uri =__UriFactory::getInstance()->createUri($request_url);
            $this->setUri($uri);
            if($uri instanceof __Uri) {
                $route = $uri->getRoute();
                if($route != null && $route instanceof __Route) {
                    $route_id_to_redirect_to = $route->getRouteIdToRedirectTo();
                    if(!empty($route_id_to_redirect_to)) {
                        $uri = __UriFactory::getInstance()->createUri()->setRoute($route_id_to_redirect_to)
                               ->setParameters($this->toArray(REQMETHOD_GET));
                        
                        $redirection_code = $route->getRedirectionCode();
                        __FrontController::getInstance()->redirect($uri, $this, $redirection_code);
                    }
                }
            }
            
        }
    }
    
    public function getFile($file_id) {
        $return_value = null;
        if(key_exists($file_id, $this->_files)) {
            $return_value = $this->_files[$file_id];
        }
        return $return_value;
    }
    
    public function hasFile($file_id) {
        return $this->_files[$file_id];
    }
    
    public function getFiles() {
        return $this->_files;
    }
    
    public function setFiles(array $files) {
        $this->_files = $files;
    }
    
    public function addFile($file_id, $file_values) {
        $this->_files[$file_id] = $file_values;
    }
    
    private function _getRequestUrl() {
        $return_value = null;
        if(!empty($_SERVER['REQUEST_URI'])) {
            $return_value = $_SERVER['REQUEST_URI'];
            //Remove get parameters appended to the url (all characters after the '?' symbol):
            if(strpos($return_value, '?') !== false) {
                $return_value = substr($return_value, 0, strpos($return_value, '?'));
            }
        }
        return $return_value;
    }    
    
    private function _readGlobalRequestParameters() {
        foreach($_GET as $key => $value) {
            $this->_requested_parameters[REQMETHOD_GET][strtolower($key)] = $value;
        }
        foreach($_POST as $key => $value) {
            $this->_requested_parameters[REQMETHOD_POST][strtolower($key)] = $value;
        }
        foreach($_REQUEST as $key => $value) {
            $this->_requested_parameters[REQMETHOD_ALL][strtolower($key)] = $value;            
        }
        $this->_files = $_FILES;
        
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET':
            default:
                $this->_request_method = REQMETHOD_GET;
                break;
            case 'POST':
                $this->_request_method = REQMETHOD_POST;
                break;
            case 'PUT':
                $this->_request_method = REQMETHOD_PUT;
                break;
            case 'DELETE':
                $this->_request_method = REQMETHOD_DELETE;
                break;
            case 'HEAD':
                $this->_request_method = REQMETHOD_HEAD;
                break;
        }

    }
    
    public function getActionIdentity() {
        $return_value = null;
        $request_flow_execution_key = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
        if($this->hasParameter($request_flow_execution_key)) {
            $return_value = new __ActionIdentity();
            $return_value->setController('flowController');
            $return_value->setAction('default');
        }
        else {
            $flow_id = $this->getFlowId();
            if($flow_id != null) {
                $return_value = new __ActionIdentity();
                $return_value->setController('flowController');
                $return_value->setAction('startFlow'); 
            }
        }
        if($return_value == null) {
            $return_value = parent::getActionIdentity();
        }
        return $return_value;
    }

    public function getHeader($header_name) {
        return $_SERVER[$header_name];
    }

    public function getCookie($name)
    {
        $return_value = null;
        if(key_exists($name, $_COOKIE)) {
            $return_value = $_COOKIE[$name];
        }
        return $return_value;
    }

    public function hasCookie($name) {
        return key_exists($name, $_COOKIE);
    }
    
    public function &getCookies() {
        return $_COOKIE;
    }
           
}