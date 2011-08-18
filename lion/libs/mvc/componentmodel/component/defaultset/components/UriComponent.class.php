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
 * The Uri Component is a component designed to render a url according to a route definition.
 * <br>
 * Uri tag is <b>uri</b><br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:uri route = "search" 
 *        controller = "invoices" 
 *        parameters = "invoice_id=4301"/>
 * 
 * </code>
 * 
 * This example will generate a url according to the given route (i.e. <i>search</i> route: http://yourdomain.com/invoices/4301.html)<br>
 * <br>
 * Uri component allow to specify the following attributes:<br>
 *  - <b>route</b>: The route id to use to generate the url<br>
 *  - <b>controller</b>: The controller code to use to generate the url<br>
 *  - <b>action</b>: The action code to use to generate the url<br>
 *  - <b>parameters</b>: A comma-separate list of parameters to generate the url<br>
 * <br>
 * As a result, the Uri component generates a url.<br> 
 * 
 *
 */
class __UriComponent extends __UIComponent implements __IUriContainer {

    private $_action_code = null;
    private $_controller_code = null;
    private $_parameters = null;
    private $_route_id = null;
    private $_url = null;
    private $_use_current_route = false;
    private $_use_absolute_url = false;
    
    /**
     * Set the action code to use to generate the url
     *
     * @param string $action_code
     */
    public function setAction($action_code) {
        if($this->_action_code != $action_code) {
            $this->_action_code = $action_code;
            $this->_url = null;
        }
    }
    
    /**
     * Set the controller code to use to generate the url
     *
     * @param string $controller_code
     */
    public function setController($controller_code) {
        if($this->_controller_code != $controller_code) {
            $this->_controller_code = $controller_code;
            $this->_url = null;
        }
    }
    
    /**
     * Set a comma-separate list of parameters to use to generate the url
     *
     * @param string $parameters
     */
    public function setParameters($parameters) {
        if($this->_parameters != $parameters) {
            $this->_parameters = $parameters;
            $this->_url = null;
        }
    }
    
    public function setUseCurrentRoute($use_current_route) {
    	$this->_use_current_route = $this->_toBool($use_current_route);
    }
    
    public function setUseAbsoluteUrl($use_absolute_url) {
        $this->_use_absolute_url = $this->_toBool($use_absolute_url);
    }    
    
    public function getUseAbsoluteUrl() {
        return $this->_use_absolute_url;
    }
    
    public function getUseCurrentRoute() {
    	return $this->_use_current_route;
    }
    
    public function useCurrentRoute() {
    	return $this->getUseCurrentRoute();
    }
    
    /**
     * Set the route id to use to generate the url
     *
     * @param string $route_id
     */
    public function setRoute($route_id) {
        if($this->_route_id != $route_id) {
            $this->_route_id = $route_id;
            $this->_url = null;
        }
    }

    /**
     * Get the action code set to the current component
     *
     * @return string
     */
    public function getAction() {
        return $this->_action_code;
    }
    
    /**
     * Get the controller code set to the current component
     *
     * @return string
     */
    public function getController() {
        return $this->_controller_code;
    }
    
    /**
     * Get the comma-separate list of parametes set to the current component
     *
     * @return string
     */
    public function getParameters() {
        return $this->_parameters;
    }
    
    /**
     * Get the route id set to the current component
     *
     * @return string
     */
    public function getRoute() {
        return $this->_route_id;
    }
    
    /**
     * Get the url generated by the current component
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }
    
    /**
     * Set a url represented by the current component.
     * This property is for internal usage only, to set the resultant url.
     *
     * @return string
     */
    public function setUrl($url) {
        $this->_url = $url;
    }    
    
}
