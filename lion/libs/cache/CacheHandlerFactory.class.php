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
 * This is the factory for {@link __CacheHandler}.
 *
 */
final class __CacheHandlerFactory {

    /**
     * Creates a new cache handler (a class implementing the {@link __ICacheHandler}) based on context configuration.
     *
     * @return __CacheHandler
     */
    static final public function &createCacheHandler() {
        $return_value = null;
        $cache_handler_class = __Lion::getInstance()->getRuntimeDirectives()->getDirective('CACHE_HANDLER_CLASS');
        if(!empty($cache_handler_class)) {
            $cache_impl_dir = LION_CACHE_DIR . DIRECTORY_SEPARATOR . 'impl';
            if(!class_exists($cache_handler_class)) {
                switch($cache_handler_class) {
                    case '__Apc':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'Apc.class.php';
                        break;
                    case '__CacheLite':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'CacheLite.class.php';
                        break;
                    case '__MemCache':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'MemCache.class.php';
                        break;
                    default:
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . __Lion::getInstance()->getRuntimeDirectives()->getDirective('CACHE_HANDLER_FILE');
                        break;
                }
                include_once($cache_impl_file);
            }
            
            $return_value = new $cache_handler_class();
            if (! $return_value instanceof __ICacheHandler ) {
                throw new Exception('Wrong cache handler class: ' . $cache_handler_class . '. A class implementing the __ICacheHandler was expected.');
            }
            
        }
        return $return_value;
    }
    
    
}