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
 * @package    FrontController
 * 
 */


abstract class __FrontController implements __IFrontController {

    private static $_instance = null;
    protected $_request  = null;
    protected $_response = null;
    
    public function &getRequest() {
        return $this->_request;
    }

    public function &getResponse() {
        return $this->_response;
    }
    
    final protected function __construct() {
    }
    
    /**
     * Singleton method to retrieve the __FrontController instance
     *
     * @return __FrontController The singleton __FrontController instance
     */
    final public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = self::_createFrontController();
        }
        return self::$_instance;
    }
    
    /**
     * Factory method for creating a __FrontController to dispatch the client request
     *
     * @return __IFrontController
     */
    final private static function _createFrontController() {
        $client_request = __Client::getInstance()->getRequest();
        //if the client request has been initialized successfully, will ask for correspondent front controller:
        if($client_request != null) {
            $front_controller_class = $client_request->getFrontControllerClass();
        }
        //otherwise will get the most appropriate fron controller depending on the client type (http, commandline, ...):
        else {
            $front_controller_class = __Client::getInstance()->getDefaultFrontControllerClass();
        }
        $front_controller = new $front_controller_class();
        if(! $front_controller instanceof __IFrontController ) {
            throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_FRONT_CONTROLLER_CLASS', array($front_controller_class));
        }
        return $front_controller;
    }

    public function dispatchClientRequest() {
        $request  = __Client::getInstance()->getRequest();
        $response = __Client::getInstance()->getResponse();
        $this->dispatch($request, $response);
        $response->flush();
    }
        
    public function dispatch(__IRequest &$request, __IResponse &$response) {
        //set the current request and response:
        $this->_request  =& $request;
        $this->_response =& $response;
        //dispatch the request:
        if($request->hasFilterChain()) {
            $filter_chain = $request->getFilterChain();
            $filter_chain->reset();
            $filter_chain->setFrontControllerCallback($this, 'processRequest');
            $filter_chain->execute($request, $response);
        }
        else {
            $this->processRequest($request, $response);
        }
    }
    
    abstract public function processRequest(__IRequest &$request, __IResponse &$response);
    
}
