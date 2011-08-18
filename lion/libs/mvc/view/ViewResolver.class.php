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
 * @package    View
 * 
 */

/**
 * This is the class in charge of resolve what's the __View that correspondes with each {@link __ModelAndView} instance.
 *
 */
class __ViewResolver  {

    private static $_instance = null;

    private $_dirty = false;

    private $_view_definitions = array();
    
    private $_views = array();

    private function __construct() {
        $view_definitions = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('view-definitions');
        if(is_array($view_definitions)) {
            $this->_view_definitions =& $view_definitions;
        }
    }

    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ViewResolver();
        }
        return self::$_instance;
    }

    /**
     * This method returns an {@link __View} child instance that correspond with a specified view code.
     *
     * @param string $view_code An view code
     * @return __View An {@link __View} child instance that correspond with the specified view code
     * 
     * @throws __SecurityException If no view is found for the specified view code, a __SecurityException will be raised.
     * 
     */
    public function &getView($view_code)
    {
        $view_code_search_key = strtoupper(trim($view_code));
                    
        if(key_exists($view_code_search_key, $this->_views)) {
            $return_value = clone($this->_views[$view_code_search_key]);
        }
        else {
            $return_value = $this->_createView($view_code);
            if(!$return_value instanceof __IView) {
                throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_VIEW', array($view_code_search_key));
            }
            $this->_views[$view_code_search_key] = $return_value;
        }
        return $return_value;
    }
    
    /**
	 * This is a factory method for creating instances implementing the {@link __IView}
	 *
	 * @param string $view_code The view code
	 * @return __IView an instance of a class implementing the {@link __IView}
	 */
    private function _createView($view_code) {
        $return_value = null;
        $path = null;
        if(!empty($view_code)) {
            $view_code_search_key = strtoupper(trim($view_code));            
            //check static rules:
            if(key_exists($view_code_search_key, $this->_view_definitions['static_rules'])) {
                try {
                    $view_definition = $this->_view_definitions['static_rules'][$view_code_search_key];
                    $return_value    = $view_definition->getView();
                }
                catch (Exception $e) {
                    throw __ExceptionFactory::getInstance()->createException('ERR_VIEW_NON_LOADABLE', array($view_code_search_key, $e->getMessage()));
                }
            }
            //check dynamic rules:
            else {
                foreach($this->_view_definitions['dynamic_rules'] as $view_definition) {
                    if( $return_value == null && $view_definition->isValidForViewCode($view_code_search_key)) {
                        $return_value = $view_definition->getView($view_code);
                    }
                }
            }
        }
        if($return_value == null) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_VIEW', array($view_code_search_key));
        }
        else if(!$return_value instanceof __IView) {
            throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_VIEW_CLASS', array(get_class($view)));
        }
        else {
            $return_value->setCode($view_code);
        }
        return $return_value;
    }


}