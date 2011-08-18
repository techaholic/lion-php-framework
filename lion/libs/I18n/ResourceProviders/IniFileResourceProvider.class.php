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
 * @package    I18n
 * 
 */

class __IniFileResourceProvider extends __ResourceProvider {
        
    protected $_language_dir = null;
    protected $_filename = null;
    protected $_encoding = null;
    
    public function getLanguageFile($language_iso_code, __ActionIdentity $action_identity = null) {
        if($action_identity != null) {
            $filename = $action_identity->getControllerCode() . '.ini';
        }
        else {
            $filename = $this->_filename;
        }
        $return_value = $this->_language_dir . DIRECTORY_SEPARATOR . $language_iso_code . DIRECTORY_SEPARATOR . $filename;
        return $return_value;
    }
    
    public function setFileEncoding($encoding) {
        $this->setEncoding($encoding);
    }
    
    public function getFileEncoding() {
        return $this->getEncoding();
    }
    
    public function setEncoding($encoding) {
        $this->_encoding = $encoding;
    }
    
    public function getEncoding() {
        return $this->_encoding;
    }

    public function setLanguageDir($language_dir) {
        $this->_language_dir = __PathResolver::resolvePath($language_dir);
    }
    
    public function getLanguageDir() {
        return $this->_language_dir;
    }
    
    public function setFilename($filename) {
        $this->_filename = $filename;
    }
    
    public function getFilename() {
        return $this->_filename;
    }

    public function loadResources($language_iso_code, __ActionIdentity $action_identity = null) {
        $language_file = $this->getLanguageFile($language_iso_code, $action_identity);
        $return_value = array();
        if(is_file($language_file) && is_readable($language_file)) {
            $resources = parse_ini_file($language_file, false);
            foreach($resources as $key => $value) {
                if($this->_encoding != null) {
                    $value = iconv($this->_encoding, iconv_get_encoding("internal_encoding"), $value);
                }
                $resource = $this->_createResource($key, $value);
                $return_value[strtoupper($key)] = $resource;
                unset($resource);
            }
        }
        return $return_value;
    }
    
}