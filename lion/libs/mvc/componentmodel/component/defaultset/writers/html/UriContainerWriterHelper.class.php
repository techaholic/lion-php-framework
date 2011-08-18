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

class __UriContainerWriterHelper {
    
    static public function resolveUrl(__IUriContainer &$component) {
        $return_value = $component->getUrl();
        if($return_value == null) {
            $action_identity = new __ActionIdentity($component->getController(), $component->getAction());
            $route_id = $component->getRoute();
            $parameters = array();
            $parameter_list = $component->getParameters();
            if(is_string($parameter_list)) {
                $parameter_list = preg_split('/,/', $parameter_list);
                foreach($parameter_list as $parameter) {
                    $parameter = preg_split('/\s*\=\s*/', $parameter);
                    if(count($parameter) == 2) {
                        $parameters[self::_parseValue($parameter[0])] = self::_parseValue($parameter[1]);
                    }
                }
            }
            else if(is_array($parameter_list)) {
                $parameters = $parameter_list;
            }
            $uri = __UriFactory::getInstance()->createUri()
                                              ->setActionIdentity($action_identity)
                                              ->setParameters($parameters);
            if(!empty($route_id)) {
                $uri->setRouteId($route_id);
            }
            if($component->getUseAbsoluteUrl()) {
                $return_value = $uri->getAbsoluteUrl();
            }
            else {
                $return_value = $uri->getUrl();
            }            
            $component->setUrl($return_value);                                                   
        }
        return $return_value;        
    }
    
    static public function resolveUri(__IUriContainer &$component) {
        $return_value = null;
        if($return_value == null) {
            $action_identity = new __ActionIdentity($component->getController(), $component->getAction());
            $route_id = $component->getRoute();
            $parameters = array();
            $parameter_list = $component->getParameters();
            if(is_string($parameter_list)) {
                $parameter_list = preg_split('/,/', $parameter_list);
                foreach($parameter_list as $parameter) {
                    $parameter = preg_split('/\s*\=\s*/', $parameter);
                    if(count($parameter) == 2) {
                        $parameters[self::_parseValue($parameter[0])] = self::_parseValue($parameter[1]);
                    }
                }
            }
            else if(is_array($parameter_list)) {
                $parameters = $parameter_list;
            }
            $uri = __UriFactory::getInstance()->createUri()
                                              ->setActionIdentity($action_identity)
                                              ->setParameters($parameters);
            if(!empty($route_id)) {
                $uri->setRouteId($route_id);
            }
            $return_value = $uri;
        }
        return $return_value;        
    }    
    
    static private function _parseValue($value) {
        $return_value = trim($value);
        if(strpos($return_value, 'const:') === 0) {
            $constant_name = trim(substr($return_value, 6));
            if(defined($constant_name)) {
                $return_value = constant($constant_name);
            }
            else {
                $return_value = $constant_name;
            }
        }
        if(strpos($return_value, 'prop:') === 0) {
            $return_value = __ApplicationContext::getInstance()->getPropertyContent(trim(substr($return_value, 5)));
        }
        return $return_value;
    }
    
    
}