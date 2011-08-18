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


interface __IActionController extends __ISystemResource {
        
    /**
     * Sets an unike code for current action controller. 
     * 
     * @param string $action_code An unike code for current action controller
     */
    public function setCode($code);
    
    /**
     * Gets the code associated to the current action controller.
     * 
     * @return string The code assigned to current action controller
     */
    public function getCode();    
    
    /**
     * Set the allowed request methods to execute current action controller
     * 
     * Possible values:<br>
     * - <b>REQMETHOD_NONE</b>: No requests methods are allowed<br>
     * - <b>REQMETHOD_GET</b>: Only GET request method is allowed<br>
     * - <b>REQMETHOD_POST</b>: Only POST request method is allowed<br>
     * - <b>REQMETHOD_COMMAND_LINE</b>: Only from command line (ussing php client) is allowed<br>
     * - <b>REQMETHOD_ALL</b>: All possible request methods are allowed<br>
     * 
     * @param integer $valid_request_method A code that identify all valid request methods to execute current action controller
     */
    public function setValidRequestMethod($valid_request_method);
    
    /**
     * Get the allowed request methods to execute current action controller.
     * 
     * @return integer A code that identify all valid request methods to execute current action controller
     */    
    public function getValidRequestMethod();

    /**
     * Set if current action can be logged in the history registry or not.
     *
     * @param boolean $is_historiable If current action can be logged in the history registry
     */
    public function setHistoriable($is_historiable);
    
    /**
     * Get if current action can be logged in the history registry or not.
     *
     * @return boolean If current action can be logged in the history registry
     */
    public function isHistoriable();
    
    /**
     * Set if an action controller can be selected and executed by the {@link __FrontController} in response to dispatching the user request.
     * In other words: if an action controller is requestable directly by the user or not. 
     * 
     * <p>i.e.: imagine that the request to the url http://yourdomain/showcars.action is resolved by the {@link __FrontController} by executing the "showcars" action controller,
     * in that case, that action should be requestable. Otherwise it will raise an exception.     
     *
     * @param boolean $is_requestable If current action controller can be requested directly by the user
     */
    public function setRequestable($is_requestable);
    
    /**
     * Get if current action controller can be requested directly by the user or not.
     *
     * @return boolean If current action controller can be requested directly by the user
     */
    public function isRequestable();
    
    /**
     * Set if current action controller requires SSL protocol to be executed. 
     *
     * @param boolean $require_ssl If current action controller requires SSL protocol to be executed
     */
    public function setRequireSsl($require_ssl);
    
    /**
     * Getter for {@link _require_ssl} member
     * Get if current action controller requires SSL protocol to be executed.
     *
     * @return boolean If current action controller requires SSL protocol to be executed
     */
    public function getRequireSsl();

    public function preExecute();
    /**
     * Enter description here...
     *
     * @param string $ The action code to execute. If not specified, the default one should be used
     * 
     * @return __ModelAndView A __ModelAndView instance as a result of executing the requested action
     */
    public function execute($action_code = null);
    
    public function postExecute();

}