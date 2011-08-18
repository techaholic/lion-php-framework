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
 * @package    WebFlow
 * 
 */

class __WebFlowManager {

    static public $_instance = null;
    
    protected $_flow_definitions = array();
    
    private function __construct() {
        $session = __ApplicationContext::getInstance()->getSession();
        $session_flows = '__WebFlowManager::' . __CurrentContext::getInstance()->getContextId() . '::_flows';
        if($session->hasData($session_flows)) {
            $this->_flow_definitions = $session->getData($session_flows);
        }
        else {
            $this->_flow_definitions = __CurrentContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('webflow');
            $session->setData($session_flows, $this->_flow_definitions);
        }
    }
    
    /**
     * 
     * @return __WebFlowManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __WebFlowManager(); 
        }
        return self::$_instance;
    }
    
    public function hasFlowDefinition($flow_definition_id) {
        $return_value = false;
        if(key_exists($flow_definition_id, $this->_flow_definitions)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function getFlowDefinition($flow_definition_id) {
        $return_value = null;
        if(key_exists($flow_definition_id, $this->_flow_definitions)) {
            $return_value = $this->_flow_definitions[$flow_definition_id];
        }
        return $return_value;
    }
    
    
    
}
