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
 * Represents the typical text box as a one single line rectangle to write text inside it.
 * 
 * An inputbox is one of the most basic valueholders, where the value is the text written by the user<br>
 * 
 * Inputbox tag is <b>inputbox</b><br>
 * 
 * i.e.
 * <code>
 * 
 *   First name: <comp:inputbox name="first_name"/>
 * 
 * </code>
 * 
 * To retrieve the input value, use the {@link __InputComponent::getValue()} method, while to set the text use the {@link __InputComponent::setValue()}.<br>
 * The event that is raised when the user change the focus (the most typical use case for inputboxes), is the <b>blur</b> event.<br>
 * <br>
 * <br>
 * See the inputbox component in action here: {@link http://www.lionframework.org/components/inputbox.html}
 *  
 */
class __InputBoxComponent extends __InputComponent {                                        
    
    protected $_value   = null;
    protected $_example_value = null;
    
    public function setExampleValue($example_value) {
        $this->_example_value = $example_value;
    }
    
    public function getExampleValue() {
        return $this->_example_value;
    }  
        
}
