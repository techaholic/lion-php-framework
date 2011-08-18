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
 * @package    Helpers
 * 
 */

class __Image {
    
    private $_name = null;
    private $_mime_type   = 'image/gif';
    private $_base64_data = null;
    
    public function __construct($name, $filename = null) {
        $this->_name = $name;
        if($filename != null) {
            //todo: populate from file            
        }
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function setBase64Data($base64_data) {
        $this->_base64_data = trim($base64_data);
    }
    
    public function getBase64Data() {
        return $this->_base64_data;
    }
    
    public function setMimeType($mime_type) {
        $this->_mime_type = $mime_type;
    }
    
    public function getMimeType() {
        return $this->_mime_type;
    }
    
    public function getDataUri() {
        $return_value = "data:" . $this->_mime_type . ";base64," . $this->_base64_data;
        return $return_value;
    }

    public function getMhtmlUri() {
        $return_value = "local:/$this->_name";
        return $return_value;
    }
    
    public function getMhtmlNextPart() {
        $return_value = "        
------=_NextPart
Content-Location: local:/$this->_name
Content-Transfer-Encoding: base64
Content-Type: $this->_mime_type

$this->_base64_data

";
        return $return_value;
    }

}
