<?php
/**
 * This class is based on PEAR::Config by Bertrand Mansion under the PHP License, version 2.02 (http://www.php.net/license/2_02.txt)
 * A copy of the license terms can be found in /thrdparty-licenses/CONFIG
 * 
 * This class was created on 11/28/2006
 *
 */

abstract class __ComplexConfigurationComponent extends __ConfigurationComponent {
    
    /**
     * Collection of children configuration components
     * 
     * @var array
     */
    protected $_children = array();
    
    protected $_section_handler = null;
    
    /**
     * Create a child configuration component.
     * 
     * @param  integer $type Type of configuration component to be created
     * @param  string $name Component name to be created
     * @param  string $content Component content
     * @param  array $attributes Component attributes
     * @param  string $where Choose a position 'bottom', 'top', 'after', 'before' where the new component will be added
     * @param  __ConfigurationComponent  $target needed if you choose 'before' or 'after' for where
     * 
     * @return __ConfigurationComponent A reference to the new component
     */
    protected function &createComponent($type, $name, $content, array $attributes = array(), $where = 'bottom', __ConfigurationComponent $target = null)
    {
        switch($type) {
            case __ConfigurationComponentType::SECTION :
                $component       = new __ConfigurationSection($name, $content, $attributes);
                $section_handler = __SectionHandlerFactory::getInstance()->createSectionHandler($name);
                if($section_handler != null) {
                    $component->registerSectionHandler($section_handler);
                }
                break;
            case __ConfigurationComponentType::PROPERTY :
                $component = new __ConfigurationProperty($name, $content, $attributes);
                break;
            case __ConfigurationComponentType::COMMENT :
                $component = new __ConfigurationComment($name, $content, $attributes);
                break;
            case __ConfigurationComponentType::BLANK  :
                $component = new __ConfigurationBlank($name, $content, $attributes);
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_CONFIGURATION_COMPONENT_TYPE', array($type));
                break;
        }
        $return_value =& $this->addComponent($component, $where, $target);
        return $return_value;
    }
    
    /**
     * Register a {@link __ISectionHandler} instance associated to the current section
     *
     * @param __ISectionHandler $section_handler The section handler for current section
     */
    public function registerSectionHandler(__ISectionHandler &$section_handler) {
        $this->_section_handler =& $section_handler;
    }
    
