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
 * Abstract class implementing the __IComponent interface.
 * 
 * @see __IComponent, __UIContainer
 *
 */
abstract class __UIComponent implements __IComponent {
    
    /**
     * Component identifier
     *
     * @var string
     */
    protected $_id           = null;
    
    /**
     * Component name
     *
     * @var string
     */
    protected $_name         = null;
    
    /**
     * An alias for current component. Alias can be used to set a human readable name associated to the component
     *
     * @var string
     */
    protected $_alias        = null;
    
    /**
     * Component index (if applicable)
     *
     * @var string
     */
    protected $_index        = null;
    
    /**
     * Component container (if applicable)
     *
     * @var __UIComponent
     */
    protected $_container  = null;
    
    /**
     * If the component is enabled or disabled
     *
     * @var bool
     */
    protected $_disabled   = false;

    /**
     * If the component is visible or not
     *
     * @var bool
     */
    protected $_visible   = true;
    
    /**
     * Component properties
     *
     * @var array
     */
    protected $_properties   = array();
    
    /**
     * The view code of the container view
     *
     * @var string
     */
    protected $_view_code    = null;
    
    /**
     * binding codes associated to current component
     *
     * @var array
     */
    protected $_binding_codes = array();    
    
    protected $_validator_ids = array();
    
    protected $_persist = true;
    
    protected $_progress = null;

    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Read only property that returns an unique identifier for current component
     *
     * @return string
     */
    public function getId() {
        if($this->_id === null) {
            $this->_id = uniqid('c');
        }
        return $this->_id;
    }
    
    /**
     * Sets the code of container view
     *
     * @param string $view_code The view code
     */
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    /**
     * Gets the code of container view
     *
     * @return string
     */
    public function getViewCode() {
        return $this->_view_code;
    }
    
    /**
     * Sets a container for current component
     *
     * @param __IContainer $container The component container
     */
    public function setContainer(__IContainer &$container) {
        //protect to infinite recursion
        if($this->_container == null || $this->_container->getId() !== $container->getId()) {
            $this->_container =& $container;
            $container->addComponent($this);
        }
    }

    /**
     * Adds a binding code for a {@link __UIBinding} associated to current component
     *
     * @param string $binding_code
     */
    public function addBindingCode($binding_code) {
        if(!in_array($binding_code, $this->_binding_codes)) {
            $this->_binding_codes[] = $binding_code;
        }
    }

    /**
     * Gets all the binding codes associated to current component
     *
     * @return array
     */
    public function getBindingCodes() {
        return $this->_binding_codes;
    }
    
    /**
     * Forces an update for all client end-points associated with current component.
     *
     */
    public function updateClient() {
        foreach($this->_binding_codes as $binding_code) {
            $ui_binding = __UIBindingManager::getInstance()->getUIBinding($binding_code);
            if($ui_binding != null) {
                $ui_binding->synchronizeClient();
            }
        }
    }
    
	/**
	 * Gets the first parent container of the given class
	 *
	 * @return __IComponent the first parent container of specified class, else null if no components are found
	 */
	public function &getParentContainerByClass($class_name) {
		$container =& $this->getContainer();
		while(!$container instanceof $class_name && $container != null) {
			$container =& $container->getContainer();
		}
		return $container;
	}        
	
    public function &getContainer() {
        return $this->_container;
    }
    
