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
 * This class is the one in charge of cache management.
 * It can be retrieved from the context by calling to {@link __Context::getCache()} method.
 * i.e.
 * <code>
 * //get the cache from the application context:
 * $cache = __ApplicationContext::getInstance()->getCache();
 * </code>
 * 
 * This class exposes methods to set/get/load/save and clear the cache. 
 * It contains a cache handler (a class implementing the {@link __ICacheHandler} interface) 
 * that is the one in charge of handle the cache storage, so the __Cache class acts a facade in that sense.
 * 
 */
final class __Cache {

    private $_cache_data = array();
    private $_enabled = false;
    
    /**
     * @var __CacheHandler
     */
    private $_cache_handler = null;
    
    final public function __construct() {
        $this->setEnabled(__Lion::getInstance()->getRuntimeDirectives()->getDirective('CACHE_ENABLED'));
    }
    
    public function setEnabled($enabled) {
        $this->_enabled = $enabled;
        //if this is the first time we enable the cache:
        if($this->_cache_handler == null) {
            $cache_handler = __CacheHandlerFactory::createCacheHandler();
            if($cache_handler != null) {
                $this->_cache_handler =& $cache_handler;
            }            
        }
    }
    
    public function getEnabled() {
        return $this->_enabled;
    }
    
    public function isEnabled() {
        return $this->_enabled;
    }
    
    public function &getData($key, $ttl = null) {
        $return_value = null;
        if(!key_exists($key, $this->_cache_data)) {
            if($this->_enabled) {
                $return_value = $this->_cache_handler->load($key, $ttl);
                if($return_value != null) {
                    $this->_cache_data[$key] =& $return_value;
                }
            }
        }
        else {
            $return_value =& $this->_cache_data[$key];
        }
        return $return_value;
    }

    public function setData($key, &$data, $ttl = null) {
        $this->_cache_data[$key] =& $data;
        if($this->_enabled) {
            $this->_cache_handler->save($key, $data, $ttl);
        }
    }

    public function removeData($key) {
        if(key_exists($key, $this->_cache_data)) {
            unset($this->_cache_data[$key]);
        }
        if($this->_enabled) {
            $this->_cache_handler->remove($key);
        }
    }
    
    public function clear() {
        if($this->_enabled) {
            $this->_cache_handler->clear();
        }
    }

}
