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
 * @package    Context
 * 
 */


/**
 * This class is a helper to resolve a path to an instance.
 *
 */
class __InstanceResolver {

    private static $_instance = null;

    private function __construct() {
    }

    /**
     * This method return a singleton instance of __InstanceResolver instance
     *
     * @return __InstanceResolver a reference to the current __InstanceResolver instance
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __InstanceResolver();
        }
        return self::$_instance;
    }    
    
    /**
     * This method receives a path to access to an instance and returns a reference to the instance.
     * 
     * i.e., <emphasis>authenticationManager.authenticatedUser.username</emphasis> will be resolved as:
     * <code>
     * 
     * $authentication_manager = __CurrentContext::getInstance()
     *                           ->getInstance('authenticationManager');
     * 
     * $authenticated_user = $authentication_manager
     *                           ->getAuthenticatedUser();
     * 
     * $username = $authenticated_user->getUsername();
     * 
     * </code>
     *
     * @param unknown_type $instance_dir
     * @return unknown
     */
    public function &resolveInstance($instance_dir) {
        if(strpos($instance_dir, '.') !== false) {
            $root_instance_name = trim(substr($instance_dir, 0, strpos($instance_dir, '.')));
            if(!__CurrentContext::getInstance()->hasInstance($root_instance_name)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_ID_NOT_FOUND', array($root_instance_name));
            }
            $current_instance = __CurrentContext::getInstance()->getContextInstance($root_instance);
            $instance_dir = trim(substr($instance_dir, strpos($instance_dir, '.') + 1));
            while(strpos($instance_dir, '.') !== false) {
                $property_name = trim(substr($instance_dir, 0, strpos($instance_dir, '.')));
                $getter_method = 'get' . ucfirst($property_name);
                if(method_exists($current_instance, $getter_method)) {
                    $current_instance = $current_instance->$getter_method();
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('ERR_GETTER_NOT_FOUND_FOR_PROPERTY', array(get_class($current_instance), $property_name));
                }
            }
        }
        else {
            $root_instance_name = trim($instance_dir);
            if(!__CurrentContext::getInstance()->hasInstance($current_instance_name)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_NOT_FOUND', array($root_instance_name));
            }
            $current_instance = __CurrentContext::getInstance()->getInstance($root_instance_name);
        }
        return $current_instance;
    }    
    
}