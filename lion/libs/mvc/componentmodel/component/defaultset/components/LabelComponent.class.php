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
 * The label component is used to show a text.
 * 
 * Labels are usefull to control text shown as the result of event executions (i.e. to change a text as the result of an ajax call)<br>
 * <br>
 * Labels tag is <b>label</b>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:label name="client_name" text="Antonio Parraga"/>
 * 
 * </code>
 * <br>
 * We can alter the label's text by setting the text property, i.e.
 * <code>
 * 
 *   $label = $this->getComponent('client_name');
 *   $label->setText('Carolina Kop');
 * 
 * </code>
 *
 */
class __LabelComponent extends __UIComponent implements __IPoolable {

	protected $_text = null;
    
	/**
	 * Set the text to be shown by the label component
	 *
	 * @param string $text
	 */
	public function setText($text) {
		$this->_text = $text;
	}
	
	/**
	 * Get the text to be shown by the label component
	 *
	 * @return string
	 */
	public function getText() {
		return $this->_text;
	}
	    
	/**
	 * Magic string method, which returns the text property value
	 *
	 * @return string
	 */
    public function __toString() {
        return $this->getText();
    }
}
