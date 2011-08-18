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
 * @package    Context
 * 
 */

/**
 * This is the factory of context instances. It creates context instance from the given {@link __InstanceDefinition}.
 *
 */
class __InstanceFactory {
    
    private $_context = null;
    
    public function __construct(__Context &$context) {
        $this->_context =& $context;
    }
    
    public function &createInstance(__InstanceDefinition &$instance_definition) {
        $return_value = null;
        //Create an empty instance:
        $instance_class        = $instance_definition->getClass();
        $factory_instance_id   = $instance_definition->getFactoryInstanceId();
        $factory_method        = $instance_definition->getFactoryMethod();
        $constructor_arguments_def = $instance_definition->getConstructorArguments()->toArray(); 
        $constructor_arguments = array();
        foreach($constructor_arguments_def as $constructor_argument_def) {
            $constructor_arguments[] = $this->_getInstanceFromPropertyValue($constructor_argument_def);
        }
        
        if($factory_instance_id != null && $instance_class != null) {
            throw __ExceptionFactory::getInstance()->createException('ERR_AMBIGUOUS_CREATION_METHOD', array($instance_class, $factory_instance_id));
        }
        else if($instance_class != null) {
            if( $factory_method == null) {
                if(count($constructor_arguments) > 0) {
                    $reflection_obj = new ReflectionClass($instance_class); 
                    $return_value = $reflection_obj->newInstanceArgs($constructor_arguments); 
                }
                else {
                    $return_value = new $instance_class();
                }
            }
            else {
                $return_value = call_user_func_array(array($instance_class, $factory_method), $constructor_arguments); 
            }
        }
        else {
            $factory_instance = $this->_context->getContextInstance($factory_instance_id);
            if($factory_instance != null && $factory_method != null) {
                $return_value = call_user_func_array(array($factory_instance, $factory_method), $constructor_arguments); 
            }
            else if($factory_instance == null) {
                throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_FACTORY_SPECIFICATION', array($factory_instance_id, $factory_method));
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_FACTORY_METHOD_REQUIRED', array($factory_instance_id));
            }
        }
        if($return_value != null) {
            $properties = $instance_definition->getProperties();
            $this->injectProperties($properties, $return_value);
        }
        //Execute startup method if any:
        $startup_method = $instance_definition->getStartupMethod();
        if($startup_method != null && method_exists($return_value, $startup_method)) {
            $return_value->$startup_method();
        }
        return $return_value;
    }
    
    public function injectProperties(__PropertiesCollection &$properties, &$instance) {
        $properties_iterator = $properties->getIterator();
        $properties_iterator->first();
        while(!$properties_iterator->isDone()) {
            $name           = $properties_iterator->currentKey();
            $property_value = $properties_iterator->currentItem();
            if (is_array($property_value)) {
                $value = array();
                foreach($property_value as $property_value_key => $property_value_item) {
                    $value[$property_value_key] = $this->_getInstanceFromPropertyValue( $property_value_item );
                }
            }
            else {
                $value = $this->_getInstanceFromPropertyValue( $property_value );
            }
            //Finally will set the property into the instance:                    
            $setter = 'set' . ucfirst($name);
            if(method_exists($instance, $setter)) {
                $instance->$setter($value);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_SETTER_METHOD_NOT_FOUND', array($setter, get_class($instance)));
            }
            unset($value);
            $properties_iterator->next();
        }
    }
    
    protected function &_getInstanceFromPropertyValue( &$property_value ) {
        $return_value = null;
        if( $property_value instanceof __InstanceDefinition ) {
            if ($this->_context->hasInstance($property_value->getId())) {
                $return_value = $this->_context->getContextInstance($property_value->getId());
            }
            else {
                $return_value = $this->createInstance($property_value);
            }
        }
        else if( $property_value instanceof __InstanceReference ) {
            if ($this->_context->hasInstance($property_value->getReferenceId())) {
                $return_value = $this->_context->getContextInstance($property_value->getReferenceId());
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_REF_INSTANCE_NOT_FOUND', array($property_value->getReferenceId()));
            }
        }
        else {
            $return_value =& $property_value;
        }
        return $return_value;
        
    }
    
}