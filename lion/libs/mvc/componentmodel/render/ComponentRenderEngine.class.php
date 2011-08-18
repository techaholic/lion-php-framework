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

/**
 * Class in charge of orchest the components rendering process.
 * 
 */
abstract class __ComponentRenderEngine implements __IComponentRenderEngine {

    protected $_view_code         = null;
    protected $_component_handler = null;
    protected $_indexes           = array();
    protected $_child_renderers   = array();
    protected $_renderers_stack   = null;
    protected $_properties_stack  = null;
    protected $_created_components = array();
    
    /**
     * true if this is the first time rendering the components, which means that the component handler has been created within this render process
     *
     * @var bool
     */
    private $_first_time_execution = true;
    
    final public function __construct($view_code) {
        $this->_view_code = $view_code;
        $this->_renderers_stack  = new __Stack();
        $this->_properties_stack = new __Stack();
        if(__ComponentHandlerManager::getInstance()->hasComponentHandler($view_code)) {
            $this->_first_time_execution = false;
            $this->_component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($view_code);
        }
        else {
            $this->_component_handler = __ComponentHandlerManager::getInstance()->createComponentHandler($view_code);
        }
        $this->_event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
        $request = __ActionDispatcher::getInstance()->getRequest();
        if($request != null) {
            if($request->hasParameter('PARENT_VIEW_CODE')) {
                $this->_event_handler->setParentViewCode($request->getParameter('PARENT_VIEW_CODE'));
            }
            if($request->hasParameter('ACTIONBOX_ID')) {
                $this->_event_handler->setContainerActionBoxId($request->getParameter('ACTIONBOX_ID'));
            }
        }
    }
    
    public function startRender() {
        //the first element on the renderers stack will be the render engine itself
        //Note: take into account that the stack cound not be empty, otherwise there is an inconsistence problem 
        //like an unexpected closed tag or similar
        $this->_renderers_stack->push($this); 
        ob_start(array($this, 'addOutputContent'));
    }

    public function endRender() {
        ob_end_flush();
        if($this->_renderers_stack->count() == 1) {
            $this->render();
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_INCONSISTENCE_RENDERING_ERROR');
        }
    }

    public function closeRender() {
        //**After Render** remove all the components that has not been rendered
        $this->_component_handler->expireNotRenderedComponents();
        //Them, set the view code within the response:
        if($this->_component_handler->hasPoolableComponents()) {
            $response = __FrontController::getInstance()->getResponse();
            if($response != null) {
                if($this->_component_handler->isDirty()) {
                    $response->addViewCode($this->_view_code, false);
                }
                else {
                    $response->addViewCode($this->_view_code, true);
                }
            }
        }
    }
    
    
    public function markComponentSingleTag(__ComponentSpec $component_spec) {
        //flush output content to the current ob callback
        ob_end_flush();
        $component = $this->_wakeupComponent($component_spec);
        if($component != null) {
            $component_writer = $component_spec->getWriter();
            $renderer  = new __Renderer($component, $this->_event_handler, $component_writer);
            $current_renderer = $this->_renderers_stack->peek();
            $current_renderer->addRenderer($renderer);
            if($this->_isComponentBeingCreated($renderer->getComponent())) {
                $renderer->raiseCreateEvent();
            }
        }
        ob_start(array($current_renderer, 'addOutputContent'));
    }
    
    public function markComponentBeginTag(__ComponentSpec $component_spec) {
        //flush output content to the current ob callback
        ob_end_flush();
        //get the component and setup his renderer
        $component = $this->_wakeupComponent($component_spec);
        if($component != null) {
            $component_writer = $component_spec->getWriter();
            $renderer  = new __Renderer($component, $this->_event_handler, $component_writer);
            //add a reference to the new renderer in the current one:
            $this->_renderers_stack->peek()->addRenderer($renderer);
            //push the new renderer on the stack to act as the current one:
            $this->_renderers_stack->push($renderer);
            //set the ob callback to the new renderer
        }
        ob_start(array($renderer, 'addOutputContent'));
    }
    
    public function markComponentEndTag(__ComponentSpec $component_spec) {
        //send output buffer to the current ob callback
        ob_end_flush();
        //pop the current renderer from the renderers stack
        $renderer = $this->_renderers_stack->pop();
        if($this->_isComponentBeingCreated($renderer->getComponent())) {
            $renderer->raiseCreateEvent();
        }
        //set the ob callback to the previous renderer in the stack
        if($this->_renderers_stack->count() > 0) {
            $previous_renderer = $this->_renderers_stack->peek();
            ob_start(array($previous_renderer, 'addOutputContent'));
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_INCONSISTENCE_RENDERING_ERROR');
        }
    }
    
    protected function _isComponentBeingCreated(__IComponent &$component) {
        return key_exists($component->getId(), $this->_created_components); 
    }
    
