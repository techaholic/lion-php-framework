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

class __HtmlComponentRender extends __ComponentRenderEngine {

    public function startRender() {
        
        if(__Client::getInstance()->getRequestType() != REQUEST_TYPE_XMLHTTP) {
            if(__ApplicationContext::getInstance()->hasProperty('INCLUDE_LION_JS')) {
                $include_lion_js = __ApplicationContext::getInstance()->getPropertyContent('INCLUDE_LION_JS');
            }
            else {
                $include_lion_js = true;
            }
            if($include_lion_js) {
                $local_js_lib = __ApplicationContext::getInstance()->getPropertyContent('JS_LIB_DIR');
                $lion_js_file = __UrlHelper::resolveUrl(__UrlHelper::glueUrlParts($local_js_lib, 'lion.js'));
                __FrontController::getInstance()->getResponse()->prependContent('<script language="javascript" type="text/javascript" src="' . $lion_js_file . '"></script>' . "\n", 'lion-js');
            }
        }

        $response_writer_manager = __ResponseWriterManager::getInstance();
        if($response_writer_manager->hasResponseWriter('javascript')) {
            $javascript_response_writer = $response_writer_manager->getResponseWriter('javascript');
        }
        else {
            $javascript_response_writer = new __JavascriptOnDemandResponseWriter('javascript');
            $response_writer_manager->addResponseWriter($javascript_response_writer);
        }
        
        if(!$javascript_response_writer->hasResponseWriter('setup-client-event-handler')) {
            $setup_client_event_handler_rw = new __JavascriptOnDemandResponseWriter('setup-client-event-handler');
            $js_code = "\n" . '(__ClientEventHandler.getInstance()).setCode("' . __CurrentContext::getInstance()->getId() . '");' . "\n";
            if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
                $js_code .= "(__ClientEventHandler.getInstance()).setDebug(true);\n";
                if(__ApplicationContext::getInstance()->getPropertyContent('DEBUG_AJAX_CALLS') == true) {
                    if(strtoupper(__ApplicationContext::getInstance()->getPropertyContent('DEBUGGER')) == 'ZEND') {
                        $client_ip  = $_SERVER['REMOTE_ADDR'];
                        $debug_port = __ApplicationContext::getInstance()->getPropertyContent('ZEND_DEBUG_PORT');
                        $debug_url  = 'index.ajax?' . 'start_debug=1&debug_port=' . $debug_port . '&debug_fastfile=1&debug_host=' . $client_ip . '&send_sess_end=1&debug_stop=1&debug_url=1&debug_new_session=1&no_remote=1';
                        $js_code .= "(__ClientEventHandler.getInstance()).setUrl('" . $debug_url . "');\n";
                    }
                }
            }
            $setup_client_event_handler_rw->addJsCode($js_code);
            $javascript_response_writer->addResponseWriter($setup_client_event_handler_rw);
        }
        parent::startRender();
    }
    
    public function endRender() {
        parent::endRender();
        $async_message = __ClientNotificator::getInstance()->getStartupNotification($this->_view_code);
        if($async_message != null && ($async_message->getHeader()->getStatus() != __AsyncMessageHeader::ASYNC_MESSAGE_STATUS_OK || $async_message->hasPayload())) {
            $response_writer_manager = __ResponseWriterManager::getInstance();
            if($response_writer_manager->hasResponseWriter('javascript')) {
                $javascript_response_writer = $response_writer_manager->getResponseWriter('javascript');
            }
            else {
                $javascript_response_writer = new __JavascriptOnDemandResponseWriter('javascript');
                $response_writer_manager->addResponseWriter($javascript_response_writer);
            }
            if($response_writer_manager->hasResponseWriter('setup-client-view')) {
                $setup_client_view_rw = $response_writer_manager->getResponseWriter('setup-client-view');
            }
            else {
                $setup_client_view_rw = new __JavascriptOnDemandResponseWriter('setup-client-view');
                $setup_client_view_rw->setLoadAfterDomLoaded(false);
                $javascript_response_writer->addResponseWriter($setup_client_view_rw);
            }
            $javascript_response_writer->addJsCode('__MessageProcessor.process(Object.extend(new __Message(), ' . $async_message->toJson() . '));', __JavascriptOnDemandResponseWriter::JS_CODE_POSITION_BOTTOM);
        }
    }

}

