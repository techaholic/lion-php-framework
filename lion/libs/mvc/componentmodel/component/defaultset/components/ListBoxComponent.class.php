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
 * A listbox component represents a control to display a list of items, showing more than one item at the same time.
 * 
 * Listbox tag is <b>listbox</b>
 * 
 * i.e.
 * <code>
 *   
 *   <comp:listbox name="categories"/>
 * 
 * </code>
 * 
 * A listbox is a subclass of {@link __ItemListComponent}, which is compound by a list of {@link __ItemComponent} instances.<br>
 * A __ItemComponent is a component containing a pair text, value.<br>
 * <br>
 * We can setup the listbox items by ussing the "item" tag:<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:listbox name="categories">
 *     <comp:item value="1" text="Agriculture, forestry and fishing"/>
 *     <comp:item value="2" text="Arts, sports and recreation"/>
 *     <comp:item value="3" text="Catering and accommodation"/>
 *     <comp:item value="4" text="Construction"/>
 *     ...
 *   </comp:listbox>
 * 
 * </code>
 * 
 * A listbox shows more than one item at the same time. By default, it show 3 items, but we can customize it by setting the <b>rows</b> property.
 * 
 * i.e.
 * <code>
 * 
 *   <!-- showing 10 categories at the same time -->
 *   <comp:listbox name="categories" rows="10">
 *     <comp:item value="1" text="Agriculture, forestry and fishing"/>
 *     <comp:item value="2" text="Arts, sports and recreation"/>
 *     <comp:item value="3" text="Catering and accommodation"/>
 *     <comp:item value="4" text="Construction"/>
 *     ...
 *   </comp:listbox>
 * 
 * </code>
 * 
 * We can also setup a listbox within the eventhandler code (typically within the beforeRender (each time the view is rendered) or within the component create event (once the component is created).<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   public function beforeRender() {
 *       $listbox = $this->getComponent('categories');
 *       $listbox->addItem(new __Item(1, 'Agriculture, forestry and fishing'));
 *       $listbox->addItem(new __Item(2, 'Arts, sports and recreation'));
 *       $listbox->addItem(new __Item(3, 'Catering and accommodation'));
 *       $listbox->addItem(new __Item(4, 'Construction'));
 *       ...
 *   }
 * 
 * </code>
 * 
 * We can select more than one item within a listbox, which represents the value for that component. For that purpose, we can:<br>
 *  - Use the {@link __ItemListComponent::setSelectedIndex()} method or<br>
 *  - Set as selected the item previous to add to the listbox (by ussing the {@link __ItemComponent::setSelected()} method) or<br>
 *  - Use the selected property within the item to be selected to
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:listbox name="categories">
 *     <comp:item value="1" text="Agriculture, forestry and fishing" selected="true"/>
 *     <comp:item value="2" text="Arts, sports and recreation" selected="true"/>
 *     <comp:item value="3" text="Catering and accommodation"/>
 *     <comp:item value="4" text="Construction"/>
 *     ...
 *   </comp:listbox>
 * 
 * </code>
 * 
 * A listbox, as a value holder, returns the value of the selected item as a result of call to the getValue.
 * 
 * i.e.
 * <code>
 * 
 *   public function categories_change(__UIEvent &$event) {
 *      //get the value of the selected item within the combo:
 *      $listbox_value = $event->getComponent()->getValue();
 *  
 *      //...
 *   }
 *      
 * </code>
 *   }
 * 
 * Default events associated to a listbox are the same as associated to a html select element. Most usefull is the <b>change</b> event, raised when the user change the selection.<br> 
 * <br>
 * <br>
 * See the listbox component in action here: {@link http://www.lionframework.org/components/listbox.html}
 *  
 * @see __ItemListComponent, __ComboBoxComponent
 * 
 */
class __ListBoxComponent extends __ComboBoxComponent  {
    
    protected $_rows = 3;
    
    /**
     * Set the number of rows (an item in each row) to show at the same time
     *
     * @param integer $rows
     */
    public function setRows($rows) {
        if(is_numeric($rows) && (int)$rows > 0) {
            $this->_rows = (int) $rows;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong rows value for ListBox component: ' . $rows);
        }
    }
    
    /**
     * Get the number of rows (an item in each row) that will be shown at the same time
     *
     * @return integer
     */
    public function getRows() {
        return $this->_rows;
    }
    
}
