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


class __AuthenticationManager extends __Singleton {

    protected $_authenticators = array();

    /**
     * This variable stores the active user in session
     *
     * @var __IUser
     */
    protected $_user = null;    

    public function __construct() {
    }
    
    /**
     * Returns the singleton instance of __AuthenticationManager class
     *
     * @return __AuthenticationManager The singleton instance of __AuthenticationManager class
     */
    static public function &getInstance() {
        return __Singleton::getSingleton('authenticationManager');
    }    
    
    public function addAuthenticator(__IAuthenticator &$authenticator) {
        $this->_authenticators[] = &$authenticator;
        if($this->_user == null) {
            $this->logonAsAnonymous();
        }
    }

    public function setAuthenticators(array &$authenticators) {
        foreach($authenticators as &$authenticator) {
            if(! $authenticator instanceof __IAuthenticator ) {
                throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_AUTHENTICATOR_CLASS', array(get_class($authenticator)));
            }
        }
        $this->_authenticators = $authenticators;
        if($this->_user == null) {
            $this->logonAsAnonymous();
        }
    }
    
    /**
     * Sets the authenticated user without run the authentication process.
     * The given user's roles will be also activated within the user session
     *
     * @param __IUser $user
     */
    public function setAuthenticatedUser(__IUser &$user) {
        unset($this->_user);
        __AuthorizationManager::getInstance()->unsetUserRoles();    	
    	$this->_user =& $user;
    	__AuthorizationManager::getInstance()->activateUserRoles($this->_user);
    }
    
    /**
     * Gets the authenticated user, or the anonymous user if applicable
     *
     * @return __IUser
     */
    public function getAuthenticatedUser() {
    	if (!isset($this->_user)) {
    		$this->_user = null;
    	}
        return $this->_user;
    }
    
    /**
     * Checks if there is any authenticated user (included the anonymous user)
     *
     * @return bool
     */
    public function isAuthenticated() {
        $return_value = false;
        if (isset($this->_user) && $this->_user instanceof __IUser) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Checks if the authenticated user is the anonymous one
     *
     * @return bool
     */
    public function isAnonymous() {
        $return_value = false;
        if($this->_user instanceof __IUser && $this->_user->getCredentials() instanceof __AnonymousCredentials) {
            $return_value = true;
        }
        return $return_value;
    }

    /**
     * This method perform the logon operation by checking the specified credentials for the specified user, 
     * changing the user and user_session according to the 
     *
     * @param __ICredentials $credentials
     */
    final public function logon(__IUserIdentity $user_identity, __ICredentials $credentials) {
        foreach($this->_authenticators as &$authenticator) {
            $user = $authenticator->authenticate($user_identity, $credentials);
            if( $user instanceof __IUser ) {
                $this->_user =& $user;
                __AuthorizationManager::getInstance()->activateUserRoles($this->_user);
                return true;
            }
        }
        return false;
    }
    
    final public function logonAsAnonymous() {
        unset($this->_user);
        $this->logon(new __AnonymousIdentity(), new __AnonymousCredentials());
    }

    final public function logout() {
        if($this->_user != null) {
            $this->_user->onLogout();
            unset($this->_user);
            $this->_user = null;
            __AuthorizationManager::getInstance()->unsetUserRoles();
            $this->logonAsAnonymous();
        }
    }
    
    
    
}