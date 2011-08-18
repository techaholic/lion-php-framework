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


/**
 * This is the base class to define action controllers in Lion.
 * 
 * For more information regarding action controllers, see the {@tutorial MVC/Controller/ActionController.pkg} document.
 * 
 * @see __Controller, __FrontController, __SystemResource
 * 
 */
abstract class __ActionController extends __SystemResource implements __IActionController {

    /**
     * Allowed request methods to execute current action controller
     * 
     * Possible values:<br>
     * - <b>REQMETHOD_NONE</b>: No requests methods are allowed<br>
     * - <b>REQMETHOD_GET</b>: Only GET request method is allowed<br>
     * - <b>REQMETHOD_POST</b>: Only POST request method is allowed<br>
     * - <b>REQMETHOD_COMMAND_LINE</b>: Only from command line (ussing php client) is allowed<br>
     * - <b>REQMETHOD_ALL</b>: All possible request methods are allowed (this is the default value)<br>
     * 
     * By default REQMETHOD_ALL, that means that current action controller can be executed by all available request methods.<br>
     * 
     * @var integer 
     * 
     */
    protected $_valid_request_method = REQMETHOD_ALL;

    /**
     * If action can be stored in the history or not (history is managed by the {@link __HistoryManager} instance). 
     * 
     * By default, an action is HISTORIABLE
     *
     * @var bool
     */
    protected $_is_historiable = true;

    /**
     * If an action controller can be selected and executed by the {@link __FrontController} in response to dispatching the user request.
     * In other words: if an action controller is requestable directly by the user or not. 
     * 
     * <p>By default, an action controller allows to be requested by the user.
     * <p>i.e.: imagine that the request to the url http://yourdomain/showcars.action is resolved by the {@link __FrontController} by executing the "showcars" action controller,
     * in that case, that action should be requestable. Otherwise it will raise an exception.     
     * 
     * @var bool
     */
    protected $_is_requestable = true;

    /**
     * If an action controller requires SSL protocol to be executed. 
     * 
     * By default an action doesn't require SSL
     *
     * @var bool
     */
    protected $_required_ssl = false;

    /**
     * The code for current action. An unike identifier for each action controller.
     *
     * @var string
     */
    protected $_code = null;
    
    protected $_I18n_resource_groups = array();

    /**
     * {@inheritdoc}
     */
    public function setValidRequestMethod($valid_request_method) {
        $this->_valid_request_method = $valid_request_method;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidRequestMethod()
    {
        return $this->_valid_request_method;
    }

    /**
     * {@inheritdoc}
     */
    public function setHistoriable($is_historiable) {
        $this->_is_historiable = (bool) $is_historiable;
    }

    /**
     * {@inheritdoc}
     */
    public function isHistoriable() {
        return $this->_is_historiable;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestable($is_requestable) {
        $this->_is_requestable = (bool)$is_requestable;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequestable() {
        return $this->_is_requestable;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequireSsl($require_ssl) {
        $this->_require_ssl = (bool) $require_ssl;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequireSsl() {
        return $this->_require_ssl;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code) {
        $this->_code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode() {
        return $this->_code;
    }
    
    public function setI18nResourceGroups(array $I18n_resource_groups) {
        $this->_I18n_resource_groups = $I18n_resource_groups;
    }
    
    public function getI18nResourceGroups() {
        if(count($this->_I18n_resource_groups) == 0) {
            $this->_I18n_resource_groups[] = $this->_code;
        }
        return $this->_I18n_resource_groups;
    }  

    /**
     * Execute a 'pre-logic' before to the execution of the action logic itself. i.e., for check pre-conditions or just to execute other actions previously to the current one.
     * 
     * i.e.: use of _preProcess method to execute an action controller in charge of showing a header:
     * {@example ActionController.preProcess.php}
     * 
     */
    public function preExecute() {}

    /**
     * This method will be executed after the execution of the action controller main logic.
     * 
     * It is useful to execute logic just after the execution of the main logic (i.e. check post-conditions, execute other action controllers, ...)
     * 
     * i.e.
     * {@example ActionController.postProcess.php}
     * 
     */
    public function postExecute() {}

    public function execute($action_code = null) {
        if($action_code == null) {
            if(method_exists($this, $this->getCode() . 'Action')) {
                $action_code = $this->getCode();
            }
            else {
                $action_code = __CurrentContext::getInstance()->getPropertyContent('DEFAULT_ACTION_CODE');
            }
        }
        if(!method_exists($this, $action_code . 'Action')) {
            throw __ExceptionFactory::getInstance()->createException('ERR_ACTION_NOT_SUPPORTED_BY_CONTROLLER', array(get_class($this), $action_code));
        }
        $model_and_view = call_user_func_array (array($this, $action_code . 'Action'), array());
        return $model_and_view;
    }

    /**
     * {@inheritdoc}
     */
    public function onAccessError() {
        $exception = __ExceptionFactory::getInstance()->createException('ERR_ACTION_PERMISSION_ERROR', array('action_code' => $this->getCode()));
        $exception->setExtraInfo(array('system_resource' => $this));
        throw $exception;
    }



}
