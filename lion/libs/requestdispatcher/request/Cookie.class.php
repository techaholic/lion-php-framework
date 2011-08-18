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
 * @package    Request
 * 
 */

class __Cookie {

    protected $_name  = null;
    protected $_value = null;
    protected $_ttl   = null;
    protected $_path  = null;
    protected $_domain = null;
    protected $_secure = false;
    protected $_http_only = false;
    
    public function __construct($name , $value = null, $ttl = null, $path = null, $domain = null, $secure = false , $http_only = false) {
        $this->setName($name);
        $this->setValue($value);
        $this->setTtl($ttl);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure($secure);
        $this->setHttpOnly($http_only);
    }
    
    public function &setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function &setValue($value) {
        $this->_value = $value;
        return $this;
    }
    
    public function getValue() {
        return $this->_value;
    }

    /**
     * Sets the ttl in secons
     *
     * @param integer $ttl
     */
    public function &setTtl($ttl) {
        $this->_ttl = time() + $ttl;
        return $this;
    }
    
    public function getTtl() {
        return $this->_ttl;
    }
    
    public function &setPath($path) {
        $this->_path = $path;
        return $this;
    }
    
    public function getPath() {
        return $this->_path;
    }
    
    public function &setDomain($domain) {
        $this->_domain = $domain;
        return $this;
    }
    
    public function getDomain() {
        return $this->_domain;
    }
    
    public function &setSecure($secure) {
        $this->_secure = (bool) $secure;
        return $this;
    }
    
    public function getSecure() {
        return $this->_secure;
    }
    
    public function &setHttpOnly($http_only) {
        $this->_http_only = (bool) $http_only;
        return $this;
    }
    
    public function getHttpOnly() {
        return $this->_http_only;
    }
    
}
