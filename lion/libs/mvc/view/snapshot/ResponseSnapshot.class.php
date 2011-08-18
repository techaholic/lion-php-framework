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
 * @package    View
 * 
 */

class __ResponseSnapshot {

    protected $_response = null;
    protected $_view_snapshots = array();
    protected $_ui_bindings = null;
    
    public function __construct(__IResponse &$response) {
        $this->setResponse($response);
    }
    
    public function setResponse(__IResponse &$response) {
        $response->prepareToSleep();
        $this->_response =& $response;
        $this->_view_snapshots = array();
        $view_codes = $response->getViewCodes();
        foreach($view_codes as $view_code) {
            $view_snapshot = new __ViewSnapshot($view_code);
            $this->addViewSnapshot($view_snapshot);
            unset($view_snapshot);
        }
        $this->_ui_bindings = __UIBindingManager::getInstance()->getCurrentRequestUIBindings();
    }
    
    public function &getResponse() {
        return $this->_response;
    }
    
    public function setViewSnapshots(array &$view_snapshots) {
        $this->_view_snapshots = $view_snapshots;
    }
    
    public function &getViewSnapshots() {
        return $this->_view_snapshots;
    }
    
    public function addViewSnapshot(__ViewSnapshot &$view_snapshot) {
        $this->_view_snapshots[$view_snapshot->getViewCode()] =& $view_snapshot;
    }
    
    public function restoreViews() {
         foreach($this->_view_snapshots as $view_code => $view_snapshot) {
             $view_snapshot->restoreView();
         }
        $ui_binding_manager = __UIBindingManager::getInstance();
        foreach($this->_ui_bindings as $ui_binding) {
            $ui_binding_manager->registerUIBinding($ui_binding);
            unset($ui_binding);
        }
         
    }
    
    public function areViewsRestorable() {
         $return_value = true; //by default we're going to read the view from the cache:
         if(!__AuthenticationManager::getInstance()->isAnonymous()) {
             $return_value = false;
         }
         else {
             $component_handler_manager = __ComponentHandlerManager::getInstance();
             foreach($this->_view_snapshots as $view_code => $view_snapshot) {
                 if($component_handler_manager->hasComponentHandler($view_code) && 
                    $component_handler_manager->getComponentHandler($view_code)->isDirty()) {
                    //do not read from the cache if the component handler is dirty
                     $return_value = false;
                 }
             }
         }
         return $return_value;
    }
    
}
