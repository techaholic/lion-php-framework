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
 * @package    Configuration
 * 
 */


/**
 * This is the abstract class for all configuration components.
 * 
 * There are 2 kinds of configuration components:<br>
 *  - complex configuration components: Components that can contain more configuration components. i.e. {@link __ConfigurationSection} instances.<br>
 *  - simple configuration components: Components that can not contain more configuration components. i.e. {@link __ConfigurationProperty} instances.<br>
 *
 */
abstract class __ConfigurationComponent {
    
    /**
     * The name of current configuration component
     * @var  string
     */
    protected $_name = '';

    /**
     * If we assign to each node an attribute as an identifier, _id will contain the value of this identifier.
     * Merge operations will take it into account.
     *
     * @var string
     */
    protected $_id   = '';
    
    /**
     * The content of current configuration component
     * @var  string
     */
    protected $_content = '';

    /**
     * Reference to current configuration component's parent
     * @var __ComplexConfigurationComponent
     */
    protected $_parent = null;
    
    /**
     * Array of attributes for current configuration component
     * @var  array
     */
    protected $_attributes = array();
    
    /**
     * This is the name of the attribute that will act as identifier for current node
     *
     * @var string
     */
    protected $_attribute_id = null;

    /**
     * Constructor method
     *
     * @param  string  $name       Name of current configuration component
     * @param  string  $content    Content for current configuration component
     * @param  array   $attributes Array of attributes for current configuration component
     * 
     */
    public function __construct($name = '', $content = '', array $attributes = array())
    {
        $this->_name       = $name;
        $this->_content    = $content;
        $this->_attributes = $attributes;
    }
    
    /**
     * Set the current configuration component's parent
     *
     * @param __ComplexConfigurationComponent $parent The current configuration component's parent
     */
    public function setParent(__ComplexConfigurationComponent &$parent) {
        $this->_parent =& $parent;
    }

    /**
     * Returns the current configuration component's parent
     * 
     * @return __ComplexConfigurationComponent A reference to parent instance or null if the current configuration component is root
     */
    public function &getParent()
    {
        return $this->_parent;
    } 

    /**
     * Set this current configuration component's name.
     * 
     * @param string $name The name for current configuration component
     */
    public function setName($name)
    {
        $this->_name = $name;
    } 

    /**
     * Get this current configuration component's name.
     * 
     * @return string  The current configuration component's name
     */
    public function getName()
    {
        return $this->_name;
    } 
    
    public function setAttributeId($attribute_name) {
    	$this->_attribute_id = $attribute_name;
    	if(key_exists($attribute_name, $this->_attributes)) {
    		$this->_id = $this->_attributes[$attribute_name];
    	}
    }

    
    public function setId($id_value) {
    	$this->_id = $id_value;
    }
    
    public function getId() {
    	return $this->_id;
    }
    
