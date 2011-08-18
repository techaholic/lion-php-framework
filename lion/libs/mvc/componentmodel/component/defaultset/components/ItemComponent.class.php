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
 * Represents an item within a list of items.
 * 
 * Item tag is <b>item</b>
 * 
 * i.e.
 * <code>
 * 
 *   <comp:item text="More than 100 employees" value="5" selected="true"/>
 * 
 * </code>
 * 
 * An item has 3 properties:<br>
 *  - <b>text</b>, the text representing the item, which will be the text shown within a list based component.<br>
 *  - <b>value</b>, the value represented by the item.<br>
 *  - <b>selected</b>, if the current item is selected within a list based component<br>
 * <br>
 * <br>
 * An item component can be contained just in list based components.<br>
 * At the same time, a list based component can only contain items, even if the list component inherit from a {@link __IContainer} class.<br>
 * <br>
 * 
 * @see __ItemListComponent, __ComboBoxComponent
 *
 */
class __ItemComponent extends __UIComponent {

	protected $_text = null;
	protected $_value = null;
	protected $_selected = false;
    
	public function __construct($value = null, $text = null) {
	    $this->setValue($value);
	    $this->setText($text);
	}
	
	/**
	 * Overwriten from __UIComponent in order to ensure that the parent component is a {@link __ItemListComponent} instance
	 *
	 * @param __IContainer $container The container component
	 */
    public function setContainer(__IContainer &$container) {
        if($container instanceof __ItemListComponent) {
            parent::setContainer($container);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong container component for an __ItemComponent. An __ItemListComponent was expected.');
        }
    }	
	
    /**
     * Set the text property
     *
     * @param string $text
	 * @return __ItemComponent a reference to the current instance
     */
	public function &setText($text) {
		$this->_text = $text;
		return $this;
	}
	
	/**
	 * Get the text property
	 *
	 * @return string
	 */
	public function getText() {
		return $this->_text;
	}
	
	/**
	 * Set the value property
	 *
	 * @param mixed $value
	 * @return __ItemComponent a reference to the current instance
	 */
	public function &setValue($value) {
	    $this->_value = $value;
	    return $this;
	}
	
	/**
	 * Gets the value property
	 *
	 * @return mixed
	 */
	public function getValue() {
	    return $this->_value;
	}

	/**
	 * Set a value that indicates whether the current item is selected
	 *
	 * @param bool $selected
	 * @return __ItemComponent a reference to the current instance
	 */
	public function &setSelected($selected) {
        $selected = $this->_toBool($selected);
	    if($this->_selected !== $selected) {
    	    $this->_selected = $selected;
    	    if($selected) {
        	    //notify to the container:
        	    $container = $this->getContainer();
        	    if($container != null) {
        	        $container->setSelectedItem($this);
        	    }
    	    }
	    }
	    return $this;
	}
	
	/**
	 * Gets a value that indicates whether the current item is selected
	 *
	 * @return bool
	 */
	public function getSelected() {
	    return $this->_selected;
	}
	
	/**
	 * Alias of {@link __ItemComponent::getSelected()} method
	 *
	 * @return bool
	 */
	public function isSelected() {
	    return $this->getSelected();
	}
	
	/**
	 * Gets a string representation of current instance
	 *
	 * @return unknown
	 */
    public function __toString() {
        return $this->getText() . " => " . $this->_component->getValue();
    }
	
}
