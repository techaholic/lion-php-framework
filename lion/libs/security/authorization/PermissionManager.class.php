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


final class __PermissionManager {
    
    static private $_instance = null;
    private $_permissions = array();
    
    private function __construct() {
        $this->startup();
    }
    
    public function startup() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__PermissionManager::_permissions')) {
            $this->_permissions = $session->getData('__PermissionManager::_permissions');
        }
        else {
            $permissions = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('permission-definitions');
            if( is_array($permissions) ) {
                $this->_permissions =& $permissions;
            }
            $session->setData('__PermissionManager::_permissions', $this->_permissions);
        }
    }
    
    static public function &getInstance() {
        if(__PermissionManager::$_instance == null) {
            __PermissionManager::$_instance = new __PermissionManager();
        }
        return __PermissionManager::$_instance;
    }

    public function hasPermission($permission_id) {
        $permission_id = strtoupper($permission_id);
        return key_exists($permission_id, $this->_permissions);
    }
    
    /**
     * Returns a permission identified by the given id.
     *
     * @param unknown_type $permission_id
     * @return unknown
     */
    public function &getPermission($permission_id) {
        $return_value = null;
        $permission_id = strtoupper($permission_id);
        if(key_exists($permission_id, $this->_permissions)) {
            $return_value = $this->_permissions[$permission_id];
        }
        else {
            //lazy initialization of special permission PERMISSION_ALL:
            if($permission_id == 'PERMISSION_ALL') {
                //PERMISSION_ALL:
                $return_value = new __Permission('PERMISSION_ALL', 0);
                foreach($this->_permissions as &$permission) {
                    $return_value->addPermission($permission);
                    unset($permission);
                }
                $this->_permissions[$return_value->getId()] =& $return_value;
            }
            else {            
                throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_PERMISSION_ID', array($permission_id));
            }
        }
        return $return_value;
    }
    
}