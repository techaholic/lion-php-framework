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
 * This class handles active roles for current user session.
 * 
 * Active roles are the subset of user's roles that are checked in order to grant or revoke the access to a system's resource.<br>
 * Each system resource has associated a required permission to access to. The access is granted if there is any active role that contains the required permission.<br>
 * <br>
 * See {@tutorial Security/Authorization.pkg} section for more information
 *
 */
final class __UserSession {
    
    /**
     * Contains a collection of active roles for current user session.
     *
     * @var __RolesCollection
     */
    private $_active_roles = null;

    /**
     * Constructor method
     *
     */
    public function __construct() {
        $this->_active_roles = new __RolesCollection();
    }
    
    /**
     * Adds a new role to the active roles collection
     *
     * @param __Role &$role The role to add to
     */
    public function addActiveRole(__Role &$role) {
        $this->_active_roles->addRole($role);
    }
    
    /**
     * Removes a role (identified by his id) from the active roles collection.
     *
     * @param string $role_id The id of the role to remove to
     */
    public function removeActiveRole($role_id) {
        $this->_active_roles->removeRole($role_id);
    }
    
    /**
     * Returns the active roles collection
     *
     * @return __RolesCollection The active roles collection
     */
    public function &getActiveRoles() {
        return $this->_active_roles;
    }
    
    /**
     * Clear the active roles collection
     *
     */
    public function reset() {
        $this->_active_roles->reset();
    }
    
    /**
     * This method is very similar to the {@link hasAccess} method.
     * The difference is that this method will call to the system resource's {__ISystemResource::onAccessError()} method 
     * in case the session doesn't contains the required permission to access to the system resource.
     *
     * @param __SystemResource &$system_resource The system resource to check the access to
     * @return boolean true if the session contains the required permission to access to the system resource
     */
    final public function checkAccess(__SystemResource &$system_resource) {
        $return_value = false; //by default
        $required_permission = $system_resource->getRequiredPermission();
        try {
            $return_value = $this->hasPermission($required_permission);
            if(!$return_value) {
                $system_resource->onAccessError();
            }
        }
        catch(Exception $e) {
            $system_resource->onAccessError($e);
        }
        return $return_value;
    }
    
    /**
     * Return true if active roles in session contains the required permission to access to the system resource.
     * 
     * @param __SystemResource &$system_resource The system resource to check the access to
     * @return boolean true if the sesion contains the required permission to access to the system resource
     */   
    final public function hasAccess(__SystemResource &$system_resource) {
        $return_value = false; //by default
        $required_permission = $system_resource->getRequiredPermission();
        try {
            $return_value = $this->hasPermission($required_permission);
        }
        catch(Exception $e) {
            //nothing to do, just catch the exception
        }
        return $return_value;
    }
    
    /**
     * Checks if active roles have a given permission
     *
     * @param integer $permission
     * @return unknown
     */
    final public function hasPermission(__Permission $permission) {
        $return_value = false;
        if($permission == null) {
            $return_value = true;
        }
        else {
            $active_roles_equivalent_permission = $this->_active_roles->getEquivalentPermission();
            if($permission->isJuniorPermissionOf($active_roles_equivalent_permission)) {
                $return_value = $permission->checkPermission();
            }
        }
        return $return_value;
    }


}