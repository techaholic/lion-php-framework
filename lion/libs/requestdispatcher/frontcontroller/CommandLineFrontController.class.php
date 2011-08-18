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

class __CommandLineFrontController extends __FrontController {

    /**
     * This method dispatch the current request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        $action_identity = $request->getActionIdentity();
        $controller_code = $action_identity->getControllerCode();
        //in case we haven't define any controller, will use the commandline controller from lion admin:
        if(empty($controller_code)) {
            //switch to lion admin area:
            __ContextManager::getInstance()->createContext("LION_ADMIN_AREA", ADMIN_DIR);
            __ContextManager::getInstance()->switchContext("LION_ADMIN_AREA");
            //execute the commandline controller:
            $action_identity->setControllerCode('commandline');
        }
        $action_controller = __ActionControllerResolver::getInstance()->getActionController( $action_identity->getControllerCode() );
        //First of all will check permissions for an action
        if($action_controller->isRequestable()) {
            __ActionDispatcher::getInstance()->dispatch( $action_identity );
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_ACTION_NON_REQUESTABLE');
        }
    }

    /**
     * Forward the execution flow to the given uri
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest $request
     */
    public function forward($uri, __IRequest &$request = null) {
        if(is_string($uri)) {
            $uri = __UriFactory::getInstance()->createUri($uri);
        }
        else if(!$uri instanceof __Uri) {
            throw __ExceptionFactory::getInstance()->createException('Unexpected type for uri parameter: ' . get_class($uri));
        }
        if($request == null) {
            $request = __RequestFactory::getInstance()->createRequest();
        }
        $request->setUri($uri);
        $request->setRequestMethod(REQMETHOD_ALL);
        $response = __Client::getInstance()->getResponse();
        $response->clear(); //clear the response
        $this->dispatch($request, $response); //dispatch the request
        $response->flush(); //flush the response
        exit;
    }
    
    /**
     * Alias of forward
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest $request
     */    
    public function redirect($uri, __IRequest &$request = null, $redirection_code = null) {
        $this->forward($uri, $request);
    }      
    
}