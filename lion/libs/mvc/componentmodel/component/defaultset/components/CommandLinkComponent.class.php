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
 * A command link represents a link.
 * <br>
 * The purpose of a command link is:<br>
 *  - To redirect the navigation to another page or<br>
 *  - To execute a portion of code when the user click on it<br>
 * <br>
 * A command link tag is "commandlink"
 * 
 * i.e.
 * <code>
 *   
 *   <comp:commandlink name="see_invoice_detail" caption="See invoice detail"/>
 * 
 * </code>
 * 
 * A command link has a <b>caption</b>, which is the text shown within the link.<br>
 * <br>
 * When a command link is clicked, the <b>click</b> event is raised, allowing to execute a portion of code associated to that event<br>
 * i.e.
 * <code>
 * 
 *   public function see_invoice_detail_click(__UIEvent &$event) {
 *       //your code here to be executed when the user click the link
 *   }
 * 
 * </code>
 * <br>
 * <br>
 * See the commandlink component in action here: {@link http://www.lionframework.org/components/combobox.html}<br>
 * <br>
 * @see __FormComponent, __CommandLinkComponent
 *
 */
class __CommandLinkComponent extends __UIComponent implements __IPoolable {

    protected $_caption = null;
    
    /**
     * Set the caption to be shown within the link
     *
     * @param string $caption
     */
    public function setCaption($caption) {
        $this->_caption = $caption;
    }
    
    /**
     * Get the caption associated to the current link
     *
     * @return string
     */
    public function getCaption() {
        return $this->_caption;
    }
    
}
