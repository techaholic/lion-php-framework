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
 * Base class for list based components, as the combobox, the listbox, ...
 * 
 * list based components are item containers basically. They contains a collection of items.<br>
 * As {@link __IValueHolder} component, the value of a list corresponds to the value of all the selected items.<br>
 * <br>
 * A list based component doesn't have any tag by default, but the subclasses.<br>
 * <br>
 * A list based component has 2 selection modes:<br>
 *  - single (__ItemListComponent::SELECTION_MODE_SINGLE), which allows the selection of one item at the same time.<br>
 *  - multiple (__ItemListComponent::SELECTION_MODE_MULTIPLE), which allows the selection of more than one item at the same time.<br>
 * <br>
 * A list based component is able to sort his items by setting the <b>sorted</b> property to true. In that sense, items will be sorted by the text property.<br>
 * <br>
 * <br>
 * @see __ComboBoxComponent, __ListBoxComponent, __ItemComponent
 * 
 *
 */
class __ItemListComponent extends __InputComponent {

    const SELECTION_MODE_SINGLE   = 'single';
    const SELECTION_MODE_MULTIPLE = 'multiple';    
    
    protected $_items   = array();
    protected $_sorted  = false;
    protected $_to_sort = false;
    protected $_selection_mode = __ItemListComponent::SELECTION_MODE_SINGLE;

    /**
     * Removes all the items from current list
     *
     */
    public function clear() {
        foreach($this->_items as &$item) {
            $this->removeComponent($item->getId());
        }
        $this->_items   = array();
        $this->_to_sort = false;
    }
    
    /**
     * Add a child component to current one. 
     * 
     * If a {@link __ItemComponent} instance is received, it will also be added to the item list
     *
     * @param __IComponent &$component The component to add to
     * @return __ItemListComponent A reference to the current component
     */
    public function addComponent(__IComponent &$component) {
        if( $component instanceof __ItemComponent && !key_exists($component->getId(), $this->_components)) {
            if( count($this->_items) == 0 && $this->_selection_mode == self::SELECTION_MODE_SINGLE ) {
               $component->setSelected(true);
            }
            $this->_items[$component->getId()] =& $component;
            $this->_to_sort = true;
            if($component->isSelected()) {
                $this->setSelectedItem($component);
            }
            return parent::addComponent($component);
        }
    }
    
    /**
     * Adds an {@link __ItemComponent} instance to the current list.
     * This method calls internally to the {@link __ItemListComponent::addComponent()} method
     *
     * @param __ItemComponent &$item The item to add to
     */
    public function addItem(__ItemComponent &$item) {
        $this->addComponent($item);
    }
    
    /**
     * Gets an item by his index within a list
     *
     * @param integer $index The item index
     * @return __ItemComponent
     */
    public function getItem($index) {
        $this->_sortItems();
        $return_value = null;
        if(is_numeric($index) && $index >=0) {
            if($index >= count($this->_items)) {
                $index = count($this->_items) - 1;
            }
            $current_index = 0;
            foreach($this->_items as &$item) {
                if($current_index == $index) {
                    return $item;
                }
                $current_index ++;
            }
        }
        return $return_value;
    }
        
    /**
     * Sets the value of the first selected item
     *
     * @param mixed $value The value to set to
     */
    public function setValue($value) {
        $selected_item = $this->getSelectedItem();
        if($selected_item != null) {
            $selected_item->setValue($value);
        }
        $this->_to_sort = true;
    }

    /**
     * Overwrite the __InputComponent reset method to avoid
     * change the value to the selected item (which is the
     * current behavior of setValue method)  
     *
     */
    public function reset() {
        $this->setSelectedIndex(0);
        $this->resetValidation();
    }    
    
    /**
     * Gets the value of the first selected item
     *
     * @return mixed value
     */
    public function getValue() {
        $return_value  = null;
        $selected_item = $this->getSelectedItem();
        if($selected_item != null) {
            $return_value = $selected_item->getValue();
        }
        return $return_value;
    }
    
    /**
     * Sets the first selected item within the current list.
     *
     * @param __ItemComponent &$item The item to select to
     */
    public function setSelectedItem(__ItemComponent &$item) {
        foreach($this->_items as &$existent_item) {
            if($existent_item->getId() == $item->getId()) {
                if(!$existent_item->isSelected()) {
                    $existent_item->setSelected(true);
                }
            }
            else if( $existent_item->isSelected() && $this->_selection_mode == self::SELECTION_MODE_SINGLE ) {
                $existent_item->setSelected(false);
            }
        }
    }

    /**
     * Gets the first selected item within the current list.
     * This method returns null if no selected items are found
     *
     * @return __ItemComponent The first selected item
     */
    public function &getSelectedItem() {
        $return_value = null;
        $this->_sortItems();
        foreach($this->_items as &$item) {
            if($item->isSelected()) {
                return $item;
            }
        }
        return $return_value;
    }
    
    /**
     * Sets the index for the first selected item
     *
     * @param numeric $selected_index
     */
    public function setSelectedIndex($selected_index) {
        $this->_sortItems();
        $index = 0;
        foreach($this->_items as &$item) {
            if($index == $selected_index) {
                $item->setSelected(true);
            }
            else if($this->_selection_mode == self::SELECTION_MODE_SINGLE) {
                $item->setSelected(false);
            }
            $index++;
        }
    }
    
