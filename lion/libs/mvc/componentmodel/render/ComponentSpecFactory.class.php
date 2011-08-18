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


final class __ComponentSpecFactory {
    
    private static $_instance = null;
    
    private $_component_tags = array();
    
    private $_client = 'default';
    
    private function __construct() {
        $component_tags = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('ui-component-tags');
        if( is_array($component_tags) ) {
            $this->_component_tags = $component_tags;
        }
    }
    
    /**
     * Gets a {@link __ComponentSpecFactory} singleton instance
     *
     * @return __ComponentSpecFactory
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ComponentSpecFactory();
        }
        return self::$_instance;
    }
    
    public function &createComponentSpec($tag_name) {
        $return_value = null;
        $tag_name = strtolower($tag_name);
        if(key_exists($tag_name, $this->_component_tags)) {
            $component_tag = $this->_component_tags[$tag_name];
            $component_class        = $component_tag->getComponentClass();
            $component_writer_class = $component_tag->getComponentWriterClass($this->_client);
            $component_writer_decorators_classes = $component_tag->getComponentWriterDecoratorClasses($this->_client);
            $return_value = new __ComponentSpec($tag_name, $component_class);
            if($component_writer_class != null) {
                $component_writer = new $component_writer_class();
                foreach($component_writer_decorators_classes as $component_writer_decorators_class) {
                    $component_decorator = new $component_writer_decorators_class($component_writer);
                    $component_writer = $component_decorator;
                    unset($component_decorator);
                }
                $view_definition = $component_tag->getViewDefinition($this->_client);
                if($view_definition != null) {
                    if($component_writer instanceof __ICompositeWriter) {
                        $component_writer->setViewDefinition($view_definition);
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Class ' . $component_writer_class . ' must implement __ICompositeWriter in order to have a view assigned to.');
                    }
                }
                $return_value->setWriter($component_writer);
            }
            $component_interface_spec = $component_tag->getComponentInterfaceSpec();
            if($component_interface_spec != null) {
                $return_value->setComponentInterfaceSpec($component_interface_spec);
            }
            
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_UI_COMPONENT_TAG', array($tag_name));
        }
        return $return_value;
    }
    
    
}