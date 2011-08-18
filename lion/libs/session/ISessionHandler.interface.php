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
 * @package    Session
 * 
 */


/**
 * This is the interface for session handler classes.
 * 
 * If you define a new session handler, you need to implement this interface
 *
 */
interface __ISessionHandler {
    
    /**
     * This method is called when a session is started
     *
     * @param string $save_dir The path to save the session to
     * @param string $session_name The name of the session
     */
    public function open( $save_dir, $session_name );
    
    /**
     * This method is called when a session is closed
     *
     */
    public function close();

    /**
     * This method is called to retrieve information from the session
     *
     * @param string $session_id An identifier of the requested information
     */
    public function read( $session_id );

    /**
     * This method is called to store information to the session
     *
     * @param string $session_id An identifier for the information to store to
     * @param mixed $session_data The data to store to
     */
    public function write( $session_id, $session_data );
    
    /**
     * This method is called to destroy some session information 
     *
     * @param string $session_id An identifier for the information to remove from
     */
    public function destroy( $session_id );
    
    /**
     * This method is called in order to specify the maximum time to invalidate the session
     *
     * @param integer $max_expire_time The maximum time to invalidate the session
     */
    public function gc( $max_expire_time );

    
}