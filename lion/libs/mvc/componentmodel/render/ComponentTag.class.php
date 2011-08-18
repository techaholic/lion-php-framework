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

class __ComponentTag {
    
    protected $_component_id = null;
    protected $_tag_name     = null;
    protected $_component_writers = array();
    protected $_component_class = null;
    protected $_component_writer_decorators = array();
    protected $_view_classes = array();
    protected $_component_interface = null;

    public function __construct($tag_name) {
        $this->setTagName($tag_name);
    }
    
    public function setTagName($tag_name) {
        $this->_tag_name = $tag_name;
    }
    
    public function getTagName() {
        return $this->_tag_name;
    }
    
    public function setComponentId($component_id) {
        $this->_component_id = $component_id;
    }
    
    public function getComponentId() {
        return $this->_component_id;
    }
    
    public function addComponentWriterClass($client, $component_writer_class, array $component_writer_decorator_classes = array()) {
        $this->_component_writers[$client] = $component_writer_class;
        ksort($component_writer_decorator_classes);
        $this->_component_writer_decorators[$client] = $component_writer_decorator_classes;
    }
    
    public function setComponentInterfaceSpec(__UICompositeComponentInterfaceSpec $ui_component_interface) {    
        $this->_component_interface = $ui_component_interface;
    }
    
    public function getComponentInterfaceSpec() {
        return $this->_component_interface;
    }

    public function addViewDefinition($client, $view_class) {
        $this->_view_classes[$client] = $view_class;
    }

    public function getViewDefinitions() {
        return $this->_view_classes;
    }
    
    public function getViewDefinition($client) {
        $return_value = null;
        if(key_exists($client, $this->_view_classes)) {
            $return_value = $this->_view_classes[$client];
        }
        return $return_value;        
    }
    
    public function getComponentWriterClasses() {
        return $this->_component_writers;
    }
    
    public function getComponentWriterDecoratorClasses($client) {
        $return_value = null;
        if(key_exists($client, $this->_component_writer_decorators)) {
            $return_value = $this->_component_writer_decorators[$client];
        }
        return $return_value;
    }
    
    public function getComponentWriterClass($client) {
        $return_value = null;
        if(key_exists($client, $this->_component_writers)) {
            $return_value = $this->_component_writers[$client];
        }
        return $return_value;
    }
        
    public function setComponentClass($component_class) {
        $this->_component_class = $component_class;
    }

    public function getComponentClass() {
        return $this->_component_class;
    }
    
}