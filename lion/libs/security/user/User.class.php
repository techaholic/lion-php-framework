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
 * This is a very simple user class provided by the framework implementing the {@link __IUser} interface, 
 * required by the security framework.
 *
 */
class __User implements __IUser {

    protected $_credentials = null;
    protected $_identity    = null;
    protected $_roles       = array();
    protected $_is_enabled  = true;
    
    public function &getRoles() {
        return $this->_roles;
    }
    
    public function addRole(__Role &$role) {
        $this->_roles[$role->getId()] =& $role;
    }
    
    public function setRoles(array $roles) {
        foreach($roles as &$role) {
            $this->addRole($role);
        }
    }
    
    public function activateRoles(__UserSession &$user_session) {
        $user_session->reset();
        $roles = $this->getRoles();
        foreach($roles as &$role) {
            $user_session->addActiveRole($role);
        }
    }
    
    /**
     * Checks if current user has permission to access to a given system resource
     *
     * @param __SystemResource $system_resource
     * @return bool
     */
    public function hasAccess(__SystemResource &$system_resource) {
        $required_permission = $system_resource->getRequiredPermission();
        return $this->hasPermission($required_permission);
    }
    
    /**
     * Checks if current user has a given permission
     *
     * @param __Permission $permission
     * @return bool
     */
    public function hasPermission(__Permission &$permission) {
        $roles_collection = new __RolesCollection();
        $roles_collection->fromArray($this->getRoles());
        $roles_equivalent_permission = $roles_collection->getEquivalentPermission();
        return $permission->isJuniorPermissionOf($roles_equivalent_permission);
    }
    
    public function setEnabled($is_enabled) {
        $this->_is_enabled = (bool) $is_enabled;
    }
    
    public function isEnabled() {
        return $this->_is_enabled;
    }

    public function setCredentials(__ICredentials &$credentials) {
        $this->_credentials =& $credentials;
    }
    
    public function &getCredentials() {
        return $this->_credentials;
    }
    
    public function setIdentity(__IUserIdentity &$user_identity) {
        $this->_identity = $user_identity;
    }
    
    public function &getIdentity() {
        return $this->_identity;
    }
    
    public function onLogout() {
        //nothing to do
    }
    
}