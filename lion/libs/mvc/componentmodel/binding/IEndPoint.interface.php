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
 * @package    ComponentModel
 * 
 */

/**
 * This is the interfaz that both client and server end points must implement.
 * 
 * @see __IServerEndPoint, __IClientEndPoint
 *
 */
interface __IEndPoint {
    
    /**
     * BIND_DIRECTION_S2C (Server 2 Client) used to allow the synchronization from server to client but not the oposite way
     *
     */
    const BIND_DIRECTION_S2C = 1;
    
    /**
     * BIND_DIRECTION_C2S (Client 2 Server) used to allow the synchronization from client to server but not the oposite way
     *
     */
    const BIND_DIRECTION_C2S = 2;
    
    /**
     * BIND_DIRECTION_ALL used to allow the synchonization from client to server and the oposite way
     *
     */
    const BIND_DIRECTION_ALL = 3;    
    
    /**
     * Sets the UIBinding (a {@link __UIBinding} instance) which current end point belong to
     *
     * @param __UIBinding $ui_binding
     */
    public function setUIBinding(__UIBinding &$ui_binding);

    /**
     * Gets the UIBinding (a {@link __UIBinding} instance) which current end point belong to
     *
     */
    public function &getUIBinding();
    
    /**
     * Set a value representing the bound direction (client to server, server to client or both)
     *
     * @param integer $bound_direction
     */
    public function setBoundDirection($bound_direction);
    
    /**
     * Get a value representing the bound direction (server to client, client to server or both)
     *
     * @return integer
     * 
     */
    public function getBoundDirection();
    
}