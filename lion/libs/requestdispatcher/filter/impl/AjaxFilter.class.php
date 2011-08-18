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
 * @package    Filter
 * 
 */

class __AjaxFilter extends __Filter {
    
    /**
     * Updates client end-point with values received from request:
     *
     * @param __IRequest $request
     * @param __IResponse $response
     */
    public function preFilter(__IRequest &$request, __IResponse &$response) {
        //update client end-points with values received from request:
        $request_component_values = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_CLIENT_ENDPOINT_VALUES');
        if($request->hasParameter($request_component_values)) {
            $values = $request->getParameter($request_component_values);
            $scape_chars = array('\\n', '\\r', '\\t');
            $double_scape_chars = array('\\\\n', '\\\\r', '\\\\t');
            $values = str_replace($scape_chars, $double_scape_chars, $values);
            $client_values_json_string = stripslashes($values);
            $client_values = json_decode($client_values_json_string, true);
            if(is_array($client_values)) {
                $ui_binding_manager = __UIBindingManager::getInstance();
                foreach($client_values as $id => $value) {
                    if($ui_binding_manager->hasUIBinding($id)) {
                        $ui_binding_manager->getUIBinding($id)->getClientEndPoint()->setValue($value);
                    }
                }
            }
        }

    }
    
    /**
     * Creates an ajax message with current server end-point values
     *
     * @param __IRequest $request
     * @param __IResponse $response
     */
    public function postFilter(__IRequest &$request, __IResponse &$response) {
        $client_notificator = __ClientNotificator::getInstance();
        //notify to client:
        $client_notificator->notify();
        //clear dirty components to avoid notify again in next requests:
        $client_notificator->clearDirty();
    }
    
}

