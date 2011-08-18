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
 * Abstract implementation of server end-point
 *
 */
abstract class __ServerEndPoint implements __IServerEndPoint {
    
    protected $_ui_binding   = null;
    protected $_component_id = null;    
    protected $_view_code    = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_ALL;
    
    /**
     * Sets the component
     *
     * @param __IComponent $component The component
     */
    public function setComponent(__IComponent &$component) {
        $this->_component_id = $component->getId();
        $this->_view_code    = $component->getViewCode();
    }
    
    /**
     * Gets the component
     *
     * @return __IComponent The component
     */
    public function &getComponent() {
        return __ComponentPool::getInstance()->getComponent($this->_component_id);
    }    
    
    /**
     * Gets the view code associated to the component
     *
     * @return string
     */
    public function getViewCode() {
        return $this->_view_code;
    }    
    
    /**
     * Set the {@link __UIBinding} containing the current server end-point
     *
     * @param __UIBinding $ui_binding The {@link __UIBinding} container
     */
    public function setUIBinding(__UIBinding &$ui_binding) {
        $this->_ui_binding =& $ui_binding;
        $this->getComponent()->addBindingCode($ui_binding->getId());
    }

    /**
     * Gets the {@link __UIBinding} containing the current server end-point
     *
     * @return __UIBinding
     */
    public function &getUIBinding() {
        return $this->_ui_binding;
    }
    
    public function getBoundDirection() {
        return $this->_bound_direction;
    }
    
    public function setBoundDirection($bound_direction) {
        $this->_bound_direction = $bound_direction;
    }
    
}