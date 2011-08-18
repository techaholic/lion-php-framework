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
 * This is the common login/password credential type
 * 
 * 
 */
class __PasswordCredentials implements __ICredentials {

    protected $_password = null;
    protected $_encryptor = null;
    
    public function setEncryptor(__IEncryptor $encryptor) {
        $this->_encryptor = $encryptor;
    }
        
    public function setPassword($password, $already_encrypted = false) {
        if($already_encrypted == false && $this->_encryptor != null) {
            $password = $this->_encryptor->encrypt($password);
        }
        $this->_password = $password;
    }
        
    public function getPassword() {
        return $this->_password;
    }
    
    public function checkCredentials(__ICredentials &$credentials) {
        $return_value = false;
        if( $credentials instanceof __PasswordCredentials ) {
            if( $this->getPassword() == $credentials->getPassword() ) {
                $return_value = true;
            }
        }
        return $return_value;
    }
    
}

