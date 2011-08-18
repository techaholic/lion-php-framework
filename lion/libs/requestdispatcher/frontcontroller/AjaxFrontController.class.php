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
 * This is the front controller designed to dispatch AJAX request due to ui events
 *
 */
class __AjaxFrontController extends __HttpFrontController {
    
    /**
     * This method process an AJAX request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        $ui_event = __EventResolver::getInstance()->resolveEvent();
        if($ui_event != null) {
            $view_code = $ui_event->getComponent()->getViewCode();
            $event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
            $event_handler->handleEvent($ui_event);
        }
    }

    /**
     * Redirect the web flow to the given uri
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest &$request
     */
    public function redirect($uri, __IRequest &$request = null, $redirection_code = null) {
        if(is_string($uri)) {
            $uri = __UriFactory::getInstance()->createUri($uri);
        }
        else if(!$uri instanceof __Uri) {
            throw __ExceptionFactory::getInstance()->createException('Unexpected type for uri parameter: ' . get_class($uri));
        }
        $url = $uri->getUrl();
        $message = new __AsyncMessage();
        $message->getHeader()->setLocation($url);
        $this->getResponse()->addContent($message->toJson());
        $client_notificator = __ClientNotificator::getInstance();
        //notify to client:
        $client_notificator->notify();
        //clear dirty components to avoid notify again in next requests:
        $client_notificator->clearDirty();
    }
    
    
    /**
     * Alias of redirect
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest &$request
     */
    public function forward($uri, __IRequest &$request = null) {
        $this->redirect($uri, $request);
    }
    
}