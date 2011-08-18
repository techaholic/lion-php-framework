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
 * Represents a component's method as a server end-point
 * 
 * @see __IServerEndPoint, __IEndPoint, __UIBinding
 *
 */
class __ComponentMethod extends __ServerEndPoint {
    
    protected $_method = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C;
    /**
     * Constructor method
     *
     * @param __IComponent $component The component associated to this end-point
     * @param string $method The component method
     */
    public function __construct(__IComponent &$component, $method) {
        $this->setComponent($component);
        $this->setMethod($method);
    }
    
    public function receiveComponentNotification($accessor) {
        if($accessor == $this->_method) {
            $this->_ui_binding->synchronizeClient();
        }
    }
    
    /**
     * Sets the method
     *
     * @param string $method The method
     */
    public function setMethod($method) {
        $this->_method = $method;
    }
    
    /**
     * Gets the method
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }

    /**
     * Gets the bound direction allowed by this end-point
     *
     * @return integer
     */
    public function getBoundDirection() {
        return __IEndPoint::BIND_DIRECTION_S2C;
    }
    
}