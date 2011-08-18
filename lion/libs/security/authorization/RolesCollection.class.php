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


class __RolesCollection {

    private $_roles = array();
    private $_equivalent_permission = null;
    
    public function __construct() {
        $this->_recalculateEquivalentPermission();
    }
    
    public function addRole(__Role &$role) {
        $this->_roles[$role->getId()] =& $role;
        $this->_recalculateEquivalentPermission();
    }
    
    public function fromArray(array &$roles) {
        $this->_roles =& $roles;
        $this->_recalculateEquivalentPermission();
    }
    
    public function &toArray() {
        return $this->_roles;
    }

    public function removeRole($role_id) {
        if(key_exists($role_id, $this->_roles)) {
            unset($this->_roles[$role_id]);
            $this->_recalculateEquivalentPermission();
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROLE', array($role_id));
        }
    }

    public function &getRole($role_id) {
        if(key_exists($role_id, $this->_roles)) {
            return $this->_roles[$role_id];
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROLE', array($role_id));
        }
    }
    
    private function _recalculateEquivalentPermission() {
        $this->_equivalent_permission = new __Permission('', 0);
        foreach($this->_roles as &$role) {
            $equivalent_permission = $role->getEquivalentPermission();
            $this->_equivalent_permission->addJuniorPermission($equivalent_permission);
            unset($equivalent_permission);
        }
    }
    
    public function reset() {
        $this->_roles = array();
        $this->_recalculateEquivalentPermission();
    }

    public function getEquivalentPermission() {
        return $this->_equivalent_permission;
    }

}