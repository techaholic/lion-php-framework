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
 * @package    UrlRoutingEngine
 * 
 */

/**
 * This class represent an URI.
 * 
 * It is able to interpret urls, discompounding into single elements like the action code, 
 * request parameters, front controller to dispatch a request for current uri, etc...<br>
 * It also make the inverse operation: build an url from a set of single elements.
 * To make both operations, it requires a route definition (contained in a __Route instance).<br>
 * <br>
 * i.e.:<br>
 * {@example Uri.usage.php}
 * 
 * @see __Route, __RouteManager, __UriFactory
 * 
 */
class __Uri {
    
    private $_front_controller_class = null;
    private $_action_identity = null;
    private $_parameters      = array();
    private $_protocol        = null;
    private $_route_id        = null;
    private $_url             = null;
    private $_dirty           = false;
    private $_application_domain = null;
    private $_flow_id = null;
    
    /**
     * Constructor method
     *
     */
    public function __construct($url = null) {
        $this->_reset();
        if($url != null) {
            $this->setUrl($url);
        }
    }
    
    protected function _reset() {
        $this->_action_identity = new __ActionIdentity();
        $this->_protocol        = __CurrentContext::getInstance()->getPropertyContent('DEFAULT_PROTOCOL');
        $this->_route_id        = __CurrentContext::getInstance()->getPropertyContent('DEFAULT_ROUTE');
        $this->_parameters      = array();
        $this->_url             = null;
        $this->_dirty           = false;
        $this->_front_controller_class = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getPropertyContent('HTTP_FRONT_CONTROLLER_CLASS');
    }

    /**
     * Alias of setActionCode
     *
     * @param unknown_type $action_code
     * @return unknown
     */
    public function &setAction($action_code) {
        return $this->setActionCode($action_code);
    }
    
    public function &setActionCode($action_code) {
        $this->_action_identity->setActionCode($action_code);
        $this->_dirty = true;
        return $this;
    }
    
    /**
     * Alias of setControllerCode
     *
     * @param unknown_type $controller_code
     * @return unknown
     */
    public function &setController($controller_code) {
        return $this->setControllerCode($controller_code);
    }
    
    public function &setControllerCode($controller_code) {
        $this->_action_identity->setControllerCode($controller_code);
        $this->_dirty = true;
        return $this;
    }
    
    /**
     * This method set the action code associated to current uri
     *
     * @param string $action_identity The action code
     * @return __Uri A reference to the current instance
     */
    public function &setActionIdentity(__ActionIdentity $action_identity) {
        if($this->_action_identity != $action_identity) {
            $this->_action_identity = $action_identity;
            $this->_dirty = true;
        }
        return $this;
    }
    
    /**
     * Returns an action code (if exists) associated to the current instance, else returns null
     *
     * @return __ActionIdentity The action code
     */
    public function getActionIdentity() {
        return $this->_action_identity;
    }
    
    public function setFlowId($flow_id) {
        $this->_flow_id = $flow_id;
    }
    
    public function getFlowId() {
        return $this->_flow_id;
    }
    
    /**
     * Set a collection of parameters associated to current instance
     *
     * @param array $parameters A collection of parameters
     * @return __Uri a reference to the current instance
     */
    public function &setParameters(array $parameters) {
        if($this->_parameters != $parameters) {
            $this->_parameters = $parameters;
            $this->_dirty = true;
        }
        return $this;
    }

    public function addParameter($parameter_name, $parameter_value) {
        if(!key_exists($parameter_name, $this->_parameters) || $this->_parameters[$parameter_name] != $parameter_value) {
            $this->_parameters[$parameter_name] = $parameter_value;
            $this->_dirty = true;
        }
        return $this;
    }
    
    /**
     * Returns a collection of parameters associated to current 
     *
     * @return unknown
     */
    public function getParameters() {
        return $this->_parameters;
    }
    
    public function &setProtocol($protocol) {
        $this->_protocol = $protocol;
        return $this;
    }
    
    public function getProtocol() {
        return $this->_protocol;
    }
    
    public function &setRouteId($route_id) {
        if($this->_route_id != $route_id) {
            if(__RouteManager::getInstance()->hasRoute($route_id)) {
                $this->_route_id = $route_id;
                $this->_dirty = true;
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROUTE_ID', array($route_id));
            }
        }
        return $this;
    }
    
    /**
     * Alias of setRouteId
     *
     * @param unknown_type $route_id
     * @return unknown
     */
    public function &setRoute($route_id) {
        return $this->setRouteId($route_id);
    }
    
    public function getRouteId() {
        return $this->_route_id;
    }
    
    public function getRoute() {
        $route = __RouteManager::getInstance()->getRoute($this->_route_id);
        return $route;
    }
    
    public function &setFrontControllerClass($front_controller_class) {
        $this->_front_controller_class = $front_controller_class;
        return $this;
    }
    
    public function getFrontControllerClass() {
        return $this->_front_controller_class;
    }

    public function &getFilterChain() {
        $return_value = null;
        if($this->_route_id != null) {
            $route = __RouteManager::getInstance()->getRoute($this->_route_id);
            if ($route != null) {
                $return_value = $route->getFilterChain();
            }
        }
        return $return_value;
    }

