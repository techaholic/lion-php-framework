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
 * This class represents a pair of bound end-points, one representing a server end-point (implementing the {@link __IServerEndPoint}) and the other one representing a client end-point (implementing the {@link __IClientEndPoint}).
 * i.e., a component's property <i>text</i> bound to a HTML element property <i>innerHTML</i>
 *
 * @see __IEndPoint, __UIBindingManager
 * 
 */
final class __UIBinding { 
    
    private $_id = null;
    private $_server_end_point = null;
    private $_client_end_point = null;

    /**
     * Constructor method
     *
     * @param __IComponent &$component A reference to the component
     * @param string $property The property name
     */
    public function __construct(__IServerEndPoint $server_end_point, __IClientEndPoint $client_end_point) {
        $this->_id = uniqid();
        $this->setServerEndPoint($server_end_point);
        $this->setClientEndPoint($client_end_point);
        
    }
    
    /**
     * Gets an unique identifier for current instance
     *
     * @return string The id
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Sets a reference to the server end-point
     *
     * @param __IServerEndPoint $server_end_point The server end-point
     */
    public function setServerEndPoint(__IServerEndPoint $server_end_point) {
        $this->_server_end_point = $server_end_point;
        $this->_server_end_point->setUIBinding($this);
    }
    
    /**
     * Gets a reference to the server end-point
     *
     * @return __IServerEndPoint
     */
    public function &getServerEndPoint() {
        return $this->_server_end_point;
    }
    
    /**
     * Sets a reference to the client end-point
     *
     * @param __IClientEndPoint $client_end_point The client end-point
     */
    public function setClientEndPoint(__IClientEndPoint $client_end_point) {
        $this->_client_end_point = $client_end_point;
        $this->_client_end_point->setUIBinding($this);
    }
    
    /**
     * Gets a reference to the client end-point
     *
     * @return __IClientEndPoint
     */
    public function &getClientEndPoint() {
        return $this->_client_end_point;
    }
    
    /**
     * Checks if server and client end-points contains different values
     *
     * @return bool
     */
    public function isDirty() {
        $return_value = false;
        if($this->_server_end_point->getValue() !== $this->_client_end_point->getValue()) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Updates the server end-point with the current client end-point value (if applicable)
     *
     */
    public function synchronizeServer() {
        //if both end-points have the BIND_DIRECTION_C2S or equivalent:
        if(($this->_server_end_point->getBoundDirection() & 
           $this->_client_end_point->getBoundDirection() & 
           __IEndPoint::BIND_DIRECTION_C2S) == __IEndPoint::BIND_DIRECTION_C2S ) {
             $this->_server_end_point->synchronize($this->_client_end_point);
//            if($this->_server_end_point->synchronize($this->_client_end_point)) {
//                $view_code = $this->_server_end_point->getComponent()->getViewCode();
//                __ComponentHandlerManager::getInstance()->getComponentHandler($view_code)->setDirty(true);
//            }
        }
    }
    
    /**
     * Updates the client end-point with the current server end-point value (if applicable)
     *
     */
    public function synchronizeClient() {
        //if both end-points have the BIND_DIRECTION_S2C or equivalent:
        if(($this->_server_end_point->getBoundDirection() & 
           $this->_client_end_point->getBoundDirection() & 
           __IEndPoint::BIND_DIRECTION_S2C) == __IEndPoint::BIND_DIRECTION_S2C ) {
            $this->_client_end_point->synchronize($this->_server_end_point);
        }
    }

    
}