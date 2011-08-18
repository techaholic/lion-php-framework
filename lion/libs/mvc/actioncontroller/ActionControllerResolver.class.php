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
 * @package    ActionController
 * 
 */


class __ActionControllerResolver {

    private static $_instance = null;

    private $_controller_definitions = array();

    private function __construct() {
        $controller_definitions = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('controller-definitions');
        if(is_array($controller_definitions)) {
            $this->_controller_definitions = $controller_definitions;
        }
    }

    /**
     * This method return a singleton instance of __ActionFactory instance
     *
     * @return __ActionFactory a reference to the current __ActionFactory instance
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ActionControllerResolver();
        }
        return self::$_instance;
    }
    
    public function getActionController($action_controller_code) {
        $return_value = null;
        $controller_definition = $this->getActionControllerDefinition($action_controller_code);
        if($controller_definition instanceof __ActionControllerDefinition) {
            $return_value = $controller_definition->getActionController($action_controller_code);
        }
        if($return_value == null) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_CONTROLLER', array($action_controller_code));
        }
        return $return_value;
    }
    
    public function &getActionControllerDefinition($action_controller_code) {
        $return_value = null;
        if(!empty($action_controller_code)) {
            $action_controller_code = strtoupper(trim($action_controller_code));
            if(key_exists($action_controller_code, $this->_controller_definitions['static_rules'])) {
                $return_value = $this->_controller_definitions['static_rules'][$action_controller_code];
            }
            //check dynamic rules:
            else {
                foreach($this->_controller_definitions['dynamic_rules'] as &$controller_definition) {
                    if( $controller_definition->isValidForControllerCode($action_controller_code)) {
                        $return_value = $controller_definition;
                    }
                }
            }
        }
        return $return_value;
    }

}