    public function markPropertyBeginTag($property) {
        $this->_properties_stack->push($property);
        ob_start(array($this, 'setProperty'));
    }
    
    public function markPropertyEndTag() {
        $component_property = $this->_properties_stack->peek();
        if($component_property != null) {
            ob_end_flush();
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_INCONSISTENCE_RENDERING_ERROR');
        }
    }
    
    public function setProperty($buffer = null) {
        $component      = $this->_renderers_stack->peek()->getComponent();
        $property       = $this->_properties_stack->pop();
        $component->$property = $buffer;
        return '';
    }

    protected function &_wakeupComponent(__ComponentSpec $component_spec) {
        $component_name = $component_spec->getName();
        if($component_spec->isArray()) {
            $component_index = $this->_getNextIndex($component_name);
        }
        else {
            $component_index = null;
        }
        //if the component exists, will get it from the component handler
        if($this->_component_handler->hasComponent($component_name, $component_index)) {
            $component = $this->_component_handler->getComponent($component_name, $component_index);
        }
        //else will create a new one just if this is the first time rendering the page:
        else {
            $component = $this->_createComponent($component_spec, $component_index);
            $this->_created_components[$component->getId()] = true;
        }
        return $component;
    }
    
    private function &_createComponent(__ComponentSpec $component_spec, $component_index = null) {
        $component = __ComponentFactory::getInstance()->createComponent($component_spec, $component_index);
        if($this->_renderers_stack->count() > 1) {
            $parent_component = $this->_renderers_stack->peek()->getComponent();
        }
        else {
            $parent_component = null;
        }
        $this->_component_handler->registerComponent($component);
        if( $component instanceof __IPoolable && $component->getPersist() ) {
            $writer = $component_spec->getWriter();
            if($writer != null) {
                $writer->bindComponentToClient($component);
            }
            if($parent_component != false) {
                $component->setContainer($parent_component);
            }
        }
        else {
            if($parent_component instanceof __IPoolable) {
                if($this->_isComponentBeingCreated($parent_component)) {
                    $component->setContainer($parent_component);
                }
            }
            else if($parent_component != null) {
                $component->setContainer($parent_component);
            }
        }
        return $component;
    }

    private function _getNextIndex($component_name) {
        $return_value = 0;
        if(key_exists($component_name, $this->_indexes)) {
            $return_value = $this->_indexes[$component_name];
        }
        $this->_indexes[$component_name] = $return_value + 1;
        return $return_value;
    }
    
    public function addRenderer(__IRenderer &$renderer) {
        $this->_child_renderers[] =& $renderer;
    }
    
    public function addOutputContent($buffer) {
        $plain_content_renderer = new __PlainContentRenderer($buffer);
        $this->_child_renderers[] =& $plain_content_renderer;
        return '';
    }

    public function render() {
        $return_value = '';
        //initialize event handler
        if($this->_first_time_execution) {
            $this->_event_handler->create();
            if($this->_event_handler instanceof __ICompositeComponentEventHandler) {
                $this->_event_handler->setupProperties();
            }
        }
        $this->_event_handler->beforeRender();
        //do the render
        foreach($this->_child_renderers as &$renderer) {
            $return_value .= $renderer->render();
        }
        $this->_exposeEventHandlerMethods();
        $this->_event_handler->afterRender();
        //print the result:
        echo $return_value;
    }
    
    protected function _exposeEventHandlerMethods() {
        if($this->_event_handler != null) {
            $event_handler_class = get_class($this->_event_handler);
            $annotations_collection = __AnnotationParser::getInstance()->getAnnotations($event_handler_class);
            $annotations = $annotations_collection->toArray();
            foreach($annotations as $annotation) {
                switch (strtoupper($annotation->getName())) {
                    case 'REMOTESERVICE':
                        $this->_generateRemoteServiceCode($annotation->getMethod(), $annotation->getArguments());
                        break;
                    default:
                        break; 
                }
            }
        }
    }
    
    protected function _generateRemoteServiceCode($method_name, $arguments = array()) {
        $component_name = $method_name;
        $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_event_handler->getViewCode());
        //get the remote_service_writer:
        $remote_service_spec = __ComponentSpecFactory::getInstance()->createComponentSpec('remoteservice');
        $remote_service_writer = $remote_service_spec->getWriter();
        if($component_handler->hasComponent($component_name)) {
            $remote_service = $component_handler->getComponent($component_name);
        }
        else {
            $remote_service = __ComponentFactory::getInstance()->createComponent($remote_service_spec);
            $remote_service->setName($component_name);
            foreach($arguments as $argument_name => $argument_value) {
                $remote_service->$argument_name = $argument_value;
            }
            $component_handler->registerComponent($remote_service);
        }
        $remote_service_writer->bindComponentToClient($remote_service);
        $remote_service_writer->startRender($remote_service);
    }
    
    
}