    public function hasFilterChain() {
        $return_value = false;
        if($this->_route_id != null) {
            $route = __RouteManager::getInstance()->getRoute($this->_route_id);
            if ($route != null) {
                $return_value = $route->hasFilterChain();
            }
        }
        return $return_value;
    }
    
    public function &setUrl($url) {
        if($this->_url != $url) {
            $url_components = parse_url($url);
            if(is_array($url_components)) {
                $url = $url_components['path'];
                if(key_exists('query', $url_components)) {
                    $query = $url_components['query'];
                    if(!empty($query)) {
                        $url .= '?' . $query;
                    }
                }
                $this->_reset();
                $this->_url = $url;
                $this->_calculateUriComponents();
                $this->_dirty = false;
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Invalid url: ' . $url);
            }
        }
        return $this;
    }
    
    public function getUrl($force_relcalculate = false) {
        return $this->getRelativeUrl($force_relcalculate);
    }
    
    public function getRelativeUrl($force_relcalculate = false) {
        if($this->_dirty || $force_relcalculate) {
            $this->_url = $this->_calculateUrl();
            $this->_dirty = false;
        }
        return $this->_url;
    }
    
    public function getAbsoluteUrl($force_relcalculate = false) {
        if($this->_application_domain != null) {
            $application_domain = $this->_application_domain;
        }
        else {
            $application_domain = __ApplicationContext::getInstance()->getPropertyContent('APPLICATION_DOMAIN');
            if($application_domain == null && isset($_SERVER['SERVER_NAME'])) {
                $application_domain = $_SERVER['SERVER_NAME'];
            }
        }
        if($application_domain != null) {
            $return_value = HTTP_PROTOCOL . '://' . $application_domain . HTTP_PORT . $this->getRelativeUrl($force_relcalculate);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unable to resolve a server domain. Set a domain (APPLICATION_DOMAIN in settings.ini) in order to call to this method by the command line');
        }
        return $return_value;
    }

    public function __toString() {
        return $this->getUrl();
    }

    protected function _calculateUrl() {
        $return_value = null;
        $route = __RouteManager::getInstance()->getRoute($this->getRouteId());
        $url_builder = $route->getUrlBuilder();
        if($url_builder != null) {
            $url_builder->setActionIdentity($this->_action_identity);
            foreach($this->_parameters as $parameter_name => $parameter_value) {
                $url_builder->addParameter($parameter_name, $parameter_value);
            }
            $return_value = $url_builder->getUrl();
        }
        return $return_value;
    }
    
    protected function _calculateUriComponents() {
        $route = __RouteManager::getInstance()->getValidRouteForUrl($this->_url);
        if($route == null) {
            throw new Exception('Route not found matching url: ' . $this->_url);
        }
        $this->setRouteId( $route->getId() );
        $this->setFlowId( $route->getFlowId() );
        $url_variable_values = array();
        $variables_matched   = array();
        if(preg_match('/' . $route->getUrlRegularExpression() . '/', $this->_url, $variables_matched)) {
            $variables_order = $route->getVariablesOrder();
            $total_variables_matched = count($variables_matched);
            for($i = 1; $i < $total_variables_matched; $i++) {
                $url_variable_values[$variables_order[$i-1]] = $variables_matched[$i];
            }
            //Now will instantiate a valid front controller for the current request:
            $front_controller_class = $route->getFrontControllerClass();
            if($front_controller_class != null) {
                if(key_exists($front_controller_class, $url_variable_values)) {
                    $this->setFrontControllerClass( $url_variable_values[$front_controller_class] );
                }
                else {
                    $this->setFrontControllerClass( $front_controller_class );
                }
            }
            $request_parameters = $route->getParameters($url_variable_values);
            $action_identity = $route->getActionIdentity();
            if($action_identity != null) {
                $request_action_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_ACTION_CODE');
                if(!in_array($action_identity->getActionCode(), $variables_order)) {
                    $this->setActionCode($action_identity->getActionCode());
                    $request_parameters[$request_action_code] = $action_identity->getActionCode();
                }
                else if(key_exists($action_identity->getActionCode(), $url_variable_values)) {
                    $this->setActionCode($url_variable_values[$action_identity->getActionCode()]);
                    $request_parameters[$request_action_code] = $url_variable_values[$action_identity->getActionCode()];
                }
                $request_controller_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_CONTROLLER_CODE');
                if(!in_array($action_identity->getControllerCode(), $variables_order)) {
                    $this->setControllerCode($action_identity->getControllerCode());
                    $request_parameters[$request_controller_code] = $action_identity->getControllerCode();
                }
                else if(key_exists($action_identity->getControllerCode(), $url_variable_values)) {
                    $this->setControllerCode($url_variable_values[$action_identity->getControllerCode()]);
                    $request_parameters[$request_controller_code] = $url_variable_values[$action_identity->getControllerCode()];
                }
            }
            $this->setParameters($request_parameters);
        }
    }
    
    public function &setApplicationDomain($application_domain) {
        $this->_application_domain = $application_domain;
        return $this;
    }

    public function getApplicationDomain() {
        return $this->_application_domain;
    }
    
    
}