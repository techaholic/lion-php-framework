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


class __Authenticator implements __IAuthenticator {
    
    /**
     * User loader instance (the one in charge of load __IUser instances)
     *
     * @var __IUserLoader
     */
    protected $_user_loader = null;
    
    /**
     * Set the instance implementing the {@link __IUserLoader} to load an user when
     * the authentication is requested
     *
     * @param __IUserLoader $user_loader
     */
    public function setUserLoader(__IUserLoader &$user_loader) {
        $this->_user_loader =& $user_loader;
    }


    /**
     * Get the __IUserLoader instance that belong to the current __AuthorizationManager
     *
     * @return __IUserLoader
     */
    public function &getUserLoader() {
        return $this->_user_loader;
    }

    /**
     * This method is called to compare a credentials instance with the current one.
     *
     * @param __ICredentials $credentials The credentials to be compared with the current one.
     * @return boolean true if the credentials are equals, otherwise false.
     */
    public function &authenticate(__IUserIdentity $user_identity, __ICredentials $credentials) {
        $return_value = null;
        $user =& $this->_user_loader->loadUser($user_identity);
        if($user != null) {
            $user_credentials = $user->getCredentials();
            if($user_credentials != null && $user_credentials->checkCredentials($credentials)) {
                $return_value =& $user;
            }
        }
        return $return_value;
    }
    
}
