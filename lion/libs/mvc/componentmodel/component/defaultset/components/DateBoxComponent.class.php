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
 * Datebox is an inputbox with a calendar image at right, showing a calendar to pick a date when click on it.
 * 
 * Datebox tag is <b>datebox</b>
 * 
 * i.e.
 * <code>
 * 
 *   Please select a date: <comp:datebox name="invoice_date"/>
 * 
 * </code>
 *
 * Datebox component is very similar to an inputbox component. Date picked by the user can be retrieved by asking the {@link __InputComponent::getValue()} method.<br>
 * <br>
 * <br>
 * See the datebox component in action here: {@link http://www.lionframework.org/components/datebox.html}
 * 
 * @see __InputBox, __InputComponent 
 * 
 */
class __DateBoxComponent extends __InputComponent {
        
    protected $_date_format = '%Y-%m-%d';
    
    public function setDateFormat($date_format) {
        $this->_date_format = $date_format;
    }
    
    public function getDateFormat() {
        return $this->_date_format;
    }
    
}