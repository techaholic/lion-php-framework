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


class __Permission {

    static protected $_next_binary_representation = 1;
    
    protected $_id = null;
    protected $_binary_representation = 0;
    protected $_junior_permissions = array();
    
    /**
     * Constructor method
     *
     * @param string $id The identifier for current permission
     * @param integer $binary_representation The numeric representation of current permission. If not specified, an unike representation will be assigned.
     */
    final public function __construct($id, $binary_representation = null) {
        $this->_id = strtoupper($id);
        if($binary_representation === null) {
            $this->_binary_representation = __Permission::$_next_binary_representation;
            __Permission::$_next_binary_representation = __Permission::$_next_binary_representation * 2;
        }
        else {
            if(is_numeric($binary_representation)) {
                $this->_binary_representation = $binary_representation;
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_BINARY_REPRESENTATION_FOR_PERMISSION');
            }
        }
    }
    
    final public function getId() {
        return $this->_id;
    }
    
    final public function addJuniorPermission(__Permission &$junior_permission) {
        $this->_junior_permissions[$junior_permission->getId()] =& $junior_permission;
        $this->_binary_representation = $this->_binary_representation | $junior_permission->_binary_representation;
    }

    final public function &getJuniorPermissions() {
        return $this->_junior_permissions;
    }
    
    /**
     * This method check if current permission has at least the same number of permissions 
     * that the given one. It means that current permission is at least a subset of the given one.
     * 
     * @param __Permission $permission The permission to check if it's equivalent to the current one
     * @return boolean true if the permission is equivalent, otherwise false
     */
    final public function isJuniorPermissionOf(__Permission &$permission) {
        $return_value = false;
        if(($this->_binary_representation & $permission->_binary_representation) == $this->_binary_representation) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Overrideable method designed to check in runtime for a given permission dynamically.
     * This method must return true in order to let the security framework know that the current user has the given permission.
     *
     * @return bool
     */
    public function checkPermission() {
        return true;
    }
    
}