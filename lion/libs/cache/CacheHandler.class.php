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
 * @package    Cache
 * 
 */

/**
 * This is the base class for cache handlers.
 * A cache handler is a facade to the real cache library (i.e. memcache, cachelite, ...)
 * 
 * @see __CacheLite, __MemCache
 *
 */
abstract class __CacheHandler implements __ICacheHandler {
    
    /**
     * ttl = 0 is the default value (never expire)
     *     
     */
    protected $_default_ttl = 0;

    public function setDefaultTtl($default_ttl) {
        if(is_numeric($default_ttl)) {
            $this->_default_ttl = (int)$default_ttl;
        }
        else {
            throw new __CacheException('Wrong value for default cache ttl: ' . $default_ttl);
        }
    }
    
    public function getDefaultTtl() {
        return $this->_default_ttl;
    }
    
}