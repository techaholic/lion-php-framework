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
 * This is the interface associated to a cache handler
 * 
 * @see __CacheHandler
 *
 */
interface __ICacheHandler {
    
    /**
     * Loads data from cache
     *
     * @param unknown_type $cache_id
     * @param unknown_type $context_id
     * @return mixed
     */
    public function load($key, $ttl = null);
    
    /**
     * Saves data to cache
     *
     * @param mixed $data
     * @param string $cache_id
     * @param string $context_id
     * @return bool true if the data has been saved successfully
     */
    public function save($key, $data, $ttl = null);
    
    /**
     * Removes data from cache
     *
     * @param string $cache_id
     * @param string $context_id
     * @return bool true if the data has been removed successfully
     */
    public function remove($key);
    
    /**
     * Clear the entire cache
     *
     * @param string $context_id
     * @return bool true if the cache has been cleaned successfully
     */
    public function clear();
    
    public function setDefaultTtl($ttl);
    
    public function getDefaultTtl();
    
}