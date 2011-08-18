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



interface __IUser {
    
    /**
     * Returns an array of role ids for an user instance
     * 
     * Note that an __IUser implementation does not need to store __Role instances, just the ids for those roles.
     *
     * @return array An array of role ids for an user instance
     */
    public function &getRoles();
            
    /**
     * Activate/deactivate roles in __UserSession instance
     *
     */
    public function activateRoles(__UserSession &$user_session);
    
    /**
     * Returns if an user instance is enabled or not
     *
     * @return boolean true if an user instance is enabled, otherwise false
     */
    public function isEnabled();
    
    /**
     * Returns the user credentials
     * 
     * @return __ICredentials The user credentials
     *
     */
    public function &getCredentials();
    
    /**
     * Returns the user identity
     * 
     * @return __IUserIdentity The user identity
     *
     */
    public function &getIdentity();
    
    /**
     * Checks if current user has access to a given system resource
     *
     * @param __SystemResource $system_resource
     * @return bool 
     */
    public function hasAccess(__SystemResource &$system_resource);
    
    /**
     * Checks if current user has a given permission
     *
     * @param __Permission $permission
     * @return bool
     */
    public function hasPermission(__Permission &$permission);
    
    public function onLogout();
    
}