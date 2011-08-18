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
 * Checkbox represents the typical checkbox.
 * <br>
 * The CheckBox tag is "checkbox"
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:checkbox name="accept_terms" caption="I have read and accept terms and conditions"/>
 * 
 * </code>
 *
 * The checkbox is a valueholder containing a boolean value. When the checkbox is checked, value is true, otherwise value is false.<br>
 * <br>
 * A checkbox component raises the <b>click</b> event when the user check or uncheck the component.<br>
 * See the {@tutorial View/Components/View.Events.pkg} section for more information about events handled by lion components.<br>  
 * 
 * <br>
 * <br>
 * See the checkbox component in action here: {@link http://www.lionframework.org/components/checkbox.html}
 * 
 */
class __CheckBoxComponent extends __InputBoxComponent {

	protected $_value   = false;
	protected $_caption = null;
	
	/**
	 * Set a caption to be show within the checkbox
	 *
	 * @param string $caption
	 */
	public function setCaption($caption) {
	    $this->_caption = $caption;
	}
	
	/**
	 * Get the caption associated to the current checkbox
	 *
	 * @return unknown
	 */
	public function getCaption() {
	    return $this->_caption;
	}
    
	/**
	 * Set the value associated to the current checkbox (true as checked, false as unchecked)
	 *
	 * @param bool $value
	 */
    public function setValue($value) {
        $this->_value = $this->_toBool($value);
    }
    
    public function getValue() {
        return $this->_value;
    }
    
}