    /**
     * Gets the index of the first selected item
     *
     * @return integer
     */
    public function getSelectedIndex() {
        $this->_sortItems();
        $selected_index = 0;
        foreach($this->_items as $item) {
            if($item->isSelected()) {
                return $selected_index;
            }
            $selected_index++;
        }
        return 0;
    }

    /**
     * Sets indexes for selected items
     *
     * @param array $selected_indexes
     */
    public function setSelectedIndexes(array $selected_indexes) {
        $this->_sortItems();
        $index = 0;
        foreach($this->_items as &$item) {
            if(in_array($index, $selected_indexes)) {
                $item->setSelected(true);
            }
            else {
                $item->setSelected(false);
            }
            $index++;
        }
    }
    
    /**
     * Gets the indexes of all selected items
     *
     * @return array
     */
    public function getSelectedIndexes() {
        $this->_sortItems();
        $return_value = array();
        $selected_index = 0;
        foreach($this->_items as &$item) {
            if($item->isSelected()) {
                $return_value[] = $selected_index;
            }
            $selected_index++;
        }
        return $return_value;
    }

    /**
     * Gets all selected items
     *
     * @return array
     */
    public function &getSelectedItems() {
        $this->_sortItems();
        $return_value = array();
        foreach($this->_items as &$item) {
            if($item->isSelected()) {
                $return_value[] =& $item;
                unset($item);
            }
        }
        return $return_value;
    }
      
    /**
     * Get an array of pair key,values representing items contained within the current list component
     *
     * @return array
     */
    public function getItemValues() {
        $return_value = array();
        $items = $this->getItems();
        foreach($items as &$item) {
            $return_value[] = array($item->getText(), $item->getValue());
        }
        return $return_value;
    }
    
    /**
     * Set an array of pair key,values representing items to be contained within the current list component
     *
     * @param array $item_values
     */
    public function setItemValues(array $item_values) {
        if(count($item_values) == count($this->_items)) {
            $index = 0;
            foreach($item_values as $item_value) {
                $item = $this->getItem($index);
                $item->setText($item_value[0]);
                $item->setValue($item_value[1]);
                $index++;
            }
        }
    }    
      
    /**
     * Sets the items for current component.
     * 
     * Note that this method will clear any existing item within the current instance
     *
     * @param array &$items
     */
    public function setItems(array &$items) {
        $this->clear();
        //validate:
        foreach($items as &$item) {
            if(! $item instanceof __ItemComponent ) {
                throw __ExceptionFactory::getInstance()->createException('Error trying to add a ' . get_class($item) . ' to a __ItemList. An __ItemComponent was expected');
            }
            else {
                $this->addItem($item);
            }
            unset($item);
        }
    }
    
    /**
     * Gets the items of current component
     *
     * @return array
     */
    public function &getItems() {
        $this->_sortItems();
        return $this->_items;
    }

    protected function _sortItems() {
        if($this->_sorted && $this->_to_sort) {
            uasort($this->_items, array($this, "cmpItems"));
            $this->_to_sort = false;
        }
    }
    
    /**
     * Compares two items and return a numeric value representing the comparison result
     *
     * @param __ItemComponent $item1
     * @param __ItemComponent $item2
     * @return numeric 0: equals, -1: item1&lt;item2, 1: item1&gt;item2
     */
    public function cmpItems(__ItemComponent $item1, __ItemComponent $item2) {
        $item1_value = $item1->getText();
        $item2_value = $item2->getText();
        if ($item1_value == $item2_value) {
            return 0;
        }
        return ($item1_value < $item2_value) ? -1 : 1;
    }        
    
    /**
     * Sets if the item list is sorted
     *
     * @param bool $sorted
     */
    public function setSorted($sorted) {
        $this->_sorted = $this->_toBool($sorted);
        if($this->_sorted) {
            $this->_sortItems();
        }
    }
    
    /**
     * Gets if the item list is sorted 
     *
     * @return bool
     */
    public function getSorted() {
        return $this->_sorted;
    }

    /**
     * Alias of setSorted
     *
     * @param bool $ordered
     */
    public function setOrdered($ordered) {
        $this->setSorted($ordered);
    }
    
    /**
     * Alias of getSorted
     *
     * @return bool
     */
    public function getOrdered() {
        return $this->getSorted();
    }
    
    /**
     * Set an string representing the selection mode, as one of the following:<br>
     *  - single (__ListBoxComponent::SELECTION_MODE_SINGLE)
     *  - multiple (__ListBoxComponent::SELECTION_MODE_MULTIPLE)
     *
     * @param string $selection_mode
     */
    public function setSelectionMode($selection_mode) {
        switch (strtoupper($selection_mode)) {
            case strtoupper(__ListBoxComponent::SELECTION_MODE_SINGLE):
                $this->_selection_mode = __ListBoxComponent::SELECTION_MODE_SINGLE;
                break;
            case strtoupper(__ListBoxComponent::SELECTION_MODE_MULTIPLE):
                $this->_selection_mode = __ListBoxComponent::SELECTION_MODE_MULTIPLE;
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Wrong selection mode for item list component: ' . $selection_mode);
        }
    }    
    
    /**
     * Get an string representing the selection mode set to the current list component
     *
     * @return string
     */
    public function getSelectionMode() {
        return $this->_selection_mode;
    }    

    /**
     * Gets the number of items contained within the current list component
     *
     * @return integer
     */
    public function getSize() {
        return count($this->_items);
    }
    
    
}