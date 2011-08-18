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
 * @package    Base
 * 
 */

/**
 * Generic Singleton base class usefull to retrieve a singleton instance from the current context.
 * <br>
 * Just inherit(extend) from __Singleton and add a getter like the following one:
 * <br>
 * <code>
 * public static function &getInstance() {
 *   return __Singleton::getSingleton(AN_INSTANCE_ID);
 * }
 * </code>
 * <br>
 * Note that you need to specify an instance id to retrieve the singleton instance from the current context.<br>
 * The getSingleton static method will call to the getInstance method of the current {@link __Context} instance
 * 
 */
abstract class __Singleton {
    
    /**
     * Protected getter for singleton instances
     *
     * @param string $instance_id The instance id to retrieve the instance from current {@link __Context} instance
     * @return object The requested instance
     */
    protected static function &getSingleton($instance_id){
        $return_value = null;
        if (__ContextManager::getInstance()->getCurrentContext()->hasInstance($instance_id)) {
            $return_value = __ContextManager::getInstance()->getCurrentContext()->getInstance($instance_id);
        }
        return $return_value;
    }
    
    /**
     * Denie cloning of singleton objects
     *
     */
    public final function __clone(){
        throw __ExceptionFactory::getInstance()->createException('Clone is not allowed for a __Singleton class');
    }    
}
    
