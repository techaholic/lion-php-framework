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
 * @package    Security
 * 
 */


/**
 * This class stores and manages all the roles and associated permissions in 2 ways:<br>
 * - Roles definitions<br>
 * - Permissions required by system resources for each role to grant/revoke the access<br>
 * 
 */
final class __RoleManager {
    
    /**
     * This variable stores all system's roles
     *
     * @var array
     */
    private $_roles       = array();
        
    static private $_instance = null;     
    
    private function __construct() {
        $this->startup();
    }
    
    public function startup() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__RoleManager::_roles')) {
            $this->_roles = $session->getData('__RoleManager::_roles');
        }
        else {
            $roles = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('role-definitions');
            if( is_array($roles) ) {
                $this->_roles =& $roles;
            }
            $session->setData('__RoleManager::_roles', $this->_roles);
        }
    }

    /**
     * This method return a singleton instance of __RoleManager instance
     *
     * @return __RoleManager a reference to the current __RoleManager instance
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __RoleManager();
        }
        return self::$_instance;
    }    
    
    /**
     * Get if a role with the specified role id exists or not
     *
     * @param string $role_id The role id
     * @return bool true if there is any role with the specified roile id
     */
    public function hasRole($role_id) {
        $role_id = strtoupper($role_id);
        return key_exists($role_id, $this->_roles);
    }
    
    /**
     * Returns a {@link __Role} instance that correspond with the specified role id
     *
     * @param string $role_id The role id
     * @return __Role The requested {@link __Role} instance
     */
    public function &getRole($role_id) {
        $return_value = null;
        $role_id = strtoupper($role_id);
        if(key_exists($role_id, $this->_roles)) {
            $return_value =& $this->_roles[$role_id];
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROLE_ID', array($role_id));
        }
        return $return_value;
    }
     
}