    /**
     * This method returns true if the parameter passed has the same type
     * name and attributes that the current component 
     *
     */
    public function isEqual($configuration_component) {
        if(get_class($this) == get_class($configuration_component) &&
           $this->getName() == $configuration_component->getName() &&
           $this->getId()   == $configuration_component->getId()) {
            $own_attributes = $this->getAttributes();
            $configuration_component_attributes = $configuration_component->getAttributes();
            if(count($this->getAttributes()) == count($configuration_component->getAttributes())) {
                foreach($own_attributes as $own_attribute_name => $own_attribute_value) {
                    if(!key_exists($own_attribute_name, $configuration_component_attributes) ||
                       $configuration_component_attributes[$own_attribute_name] != $own_attribute_value) {
                          return false;
                    }
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
        return true;
    }    
    
    /**
     * Set the current configuration component's content.
     * 
     * @param string $content The content for current configuration component
     */
    public function setContent($content)
    {
        $this->_content = $content;
    } 
    
    /**
     * Get the current configuration component's content.
     * 
     * @return string The current configuration component's content
     */
    public function getContent()
    {
        return $this->_parseValue($this->_content);
    }
    
    protected function _parseValue($value) {
        return __ConfigurationValueResolver::resolveValue($value);
    }    

    /**
     * Set the current configuration component's attributes.
     * 
     * @param  array $attributes An array of attributes to set
     */
    public function setAttributes(array $attributes)
    {
        $this->_attributes = $attributes;
    	if($this->_attribute_id != null && 
    	   key_exists($this->_attribute_id, $this->_attributes)) {
        	$this->setId($this->_attributes[$this->_attribute_id]);
    	}        
    }
    
    public function addAttribute($attribute_name, $attribute_value) {
        $this->_attributes[$attribute_name] = $attribute_value;
        if($this->_attribute_id != null &&
           $attribute_name == $this->_attribute_id) {
        	$this->setId($attribute_value);
        }
    }

    public function updateAttribute($attribute_name, $attribute_value) {
        if(key_exists($attribute_name, $this->_attributes)) {
            $this->_attributes[$attribute_name] = $attribute_value;
	        if($this->_attribute_id != null &&
	           $attribute_name == $this->_attribute_id) {
	        	$this->setId($attribute_value);
	        }
        }
    }
    
    /**
     * Update the current configuration component's attributes.
     * 
     * @param array $attributes An array of attributes to update
     */
    public function updateAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->_attributes[$key] = $value;
	        if($this->_attribute_id != null && 
	           $key == $this->_attribute_id) {
	        	$this->setId($value);
	        }
        }
    } 

    /**
     * Get the current configuration component's attributes.
     * 
     * @return array An array of attributes
     */
    public function getAttributes()
    {
        return $this->_attributes;
    } 
    
    public function hasAttribute($attribute_name) {
        return key_exists($attribute_name, $this->_attributes);
    }
    
    /**
     * Get one attribute value of this component
     * 
     * @param  string $attribute Attribute key
     * @return mixed component's attribute value
     */
    public function getAttribute($attribute_name)
    {
        $return_value = null;
        if (key_exists($attribute_name, $this->_attributes)) {
            $return_value = $this->_parseValue($this->_attributes[$attribute_name]);
        }
        return $return_value;
    }
    
    /**
     * Returns if this component is a root component or not
     * 
     * @return bool true if component is root
     */
    public function isRoot()
    {
        $return_value = false;
        if ($this->_parent == null) {
            $return_value = true;
        }
        return $return_value;
    } 
    
    /**
     * Return an string in the specified format for current configuration component.
     * 
     * @param string $configuration_type Type of configuration format to generate the string
     * @param array $options Specify special options for output
     * 
     * @return string The requested string
     */
    public function toString($configuration_type, array $options = array())
    {
        $return_value = null;
        $configuration_storage = __ConfigurationStorageFactory::getInstance()->createConfigurationStorage($configuration_type);
        if($configuration_storage instanceof __ConfigurationStorage) {
            $return_value = $configuration_storage->toString($this, $options);
        }
        return $return_value;
    }

    /**
     * Returns a key/value pair array of the container and its children.
     *
     * Format : section[property][index] = value
     * If the container has attributes, it will use '@' and '#'
     * index is here because multiple propertys can have the same name.
     *
     * @param    bool    $useAttr        Whether to return the attributes too
     * @return array
     */    
    public function toArray($use_attributes = true) {
        return null;      
    }

    /**
     * Returns the root configuration component in the configuration hierarchy that current component is from.
     *
     * @return __ConfigurationComponent
     */
    public function &getRootConfigurationComponent() {
        if($this->isRoot() == true) {
            $return_value =& $this;
        }
        else {
            $return_value =& $this->_parent->getRootConfigurationComponent();
        }
        return $return_value;
    }
    
    /**
     * Returns the __Context instance associated to the current configuration component.
     * If __Context doesn't exists for current configuration component, this method returns null.
     * 
     * @return __Context The requested __Context if exists, else null.
     */
    public function &getContext() {
        $return_value = null;
        $root_configuration_component =& $this->getRootConfigurationComponent();
        if($root_configuration_component instanceof __Configuration) {
            $return_value =& $root_configuration_component->getContext();
        }
        return $return_value;
    }          
    
}