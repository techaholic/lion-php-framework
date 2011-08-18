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
 * Memcache facade implementing the {@link __ICacheHandler}
 * 
 * This class requires a Memcache server as well as PECL::Memcache PHP module loaded in order to work.
 * 
 * @link http://www.danga.com/memcached/
 * @link http://pecl.php.net/package/memcache
 *
 */
class __MemCache extends __CacheHandler {

    const MEMCACHE_DEFAULT_PORT = 11211;
    const MEMCACHE_DEFAULT_SERVER = "localhost";
    
    static protected $_connection = null;
    
    public function __destruct() {
        if (self::$_connection != null) {
            self::$_connection->close();
            self::$_connection = null;
        }
    }
    
    public function __construct() {
        $this->_connectToMemcacheServer();
    }
    
    private function _connectToMemcacheServer() {
        $lion_runtime_directives = __Lion::getInstance()->getRuntimeDirectives();
        
        //Checks if the Memcache module is loaded:
        if (!class_exists('Memcache')) {
            throw new __CacheException("PECL Memcache extension is not installed. Can not use the __MemCache cache handler.");
        }
        //Perform the connection to the memcache server:
        if(self::$_connection == null) {
            self::$_connection = new Memcache();
            if($lion_runtime_directives->hasDirective('MEMCACHE_SERVER')) {
                $server = $lion_runtime_directives->getDirective('MEMCACHE_SERVER');
            }
            else {
                $server = self::MEMCACHE_DEFAULT_SERVER;
            }
            if($lion_runtime_directives->hasDirective('MEMCACHE_PORT')) {
                $port = $lion_runtime_directives->getDirective('MEMCACHE_PORT');
            }
            else {
                $port = self::MEMCACHE_DEFAULT_PORT;
            }
            
            if (!(self::$_connection->connect($server, $port))) {
                throw new __CacheException("Can not connect to memcache server (server: $server, port: $port)");
            }
        }
    }

    public function load($key, $ttl = null) {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }        
        $return_value = null;
        @$cache_value = self::$_connection->get($key);
        if($cache_value !== false) {
            $return_value = $cache_value;
        }
        return $return_value;
    }

    public function save($key, $data, $ttl = null) {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }            
        $return_value = self::$_connection->set($key, $data, 0, $ttl);
        return $return_value;
    }

    public function remove($key) {
        $return_value = self::$_connection->delete($key);
        return $return_value;
    }

    public function clear() {
        $return_value = self::$_connection->flush();
        return $return_value;
    }
}


