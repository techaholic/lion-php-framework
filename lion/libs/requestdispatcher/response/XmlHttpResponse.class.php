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
 * @package    Response
 * 
 */

class __XmlHttpResponse extends __HttpResponse {

    protected $_headers = array();
    protected $_boundary = null;
    protected $_use_x_mixed_replace_content = false;
    protected $_initialized = false;
    
    public function __construct() {
    }

    protected function _initializeXmlHttpResponse() {
        //use multipart long-pulling reverse ajax for other than IE
        $user_agent = strtoupper($_SERVER['HTTP_USER_AGENT']);
        //strstr($user_agent, "WEBKIT") === false &&
        if(strstr($user_agent, "FIREFOX") === false) {
            $this->_headers = array('Content-type: application/json');
        }
        else {
            $this->_use_x_mixed_replace_content = true;
            $this->_boundary = rand(1000, 9999);
            $this->_msie = false;
            $this->_headers = array('Content-type: multipart/x-mixed-replace;boundary="rn' . $this->_boundary . '"');
            $this->appendContent("--rn" . $this->_boundary . "\n");
            //parent::flush();
        }
        $this->_initialized = true;
    }
    
    public function flush() {
        if($this->_initialized == false) {
            $this->_initializeXmlHttpResponse();
        }
        //use multipart long-pulling reverse ajax for other than IE
        if($this->_use_x_mixed_replace_content == true) {
            $this->prependContent("Content-type: text/plain\n\n");
            $this->appendContent("\n--rn" . $this->_boundary . "\n");
        }
        parent::flush();
    }   
    
}