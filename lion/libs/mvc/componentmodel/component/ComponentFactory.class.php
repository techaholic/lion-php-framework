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
 * Factory for instances implementing the {@link __IComponent} interface
 * 
 * @see __IComponent, __UIComponent, __ComponentSpec, __ICompositeComponent
 *
 */
class __ComponentFactory {
    
    private static $_instance = null;
    
    private function __construct() {
    }
    
    /**
     * Gets a {@link __ComponentFactory} singleton instance
     *
     * @return __ComponentFactory
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ComponentFactory();
        }
        return self::$_instance;
    }
    
	/**
	 * Factory method for component creation according to the given parameters.
	 * 
	 * example of usage
	 * <code>
	 * //create a component by giving a tag name
	 * $commandlink = __ComponentFactory::getInstance()->createComponent("commandlink");
	 * 
	 * //create a component by giving a component spec:
	 * $commandlink_spec = __ComponentSpecFactory::getInstance()->createComponentSpec("commandlink");
	 * $commandlink = __ComponentFactory::createComponent($commandlink_spec);
	 * </code>
	 * 
	 * @param mixed $component_spec_or_tag A {@link __ComponentSpec} instance or a tag name
	 * 
	 * @return __IComponent
	 */
    public function &createComponent($component_spec_or_tag, $component_index = null) {
        $return_value = null;
        if($component_spec_or_tag instanceof __ComponentSpec) {
            $return_value = $this->_doCreateComponent($component_spec_or_tag, $component_index);
        }
        else if(is_string($component_spec_or_tag)) {
            $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($component_spec_or_tag);
            $return_value = $this->_doCreateComponent($component_spec, $component_index);
        }
        return $return_value;
    }    
    
    /**
     * Resolve an unique identifier for a component (using the component spec identifier and the component index (if applicable) as seed)
     * 
     * @param __ComponentSpec $component_spec
     * @param $component_index
     * @return string
     */
    protected function _resolveComponentId(__ComponentSpec $component_spec, $component_index = null) {
        $return_value = 'c' . $component_spec->getId();
        if($component_index != null) {
            $return_value .= '_' . $component_index;
        }
        return $return_value;
    }
    
    /**
     * Internally, the factory uses a {@link __ComponentSpec} instance to create a component.
     *
     * @param __ComponentSpec $component_spec The component spec
     * @return __IComponent
     */
    private function &_doCreateComponent(__ComponentSpec $component_spec, $component_index = null) {
        $component_class = $component_spec->getClass();
        //create an empty component
        $return_value = new $component_class();
        
        //setup component identifiers
        $return_value->setId($this->_resolveComponentId($component_spec, $component_index));
        $return_value->setName($component_spec->getName());
        $return_value->setIndex($component_index);
        
        //setup component properties
        $default_values = $component_spec->getDefaultValues();
        foreach($default_values as $property_name => $property_value) {
            $return_value->$property_name = $property_value;
        }
        $component_interface_spec = $component_spec->getComponentInterfaceSpec();
        if($component_interface_spec != null) {
            if($return_value instanceof __ICompositeComponent) {
                $return_value->setComponentInterfaceSpec($component_interface_spec);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Can not set a component interface spec to a non-composite component:' . $component_spec->getName());       
            }
        }
        //return the component:
        return $return_value;        
    }
    
}