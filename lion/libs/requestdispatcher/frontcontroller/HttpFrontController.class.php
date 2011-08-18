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

/**
 * This is the most basic implementation to dispatch HTTP requests.
 *
 */
class __HttpFrontController extends __FrontController {

    /**
     * This method dispatch the current request
     * 
     * @param __IRequest &$request The request to process to
     * @param __IResponse &$response The instance to set the response to
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        //resolve action identity from request
        $action_identity = $request->getActionIdentity();
        //resolve the action controller associated to the given action identity
        $action_controller = __ActionControllerResolver::getInstance()->getActionController( $action_identity->getControllerCode() );
        //check if action controller is requestable
        if($action_controller->isRequestable()) {
            __HistoryManager::getInstance()->addRequest($request);
            //last, execute the action controller
            __ActionDispatcher::getInstance()->dispatch($action_identity);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_ACTION_NON_REQUESTABLE');
        }
    }
    
    /**
     * Forward the web flow to the given uri.
     * This method is similar to redirect, but performs the redirection internally (without http redirection codes)
     *
     * @param __Uri|string the uri (or an string representing the url) to redirect to
     * @param __IRequest &$request The request instance to use in the forward
     * 
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
     * Redirects to the given uri.
     * This method uses an HTTP redirection to force the client browser to be redirected to
     * 
     * @see __HttpFrontController::forward()
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest &$request The request associated to the redirection
     * @param integer $redirection_code The HTTP redirection code
     */
    public function redirect($uri, __IRequest &$request = null, $redirection_code = null) {
        if(is_string($uri)) {
            $uri = __UriFactory::getInstance()->createUri($uri);
        }
        else if(!$uri instanceof __Uri) {
            throw __ExceptionFactory::getInstance()->createException('Unexpected type for uri parameter: ' . get_class($uri));
        }
        $url = $uri->getUrl();
        if($request != null) {
            $parameters = $request->getParameters(REQMETHOD_GET);
            if(count($parameters) > 0) {
                if(strpos($url, '?') === false) {
                    $url .= '?';
                }
                else {
                    $url .= '&';
                }
                $url .= http_build_query($parameters);
            }
        }
        //Now will redirect the user to show the error:
        if (!headers_sent()) {
            switch($redirection_code) {
                case 300:
                    header("HTTP/1.1 300 Multiple Choices");
                    break;
                case 301:
                    header("HTTP/1.1 301 Moved Permanently");
                    break;
                case 302:
                    header("HTTP/1.1 302 Found");
                    break;
                case 303:
                    header("HTTP/1.1 303 See Other");
                    break;
                case 304:
                    header("HTTP/1.1 304 Not Modified");
                    break;
                case 305:
                    header("HTTP/1.1 305 Use Proxy");
                    break;
                case 306:
                    header("HTTP/1.1 306 Switch Proxy");
                    break;
                case 307:
                    header("HTTP/1.1 307 Temporary Redirect");
                    break;
                default:
                    //nothing to do
                    break;
            }
            header('Location: ' . $url);
        } else {
            print '
    <SCRIPT LANGUAGE=JAVASCRIPT>
      document.location.href = "' . $url . '";
    </SCRIPT>
        ';
        }
        exit;
    }    
    
}