    /**
     * Return true if the current section has a {@link __ISectionHandler} instance associated to, otherwise false.
     *
     * @return bool true if the current seciton has a {@link __ISectionHandler} instance associated to, otherwise false
     */
    public function hasSectionHandler() {
        $return_value = false;
        if($this->_section_handler != null) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Return the {@link __ISectionHandler} instance associated to the current section. If it does not exist, returns null
     *
     * @return __ISectionHandler The section handler associated to the current section. If it does not exist, returns null
     */
    public function &getSectionHandler() {
        return $this->_section_handler;
    }
        
    /**
     * Adds a comment to this component.
     * This is a helper method that calls createComponent
     *
     * @param  string    $content        Object content
     * @param  string    $where          Position : 'top', 'bottom', 'before', 'after'
     * @param  object    $target         Needed when $where is 'before' or 'after'
     * @return object  reference to new component
    */
    public function &createComment($content = '', $where = 'bottom', $target = null)
    {
        return $this->createComponent(__ConfigurationComponentType::COMMENT , null, $content, array(), $where, $target);
    } 

    /**
     * Adds a blank line to this component.
     * This is a helper method that calls createComponent
     *
     * @return object  reference to new component
    */
    public function &createBlank($where = 'bottom', $target = null)
    {
        return $this->createComponent(__ConfigurationComponentType::BLANK , null, null, array(), $where, $target);
    } 

    /**
    * Adds a property to this component.
    * This is a helper method that calls createComponent
    *
    * @param  string    $name           Name of new property
    * @param  string    $content        Content of new property
    * @param  mixed     $attributes     property attributes
    * @param  string    $where          Position : 'top', 'bottom', 'before', 'after'
    * @param  object    $target         Needed when $where is 'before' or 'after'
    * @return object  reference to new component
    */
    public function &createProperty($name, $content, array $attributes = array(), $where = 'bottom', $target = null)
    {
        $return_value =& $this->createComponent(__ConfigurationComponentType::PROPERTY , $name, $content, $attributes, $where, $target);
        $root_configuration_component =& $this->getRootConfigurationComponent();
        if($root_configuration_component instanceof __Configuration) {
            $root_configuration_component->registerProperty($return_value);
        }
        return $return_value;
    }
    
    /**
     * Adds a section to this component.
     *
     * This is a helper method that calls createComponent
     * If the section already exists, it won't create a new one. 
     * It will return reference to existing component.
     * 
     * @param  string    $name           Name of new section
     * @param  array     $attributes     Section attributes
     * @param  string    $where          Position : 'top', 'bottom', 'before', 'after'
     * @param  object    $target         Needed when $where is 'before' or 'after'
     * @return object  reference to new component
     */
    public function &createSection($name, $attributes = array(), $where = 'bottom', $target = null)
    {
        return $this->createComponent(__ConfigurationComponentType::SECTION , $name, null, $attributes, $where, $target);
    }
    
    
    /**
     * Set a children property content.
     * 
     * This is an helper method calling getComponent and addComponent or setContent for you.
     * If the property does not exist, it will be created at the bottom.
     *
     * @param  string    $name        Name of the property to look for
     * @param  mixed     $content     New content
     * @param  int       $index       Index of the property to set,
     *                                in case there are more than one property
     *                                with the same name
     * @return object    newly set property
     */
    function &setPropertyContent($name, $content, $index = -1)
    {
        $component =& $this->getProperty($name, null, null, $index);
        if ($component == null) {
            return $this->createProperty($name, $content, null);
        } else {
            // Change existing property value
            $component->setContent($content);
            return $component;
        }
    }
    
    /**
     * Return a property's content.
     * 
     * This method can use two different search approach, depending on
     * the parameter it is given. If the parameter is an array, it will use
     * the {@link __ComplexConfigurationComponent::searchPath()} method. If it is a string, 
     * it will use the {@link __ComplexConfigurationComponent::getComponent()} method.
     *
     * Example:
     * <code>
     * 
     * // Will return the value found at :
     * // [Database]
     * // host=localhost
     * echo $configuration_component->getPropertyContent(array('Database', 'host')));
     *
     * // Will return the value found at :
     * // date="dec-2004"
     * echo $configuration_component->getPropertyContent('date');
     *
     * </code>
     *
     * @param    mixed   Search path and attributes or a property name
     * @param    int     Index of the component in the returned property list.
     *                   Eventually used if args is a string.
     * 
     * @return   mixed   Content of property or false if not found.
     * @access   public
     */
    function getPropertyContent($args, $index = -1)
    {
        $return_value = null;
        if (is_array($args)) {
            $component =& $this->searchPath($args);
        } else {
            $component =& $this->getProperty($args, null, null, $index);
        }
        if ($component) {
            $return_value = $component->getContent();
        }
        return $return_value;
    }    
    
    /**
     * Returns how many _children this container has
     *
     * @param class $type The class type of _children counted
     * @param  string    $name    name of _children counted
     * @return int  number of _children found
     */
    function countChildren($type = null, $name = null)
    {
        $return_value = 0;
        if ($name != null && $type != null) {
            for ($i = 0, $children = count($this->_children); $i < $children; $i++) {
                if ($this->_children[$i]->getName() === $name && 
                    $this->_children[$i] instanceof $type) {
                    $return_value++;
                }
            }
        }
        else if ($type != null) {
            for ($i = 0, $children = count($this->_children); $i < $children; $i++) {
                if ($this->_children[$i] instanceof $type) {
                    $return_value++;
                }
            }
        }
        else if ($name != null) {
            // Some propertys can have the same name
            for ($i = 0, $children = count($this->_children); $i < $children; $i++) {
                if ($this->_children[$i]->getName() === $name) {
                    $return_value++;
                }
            }
        }
        return $return_value;
    } 

    /**
     * Deletes a component (section, property, comment...) from the current object
     * TODO: recursive remove in sub-sections
     * @return mixed  true if object was removed, false if not
     * @throws __ConfigurationException if current configuration component is root 
     */
    function removeComponent()
    {
        if ($this->isRoot()) {
            throw new __ConfigurationException('Cannot remove root component');
        }
        $index = $this->getComponentIndex();
        if (!is_null($index)) {
            array_splice($this->_parent->_children, $index, 1);
            return true;
        }
        return false;
    } 

    /**
    * Returns the component index in its parent children array.
    * @return int  returns int or null if root object
    */
    function getComponentIndex()
    {
        if ($this->_parent != null) {
            // This will be optimized with Zend Engine 2
            $pchildren =& $this->_parent->_children;
            for ($i = 0, $count = count($pchildren); $i < $count; $i++) {
                if ($pchildren[$i] != $this) {
                    return $i;
                }
            }
        }
        return;
    } 

    /**
    * Returns the component rank in its parent children array
    * according to other components with same type and name.
    * @param bool  count components differently by type
    * @return int  returns int or null if root object
    */
    function getComponentPosition($by_type = true)
    {
        if ($this->_parent != null) {
            $pchildren =& $this->_parent->_children;
            for ($i = 0, $count = count($pchildren); $i < $count; $i++) {
                if ($pchildren[$i]->getName() == $this->getName()) {
                    if ($by_type == true) {
                        if (get_class($pchildren[$i]) == get_class($this->type)) {
                            $obj[] =& $pchildren[$i];
                        }
                    } else {
                        $obj[] =& $pchildren[$i];
                    }
                }
            }
            for ($i = 0, $count = count($obj); $i < $count; $i++) {
                if ($obj[$i] != $this) {
                    return $i;
                }
            }
        }
        return;
    } 

    /**
    * Returns the component parent object.
    * @return mixed  returns reference to child object or false if child does not exist
    */
    function &getChild($index = 0)
    {
        $return_value = null;
        if (!empty($this->_children[$index])) {
            $return_value = $this->_children[$index];
        }
        return $return_value;
    } 
    
    public function &getChildrens() {
        return $this->_children;
    }

    protected function _mergeComponent(__ConfigurationComponent &$component) {
        if(get_class($this) == get_class($component)) {
            foreach($component->_children as &$children) {
                $same_children = $this->getComponentEqualsTo($children);
                if($same_children === false) {
                    $this->addComponent($children);
                }
                else {
                    if( $same_children instanceof __ComplexConfigurationComponent ) {
                        $same_children->_mergeComponent($children);
                    }
                    unset($same_children);
                }
                unset($children);
            }
        }
    }
    
    protected function &getComponentEqualsTo(__ConfigurationComponent &$component) {
        $return_value = false;
        foreach($this->_children as &$children) {
            if($children->isEqual($component)) {
                return $children;
            }
        }
        return $return_value;
    }
    
    protected function hasComponent(__ConfigurationComponent &$component) {
        foreach($this->_children as &$children) {
            if($children->isEqual($component)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Adds an component to this component.
     * @param  object   $component      a container object
     * @param  string   $where     choose a position 'bottom', 'top', 'after', 'before'
     * @param  object   $target    needed if you choose 'before' or 'after' in $where
     * @return mixed    reference to added container on success
     * @throws __ConfigurationException if an unknow location is specified on $where parameter
     */
    protected function &addComponent(__ConfigurationComponent $component, $where = 'bottom', __ConfigurationComponent $target = null)
    {
        if (is_null($target)) {
            $target =& $this;
        }
        switch ($where) {
            case 'before':
                $index = $target->getComponentIndex();
                break;
            case 'after':
                $index = $target->getComponentIndex()+1;
                break;
            case 'top':
                $index = 0;
                break;
            case 'bottom':
                $index = -1;
                break;
            default:
                throw new __ConfigurationException('Use only top, bottom, before or after in __ConfigurationComponent::addComponent.');
        }
        if (isset($index) && $index >= 0) {
            array_splice($this->_children, $index, 0, 'tmp');
        } else {
            $index = count($this->_children);
        }
        $this->_children[$index] =& $component;
        $this->_children[$index]->setParent($this);

        return $component;
    }
    
    public function &getProperties() {
        return $this->getComponentsOfType('__ConfigurationProperty');
    }

    public function &getSections() {
        return $this->getComponentsOfType('__ConfigurationSection');
    }
    
    public function &getComponentsOfType($type) {
        $return_value = array();
        foreach($this->_children as &$children) {
            if($children instanceof $type) {
                $return_value[] =& $children;
            }
        }
        return $return_value;
    }


    public function &getProperty($name = null, $content = null, $attributes = null, $index = -1) {
        return $this->_filterComponent($this->_children, '__ConfigurationProperty', $name, $content, $attributes, $index);
    }

    /**
     * Returns the requested section. If the section has associated a {@link __ISectionHandler} instance, them
     * returns the result of calling to the processSection method.
     *
     * @param The requested section name $name
     * @param unknown_type $content
     * @param unknown_type $attributes
     * @param unknown_type $index
     * @return unknown
     */
    public function &getSection($name = null, $content = null, $attributes = null, $index = -1) {
        $return_value = $this->_filterComponent($this->_children, '__ConfigurationSection', $name, $content, $attributes, $index);
        if($return_value != null && $return_value->hasSectionHandler()) {
            $return_value = $return_value->getSectionHandler()->process($return_value);
        }
        return $return_value;
    }        
    
    /**
     * Tries to find the specified component(s) and returns the objects.
     *
     * Examples:
     * $propertys =& $obj->getComponent('property');
     * $property_bar_4 =& $obj->getComponent('property', 'bar', null, 4);
     * $section_foo =& $obj->getComponent('section', 'foo');
     *
     * This method can only be called on __ComplexConfigurationComponent class.
     * Note that root is a section.
     * This method is not recursive and tries to keep the current structure.
     * For a deeper search, use searchPath()
     *
     * @param  string    $type        Type of component: property, section, comment, blank...
     * @param  mixed     $name        Component name
     * @param  mixed     $content     Find component with this content
     * @param  array     $attributes  Find component with attribute set to the given value
     * @param  int       $index       Index of the component in the returned object list. If it is not set, will try to return the last component with this name.
     * @return mixed  reference to component found or false when not found
     * @see &searchPath()
     */
    function &getComponent($type = null, $name = null, $content = null, array $attributes = null, $index = -1)
    {
        return $this->_filterComponent($this->_children, $type, $name, $content, $attributes, $index);
    } 

    protected function &_filterComponent(array &$components, $type = null, $name = null, $content = null, array $attributes = null, $index = -1) 
    {
        $return_value = null; //while the component is not matched:
        $getters     = array();
        $test_fields = array();
        if($type == null) {
            $type = __ConfigurationComponent;
        }
        if ($name != null) {
            $test_fields[] = 'name';
            $getters['name'] = 'getName';
        }
        if ($content != null) {
            $test_fields[] = 'content';
            $getters['content'] = 'getContent';
        }
        if ($attributes != null && is_array($attributes)) {
            $test_fields[] = 'attributes';
            $getters['attributes'] = 'getAttributes';
        }
        $components_matched = array();
        $fields_to_match = count($test_fields);
        for ($i = 0, $count = count($components); $i < $count; $i++) {
            $match = 0;
            if($type == null || $components[$i] instanceof $type) {
                reset($test_fields);
                foreach ($test_fields as $field) {
                    if ($field != 'attributes') {
                        $getter_method = $getters[$field];
                        if ($components[$i]->$getter_method() == ${$field}) {
                            $match++;
                        }
                    } else {
                        // Look for attributes in array
                        $attributes_to_match = count($attributes);
                        $attributes_matched  = 0;
                        foreach ($attributes as $key => $value) {
                            if (isset($components[$i]->_attributes[$key]) &&
                            $components[$i]->_attributes[$key] == $value) {
                                $attributes_matched++;
                            }
                        }
                        if ($attributes_matched == $attributes_to_match) {
                            $match++;
                        }
                    }
                }
            }
            if ($match == $fields_to_match) {
                $components_matched[] =& $components[$i];
            }
        }
        if ($index >= 0) {
            if (isset($components_matched[$index])) {
                $return_value =& $components_matched[$index];
            }
        } else if ($count = count($components_matched)) {
            $return_value =& $components_matched[$count-1];
        }
        return $return_value;
    }
    
    /**
     * Finds a node using XPATH like format.
     * 
     * The search format is an array:
     * array(component1, component2, component3, ...)
     *
     * Each component can be defined as the following:
     * component = 'string' : will match the container named 'string'
     * component = array('string', array('name' => 'xyz'))
     * will match the container name 'string' whose attribute name is equal to "xyz"
     * For example : <string name="xyz">
     * 
     * @param    mixed   Search path and attributes
     * 
     * @return   mixed   Config_Container object, array of Config_Container objects or false on failure.
     * @access   public
     */
    function &searchPath($args)
    {
        $arg = array_shift($args);

        if (is_array($arg)) {
            $name = $arg[0];
            $attributes = $arg[1];
        } else {
            $name = $arg;
            $attributes = null;
        }
        // find all the matches for first..
        $match =& $this->getComponent(null, $name, null, $attributes);

        if (!$match) {
            $return = false;
            return $return;
        }
        if (!empty($args)) {
            return $match->searchPath($args);
        }
        return $match;
    } 

    /**
     * Returns a key/value pair array of the container and its children.
     *
     * Format : section[property][index] = value
     * If the container has attributes, it will use '@' and '#'
     * index is here because multiple propertys can have the same name.
     *
     * @param    bool    $use_attributes        Whether to return the attributes too
     * @return array
     */
    function toArray($use_attributes = true)
    {
        $section_name = $this->getName();
        $id = $section_name;
        $return_value[$id] = array();
        if ($use_attributes && count($this->_attributes) > 0) {
            $return_value[$id]['@'] = $this->_attributes;
        }
        if ($count = count($this->_children)) {
            for ($i = 0; $i < $count; $i++) {
                if($this->_children) {
                    $new_array = $this->_children[$i]->toArray( $use_attributes );
                }
                if (!is_null($new_array)) {
                    foreach ($new_array as $key => $value) {
                        if (key_exists($key, $return_value[$id])) {
                            if(!key_exists($key, $return_value[$id])) {
                                $return_value[$id][$key] = array();
                            }
                            else if(!is_array($return_value[$id][$key]) || !isset($return_value[$id][$key][0])) {
                                $previous_content           = $return_value[$id][$key];
                                $return_value[$id][$key]    = array();
                                $return_value[$id][$key][0] = $previous_content;
                            }
                            $return_value[$id][$key][] = $value;
                        } else {
                            $return_value[$id][$key] = $value;
                        }
                    }
                }                
            }
        }
        return $return_value;
    } 
    
    /**
     * Returns the current section and descendants as SimpleXMLElement
     *
     * @param bool $use_attributes Whether to return the attributes too
     * @return SimpleXMLElement The current section as a SimpleXMLElement
     */
    function &toSimpleXMLElement($use_attributes = true, SimpleXMLElement &$parent_xml_element = null)
    {
        if($parent_xml_element === null) {
            $return_value = new SimpleXMLElement('<root/>'); //Use a dummy XML content '<root/>' to avoid errors on SimpleXMLElement constructor
        }
        else {
            $return_value =& $parent_xml_element;
        }
        if ($use_attributes) {
            foreach($this->_attributes as $attribute_name => $attribute_value) {
                $return_value->addAttribute($attribute_name, $attribute_value);
            }
        }
        foreach($this->_children as $children) {
            if( $children instanceof __ComplexConfigurationComponent ) {
                $child_xml_element =& $return_value->addChild($children->getName());
                $child_xml_element = $children->toSimpleXmlElement($use_attributes, $child_xml_element);
            }
        }
        return $return_value;
    }
    
    
}