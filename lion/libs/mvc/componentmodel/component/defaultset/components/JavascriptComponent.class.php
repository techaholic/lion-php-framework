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
 * A javascript component renderes a portion of javascript at the bottom of the page, inside the portion of javascript generated by the framework.
 * 
 * One of the advantages of ussing the javascript component is to have all the javascript at the bottom, as recommended by {@link http://developer.yahoo.com/performance/rules.html#js_bottom Yahoo developer network}.<br>
 * However, the javascript component exposes an API easing the execution of code once js file dependences has been downloaded.<br>
 * <br>
 * Javascript tag is <b>javascript</b>
 * 
 * i.e.
 * <code>
 * 
 *   <comp:javascript>
 * 
 *     //here your javascript code
 * 
 *   </comp:javascript>
 * 
 * </code>
 * 
 * If you are ussing it with the smarty view, take into account that javascript brakets are interpreted by smarty template engine, so you may need to use the smarty {literal} tag to enclose your javascript code.<br>
 * i.e.
 * <code>
 * 
 *   <comp:javascript>
 *   {literal}
 * 
 *     //here your javascript code
 * 
 *   {/literal}
 *   </comp:javascript>
 * 
 * </code>
 * <br> 
 * A javascript component can be even defined to be nested by other javascript components. In that case, the nested javascript code will be executed after the execution of the parent component.<br>
 * This is usefull to define the order to follow to download .js and execute code.<br>
 * <br>
 * .js files can be specified by setting the <b>jsFiles</b> attribute with an array or a comma-separated list of paths.<br>
 * To ensure that a .js has been downloaded successfully, the javascript component ask for variables defined within .js files. 
 * Those variables are known as checking variables, which can be also defined by setting the <b>checkingVariables</b> attribute.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:javascript jsFiles="/forms/balloon/balloon.js" checkingVariables="Balloon">
 * 
 *     var myBalloon = new Balloon();
 *     ...
 * 
 *   </comp:javascript>
 * 
 * </code>
 * 
 * We recommend to use javascript components because they ensure that js libraries will be downloaded, even if the code is executed on demand, i.e. as a result of fill an actionbox as the response of an AJAX call.<br>
 * In that sense, the javascript component handle what is known as javascript onDemand.<br>
 * <br>
 * In the other hand, the enclosed javascript execution can be delayed until the DOM has been loaded by setting the <b>afterDomLoaded</b> property to true.<br>
 * <br>
 * Last, the javascript component allow to group code together by setting a group to each one. Two different javascript components will be rendered together (and executed at the same time, one after the other one) if both of them belong to the same group.<br>
 * By default, a javascript component belong to the group "javascript" 
 *
 */
class __JavascriptComponent extends __UIContainer {

    protected $_after_dom_loaded = false;
    protected $_group_id = null;
    protected $_js_to_load = array();
    protected $_checking_variables = array(); 
    
    /**
     * Set a collection of .js file paths to be loaded before executing the javascript code enclosed by the component.
     * It allows an array or a comma-separated list of paths
     *
     * @param mixed $js_files
     */
    public function setJsFiles($js_files) {
        if(is_array($js_files)) {
            $this->_js_to_load = $js_files;
        }
        else if(is_string($js_files)) {
            $this->_js_to_load = preg_split('/,/', $js_files);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unknow parameter type for js files. An array or comma-separate list was expected');
        }
    }
    
    /**
     * Get an array of .js file paths to be loaded before executing the javascript code enclosed by the component.
     *
     * @return array
     */
    public function getJsFiles() {
        return $this->_js_to_load;
    }

    /**
     * Set a collection of checking variables, variables to be checked if exist to ensure that .js files has been downloaded successfully.
     * It allows an array or a comma-separated list of variable names
     * 
     * @param mixed $checking_variables
     */
    public function setCheckingVariables($checking_variables) {
        if(is_array($checking_variables)) {
            $this->_checking_variables = $checking_variables;
        }
        else if(is_string($checking_variables)) {
            $this->_checking_variables = preg_split('/,/', $checking_variables);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unknow parameter type for checking variables. An array or comma-separate list was expected');
        }
    }
    
    /**
     * Get an array of checking variables, variables to be checked if exist to ensure that .js files has been downloaded successfully.
     *
     * @return array
     */
    public function getCheckingVariables() {
        return $this->_checking_variables;
    }
    
    /**
     * Set if the javascript execution must be delayed until the Dom has been loaded
     *
     * @param bool $after_dom_loaded
     */
    public function setAfterDomLoaded($after_dom_loaded) {
        $this->_after_dom_loaded = $this->_toBool($after_dom_loaded);
    }
    
    /**
     * Get if the javascript execution must be delayed until the Dom has been loaded
     *
     * @return bool
     */
    public function getAfterDomLoaded() {
        return $this->_after_dom_loaded;
    }
    
    /**
     * Set a group id which the current javascript component belong to
     *
     * @param string $group_id
     */
    public function setGroup($group_id) {
        $this->_group_id = $group_id;
    }
    
    /**
     * Get the group id which the current javascript component belong to
     *
     * @return unknown
     */
    public function getGroup() {
        return $this->_group_id;
    }
    
}
