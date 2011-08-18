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
 * Represents a page rendered into the client browser.
 * 
 * It has accessors to all the shown components within the page, even if they belong to different views.
 *
 */
class __Page {

    static protected $_instance = null;
    
    protected $_id = null;
   
    protected $_component_handlers = array();
    
    protected function __construct() {
        $this->_id = uniqid();
    }
    
    /**
     * Get a singleton instance representing the current page
     *
     * @return __Page
     */
    static public function &getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new __Page();
        }
        return self::$_instance;
    }

    /**
     * Set an identifier for current page.
     *
     * @param unknown_type $id An unique identifier
     */
    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Get the identifier associated to the current page
     *
     * @return string
     */
    public function getId() {
        return $this->_id;
    }
    
    public function hasView($view_code) {
        return key_exists($view_code, $this->_view_codes);
    }
    
    public function addView($view_code) {
        $this->_view_codes[$view_code] = true;
    }
    
    
}