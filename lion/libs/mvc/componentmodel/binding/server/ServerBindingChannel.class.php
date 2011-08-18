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

class __ServerBindingChannel {
    
    private $_binding_codes = array();
    static private $_instance = null;
    
    private $_ui_bindings = array();
    
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__ServerBindingChannel::_binding_codes')) {
            $this->_binding_codes =& $session->getData('__ServerBindingChannel::_binding_codes');
        }
        else {
            $session->setData('__ServerBindingChannel::_binding_codes', $this->_binding_codes);
        }
    }
    
    /**
     * Gets a {@link __ServerBindingChannel} singleton instance
     *
     * @return __ServerBindingChannel
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ServerBindingChannel();
        }
        return self::$_instance;
    }
    
    public function addServerEndPoint(__IServerEndPoint $server_end_point) {
        $component_id = $server_end_point->getComponent()->getId();
        if(key_exists($component_id, $this->_binding_codes)) {
            $this->_binding_codes[$component_id] = array();
        }
        $server_end_point->getUIBinding;
    }
    
    
    
    
    
}