    public function hasContainer() {
        $return_value = false;
        if($this->_container != null) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function addProperty($property_name, $property_value) {
        $this->_properties[$property_name] = $property_value;
    }
    
    public function getProperties() {
        return $this->_properties;
    }
    
    public function setName($name) {
        $this->_name = $name;
    }

    public function getName() {
        return ($this->_name != null)? $this->_name : $this->_id;
    }
    
    public function setAlias($alias) {
        $this->_alias = $alias;
    }
    
    public function getAlias() {
	    if($this->_alias != null) {
	        $return_value = $this->_alias;
	    }
	    else {
	        $return_value = $this->_name;
	    }
	    return $return_value;
    }
    
    public function setIndex($index) {
        $this->_index = $index;
    }
    
    public function getIndex() {
        return $this->_index;
    }

    public function setDisabled($disabled) {
        $this->_disabled = $this->_toBool($disabled);
    }
    
    public function setEnabled($enabled) {
        $this->_disabled = !$this->_toBool($enabled);
    }
    
    public function getDisabled() {
        return $this->_disabled;
    }

    public function getEnabled() {
        return !$this->_disabled;
    }
    
    public function setVisible($visible) {
        $this->_visible = $this->_toBool($visible);
    }
    
    public function getVisible() {
        return $this->_visible;
    }

    public function resetValidation() {
        foreach($this->_validator_ids as $validator_id => $dummy) {
            if(__ComponentPool::getInstance()->hasComponent($validator_id)) {
                $validator = __ComponentPool::getInstance()->getComponent($validator_id);
                $validator->resetValidation();
            }
        }
    }
    
    public function validate() {
        $return_value = true;
        if(count($this->_validator_ids) > 0) {
            foreach($this->_validator_ids as $validator_id => $dummy) {
                if(__ComponentPool::getInstance()->hasComponent($validator_id)) {
                    $validator = __ComponentPool::getInstance()->getComponent($validator_id);
                    $return_value = $return_value && $validator->validate();
                }
            }
        }
        else {
            $event_handler = __EventHandlerManager::getInstance()->getEventHandler($this->_view_code);
            if($event_handler->isEventHandled('validate', $this->getName())) {
                $validate_event = new __UIEvent('validate', null, $this);
                $return_value = $event_handler->handleEvent($validate_event);
            }
        }
        return $return_value;
    }
    
    public function registerValidator(__IValidator &$validator) {
        $this->_validator_ids[$validator->getId()] = true;
    }
    
    public function __toString() {
        return "(component: $this->_name)";
    }
    
    public function hasProperty($property_name) {
        $return_value = false;
        $property_key = strtoupper($property_name);
        if(property_exists($this, $property_name)) {
            $return_value = true;
        }
        else if(method_exists($this, 'get' . ucfirst($property_name))) {
            $return_value = true;
        }
        else if(key_exists($property_key, $this->_properties)) {
            $return_value = true;
        }
        return $return_value;     
    }
    
    public function getProperty($property_name) {
        $return_value = null;
        $property_key = strtoupper($property_name);
        if(property_exists($this, $property_name)) {
            $return_value = $this->$property_name;
        }
        else if(method_exists($this, 'get' . ucfirst($property_name))) {
            $return_value = call_user_func_array(array($this, 'get' . ucfirst($property_name)), array());
        }
        else if(key_exists($property_key, $this->_properties)) {
            $return_value = $this->_properties[$property_key];
        }
        return $return_value;
    }
    
    public function setProperty($property_name, $property_value) {
        if(property_exists($this, $property_name)) {
            $this->$property_name = $property_value;
        }
        else if(method_exists($this, 'set' . ucfirst($property_name))) {
            call_user_func_array(array($this, 'set' . ucfirst($property_name)), array($property_value));
        }
        else {
            $this->_properties[strtoupper($property_name)] = $property_value;
        }
    }
    
    public function __get($property_name) {
        return $this->getProperty($property_name);
    }

    public function __set($property_name, $property_value) {
        $this->setProperty($property_name, $property_value);
    }
    
    public function __call($method_name, $parameters) {
        $return_value = null;
        $real_method_name = 'do' . ucfirst($method_name);
        //now will redirect to the component method itself:
        if(method_exists($this, $real_method_name)) {
            $return_value = call_user_func_array(array($this->_component, $real_method_name), $parameters);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_METHOD_NOT_FOUND', array(get_class($this), $method_name));
        }
        return $return_value;
    }    
     
  	protected function _toBool($value) {
        if(is_string($value)) {
            switch(strtoupper($value)) {
                case 'TRUE':
                case 'YES':
                case 'ON':
                    $value = true;
                    break;
                default:
                    $value = false;
                    break;
            }
        }
        else {
            $value = (bool) $value;
        }
        return $value;	    
	}
	
	protected function _toArray($value) {
        $return_value = array();
        if(is_string($value)) {
            $value = preg_split('/,/', $value);
            foreach($value as $parameter) {
                $parameter = preg_split('/\s*\=\s*/', $parameter);
                if(count($parameter) == 2) {
                    $return_value[self::_parseValue($parameter[0])] = self::_parseValue($parameter[1]);
                }
                else {
                    $return_value[] = reset($parameter);
                }
            }
        }
        else if(is_array($value)) {
            $return_value = $value;
        }
        return $return_value;	    
	}
	
	public function setPersist($persist) {
	    $this->_persist = $this->_toBool($persist);
	}
	
	public function getPersist() {
	    return $this->_persist;
	}
	
    public function isEventHandled($event_name) {
        return false; //by default
    }
    
    public function getHandledEvents() {
        return array();
    }
    
    public function handleEvent(__UIEvent &$event) {
        return true;
    }
    
    public function setProgress($progress) {
        if(is_numeric($progress)) {
            if($progress < 0) {
                $progress = 0;
            }
            else if($progress > 100) {
                $progress = 100;
            }
            $this->_progress = $progress;
            __ClientNotificator::getInstance()->notifyProgress($this);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong progress value: ' . $progress . '. Progress must be a numeric value between 0 and 100.');
        }
    }

    public function getProgress() {
        return $this->_progress;
    }
    
    final public function handleCallback(__IRequest &$request) {
        $event_handler = __EventHandlerManager::getInstance()->getEventHandler($this->_view_code);
        if($event_handler->isEventHandled('callback', $this->getName())) {
            $validate_event = new __UIEvent('callback', $request, $this);
            $event_handler->handleEvent($validate_event);
        }
    }
    
    
}