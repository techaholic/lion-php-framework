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
 * This is the front controller designed to dispatch remote service request
 *
 */
class __RemoteServiceFrontController extends __HttpFrontController {
    
    /**
     * This method process an AJAX request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        try {
            $return_value = $this->_resolveAndCallRemoteService($request);
            if($return_value != null) {
                if(function_exists('json_encode')) {
                    $return_value = json_encode($return_value);
                }
                else {
                    $services_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
                    $return_value = $services_json->encode($return_value);
                }
            }
            $response->addContent($return_value);
        }
        catch (Exception $e) {
            $response->addHeader("HTTP/1.0 500 Internal Server Error");
            $response->addContent(json_encode($e->getMessage()));
        }
    }
    
    protected function _resolveAndCallRemoteService(__IRequest &$request) {
        $return_value = null;
        $request = __FrontController::getInstance()->getRequest();
        if($request->hasParameter('service_name')) {
            $service_name = $request->getParameter('service_name');
            $model_proxy  = __ModelProxy::getInstance();
            if($model_proxy->isRemoteService($service_name)) {
                $model_service = $model_proxy->getModelService($service_name);
                $return_value  = $model_service->callAsRemoteService($request);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Service ' . $service_name . ' is not declared as remote');
            }
        }
        return $return_value;
    }
    
}