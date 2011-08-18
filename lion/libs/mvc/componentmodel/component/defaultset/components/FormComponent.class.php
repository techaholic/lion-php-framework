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
 * A form component represents a form to submit information filled by the user to the server.
 * <br>
 * Form tag is <b>form</b>
 * 
 * i.e.
 * <code>
 * 
 *   <comp:form name="search_invoices">
 * 
 *     Client id: <comp:inputbox name="client_id"/>
 * 
 *     <comp:commandbutton type = "submit" 
 *                         name = "search_button"
 *                      caption = "Search invoices"/>
 * 
 *   </comp:form>
 * 
 * </code>
 * 
 * Once a form is submitted, the <b>submit</b> event is raised after validating the submitted information.<br>
 * For that purpose, we can add validators to our input components, which are raised before handling the submit event. See the {@link __ValidationRuleComponent} for more information about validating components<br>
 * <br>
 * A form component redirect the user navigation to the same page by default, however we can define one of the both:<br>
 *  - A target <b>url</b> and/or a list of <b>parameters</b>, to be loaded after handling the submit or<br>
 *  - <b>route</b> and/or <b>controller</b> and/or <b>action</b> and/or a list of <b>parameters</b>, to build an url based on those values by ussing the lion url routing engine.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:form name = "search_invoices"
 *             route = "search_invoice"
 *        parameters = "search_mode=1" >
 * 
 *     ...
 * 
 *   </comp:form>
 * 
 * </code>
 *
 * In this example, we're going to redirect the navigation flow to the url built by the route "search_invoice". See the {@tutorial Request/Request.UrlRouting.pkg} to learn about url routing engine.<br>
 * <br> 
 * In the other hand, note that we can user <b>parameters</b> in both ways. Parameters is a comma-separated list of name/value pairs, which will be sent to the server as part of the submit.<br>
 * In case we are using a route, parameters will be embed within the url if the route defines placeholders for them.<br>
 * <br>
 * <br>
 * The submit life-cycle is the following one:<br>
 *  1. Validate components on client side (if applicable)<br>
 *  2. Validate components on server side, as a redundant validation (if applicable)<br>
 *  3. Handle the submit event by calling the correspond event handler method (if applicable)<br>
 *  4. Redirect the execution to the front controller (continuing the normal execution)<br>
 * <br>
 * 
 * Form components allow to specify a submit method, by setting the <b>method</b> attribute.
 * Form components also allow a set of parameters to be set as hidden (for HTML clients), by setting the <b>hiddenParameters</b> attribute.
 * <br>
 *
 */
class __FormComponent extends __UIContainer implements __IPoolable, __ISubmitter {
           		
    private $_action_code = null;
    private $_controller_code = null;
    private $_parameters = null;
    private $_hidden_parameters = array();
    private $_route_id = null;
    private $_url = null;
    private $_method = 'POST';
    private $_last_request = null;
    
    /**
     * Set the submit method, allowing same methods as the HTML form element.
     * POST is the default method used by a form component
     *
     * @param string $method
     */
    public function setMethod($method) {
        $this->_method = $method;
    }
    
    /**
     * Get the submit method to be used by the current form component
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }
    
    /**
     * Set the action to be used by the url routing engine to build the target url
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
     * Set the controller to be used by the url routing engine to build the target url
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
     * Set a list of parameters (either an array or a comma-separated list) to be sent within the form submit.
     * Parameters are also sent to the url routing engine to build the target url (if applicable)
     *
     * @param mixed $parameters
     */
    public function setParameters($parameters) {
        if($this->_parameters != $parameters) {
            $this->_parameters = $parameters;
            $this->_url = null;
        }
    }
    
    /**
     * Set the route to be used by the url routing engine to build the target url
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
     * Set the url to redirect the user navigation to, once the form is submitted
     *
     * @param string $url
     */
    public function setUrl($url) {
        $this->_url = $url;
    }
    
    /**
     * Alias of {@link __FormComponent::setUrl()} method
     *
     * @param string $target
     */
    public function setTarget($target) {
        $this->_url = $target;
    }
    
    /**
     * Get the action id to be used by the url routing engine to build the target url
     *
     * @return string
     */
    public function getAction() {
        return $this->_action_code;
    }
    
    /**
     * Get the controller id to be used by the url routing engine to build the target url
     *
     * @return string
     */
    public function getController() {
        return $this->_controller_code;
    }
    
    /**
     * Get the parameters to be send within the form submit.
     *
     * @return mixed
     */
    public function getParameters() {
        return $this->_parameters;
    }
    
    /**
     * Get the route id to be used by the url routing engine to build the target url
     *
     * @return string
     */
    public function getRoute() {
        return $this->_route_id;
    }
    
    /**
     * Get the target url to redirect the navigation once the form has been submitted
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }  	
    
    /**
     * Set an array of parameters to be rendered as hidden parameters within the form
     *
     * @param array $hidden_parameters
     */
    public function setHiddenParameters(array $hidden_parameters) {
        $this->_hidden_parameters = $hidden_parameters;
    }
    
    /**
     * Get the array of parameters to be rendered as hidden parameters within the form
     *
     * @return array
     */
    public function getHiddenParameters() {
        return $this->_hidden_parameters;
    }
    
    /**
     * For internal usage only.
     * Lion sets the last request in which this component was rendered in.
     * Is used in case the server-side validation fails
     *
     * @param __IRequest $request
     */
    public function setLastRequest(__IRequest &$request) {
        $this->_last_request = $request;
    }
    
    /**
     * Gets the last request in which this component was rendered in.
     * This method is used by Lion in order to determine where to redirect the flow in case
     * a server-side validation fails
     *
     * @return __Request
     */
    public function getLastRequest() {
        return $this->_last_request;
    }
    
    public function getUseAbsoluteUrl() {
        return false;
    }
    
    
}
