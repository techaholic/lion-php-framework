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

class __Renderer implements __IRenderer {
    
    protected $_register_as_validator = false;
    
    protected $_component_writer = null;
    
    protected $_component = null;
    
    protected $_event_handler = null;
    
    protected $_child_renderers = array();
    
    protected $_raise_create_event = false;
    
    public function __construct(__IComponent &$component, __EventHandler &$event_handler, __IComponentWriter &$component_writer = null) {
        $this->setComponent($component);
        $this->setEventHandler($event_handler);
        if($component_writer != null) {
            $this->setComponentWriter($component_writer);
        }
    }
    
    public function setEventHandler(__EventHandler &$event_handler) {
        $this->_event_handler =& $event_handler;
    }
    
    public function &getEventHandler() {
        return $this->_event_handler;
    }
    
    public function setComponentWriter(__IComponentWriter &$component_writer) {
        $this->_component_writer =& $component_writer;
    }
    
    public function &getComponentWriter() {
        return $this->_component_writer;
    }
    
    public function setComponent(__IComponent &$component) {
        $this->_component =& $component;
    }
    
    public function &getComponent() {
        return $this->_component;
    }

    public function addRenderer(__IRenderer &$renderer) {
        $this->_child_renderers[] =& $renderer;
    }
    
    public function addOutputContent($output_content) {
        $plain_content_renderer = new __PlainContentRenderer($output_content);
        $this->_child_renderers[] =& $plain_content_renderer;
    }
    
    public function render() {
        if($this->_component != null && $this->_component_writer != null && $this->_event_handler != null) {
            $enclosed_content = '';
            if($this->_component_writer->canRenderChildrenComponents($this->_component)) {            
                //get partial results from child renderer:
                foreach($this->_child_renderers as &$renderer) {
                    $enclosed_content .= $renderer->render();
                }
            }
            return $this->_renderComponent($enclosed_content);
        }        
    }
    
    public function raiseCreateEvent() {
        //call the create event
        if($this->_event_handler->isEventHandled('create', $this->_component->getName())) {
            $event = new __UIEvent('create', null, $this->_component);
            $this->_event_handler->handleEvent($event);
        }
        if($this->_component instanceof __IValidator) {
            $this->_register_as_validator = true;
        }
    }
    
    protected function _renderComponent($enclosed_content) {
        $return_value = $enclosed_content;
        //if the current renderer contains a validator, register it if it's being created:
        if($this->_register_as_validator && $this->_component instanceof __IValidator ) {
            $component_to_validate = $this->_component->getComponentToValidate();
            $component_to_validate->registerValidator($this->_component);
        }
        if($this->_component instanceof __ISubmitter && __Client::getInstance()->getRequestType() != REQUEST_TYPE_XMLHTTP) {
            $request = __FrontController::getInstance()->getRequest();
            $this->_component->setLastRequest($request);
        }
        //call the init event
        if($this->_event_handler->isEventHandled('beforeRender', $this->_component->getName())) {
            $event = new __UIEvent('beforeRender', null, $this->_component);
            $this->_event_handler->handleEvent($event);
        }
        if($this->_component_writer != null) {
            $return_value = $this->_component_writer->startRender($this->_component) . 
                            $this->_component_writer->renderContent($enclosed_content, $this->_component) . 
                            $this->_component_writer->endRender($this->_component);
        }
        //call the init event
        if($this->_event_handler->isEventHandled('afterRender', $this->_component->getName())) {
            $event = new __UIEvent('afterRender', null, $this->_component);
            $this->_event_handler->handleEvent($event);
        }
        //last, mark the component as rendered (to avoid to be removed from the component pool if not rendered)
        $this->_event_handler->getComponentHandler()->markComponentAsRendered($this->_component->getName(), $this->_component->getIndex());        
        return $return_value;
    }
    
    
    
}