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
 * Apc facade implementing the {@link __ICacheHandler}
 * 
 * This class requires APC extension enabled in order to work.
 * 
 * @link http://www.php.net/apc
 *
 */
class __Apc extends __CacheHandler {
    
    public function load($key, $ttl = null) {
        $return_value = apc_fetch($key);
        if($return_value === false) {
            $return_value = null;
        }
        return $return_value;
    }

    public function save($key, $data, $ttl = null) {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }          
        $return_value = apc_store ($key, $data, $ttl);
        return $return_value;
    }

    public function remove($key) {
        $return_value = apc_delete ( $key );
        return $return_value;
    }

    public function clear() {
        $return_value = apc_clear_cache();
        return $return_value;
    }
}


