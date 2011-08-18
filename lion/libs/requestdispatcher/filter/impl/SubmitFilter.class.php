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

class __SubmitFilter extends __Filter {
    
    public function preFilter(__IRequest &$request, __IResponse &$response) {
        //check for uploaded files
        $this->_checkUploadCode($request);
        //check if a submit code is present, corresponding to a submitted form
        $this->_checkSubmitCode($request);
    }
    
    protected function _checkUploadCode(__IRequest &$request) {
        if($request->hasParameter('APC_UPLOAD_PROGRESS')) {
            $async_upload = true;
        }
        else {
            $async_upload = false;
        }
        $files = $request->getFiles();
        if(is_array($files)) {
            foreach($files as $file_id => $file) {
                if(__ComponentPool::getInstance()->hasComponent($file_id)) {
                    $uploader_component = __ComponentPool::getInstance()->getComponent($file_id);
                    if($file['error'] == UPLOAD_ERR_OK) {
                        $uploader_component->setFilename($file['name']);
                        $temporal_file = __Lion::getInstance()->getRuntimeDirectives()->getCacheDirectory() . DIRECTORY_SEPARATOR . uniqid('img_');
                        $uploader_component->setTempFile($temporal_file);
                        $uploader_component->setSize($file['size']);
                        $uploader_component->setType($file['type']);
                        if($uploader_component->validate()) {
                            $uploader_component->setStatus(__IUploaderComponent::UPLOAD_STATUS_DONE);
                            if($async_upload == true) {
                                move_uploaded_file($file['tmp_name'], $temporal_file);
                                $uploader_component->setTempFile($temporal_file);
                            }
                            $view_code = $uploader_component->getViewCode();
                            $event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
                            if($event_handler->isEventHandled('afterUpload', $uploader_component->getName())) {
                                $upload_event = new __UIEvent('afterUpload', null, $uploader_component);
                                $event_handler->handleEvent($upload_event);
                            }
                        }
                        else {
                            $uploader_component->setStatus(__IUploaderComponent::UPLOAD_STATUS_ERROR);
                        }
                    }
                }
            }
        }
    }
    
    protected function _checkSubmitCode(__IRequest &$request) {
        $request_submit_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_SUBMIT_CODE');
        if($request->hasParameter($request_submit_code)) {
            $submit_code = $request->getParameter($request_submit_code);
            if(__ComponentPool::getInstance()->hasComponent($submit_code)) {
                $submitter_component = __ComponentPool::getInstance()->getComponent($submit_code);
                $view_code = $submitter_component->getViewCode();
                __ComponentHandlerManager::getInstance()->getComponentHandler($view_code)->setDirty(true);
                $event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
                $this->_updateClientEndpointsFromRequest($request, $submitter_component);
                if(!$this->_validateSubmitAndPurifyRequest($submitter_component, $request)) {
                    $request = $submitter_component->getLastRequest();
                }
                else if($event_handler->isEventHandled('submit', $submitter_component->getName())) {
                    $submit_event = new __UIEvent('submit', null, $submitter_component);
                    $event_handler->handleEvent($submit_event);
                }
            }
        }
    }
    
    protected function _updateClientEndpointsFromRequest(__IRequest &$request, __IComponent &$submitter_component) {
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
        //update value holder components from request values:
        $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($submitter_component->getViewCode());
        if($component_handler != null) {
            $request_parameters = $request->getParameters();
            foreach($request_parameters as $parameter_id => $parameter_value) {
                if(is_string($parameter_value)) {
                    $parameter_value = stripslashes($parameter_value);
                    if($component_handler->hasComponent($parameter_id)) {
                        $component = $component_handler->getComponent($parameter_id);
                        if($component instanceof __IValueHolder) {
                            $component->setValue($parameter_value);
                        }
                    }
                }
            }
        }
    }
    
    protected function _validateSubmitAndPurifyRequest(__IComponent &$component, __IRequest &$request) {
        $return_value = true;
        if( $component instanceof __IContainer ) {
            //validate contained value holders:            
            $value_holder_components = $component->getValueHolderComponents();
            foreach($value_holder_components as $value_holder_component) {
                if(!$value_holder_component->validate()) {
                    //reset uploaders on submit validation:
                    if( $value_holder_component instanceof __IUploaderComponent ) {
                        $value_holder_component->clearFile();
                    }
                    $return_value = false;
                }
                //overwrite the current parameter with the purified value from the component:
                $request->addParameter($value_holder_component->getName(), $value_holder_component->getValue());
            }
            //validate the submitter component:
            if(!$component->validate()) {
                $return_value = false;
            }
        }
        return $return_value;
    }

}