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
 * @package    UrlRoutingEngine
 * 
 */


class __RouteManager {

    private static $_instance = null;
    private $_routes = array();

    private function __construct() {
        $this->startup();
    }
        
    public function startup() {
        $cache = __CurrentContext::getInstance()->getCache();
        $cache_routes_key = '__RouteManager::' . __CurrentContext::getInstance()->getContextId() . '::_routes';
        $routes = $cache->getData($cache_routes_key);
        if($routes != null) {
            $this->_routes = $routes;
        }
        else {
            $routes  = __ApplicationContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('routes');
            $filters = __ApplicationContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('filters');
            if(is_array($routes)) {
                $this->_routes =& $routes;
                if(is_array($filters)) {
                    foreach($filters as $route_id => $filter_chain) {
                        if($route_id != '*') {
                            if(key_exists($route_id, $this->_routes)) {
                                $route =& $this->_routes[$route_id];
                                $route->setFilterChain($filter_chain);
                                unset($route);
                            }
                            else {
                                throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROUTE_ID', array($route_id));
                            }
                        }
                        unset($filter_chain);
                    }
                    if(key_exists('*', $filters)) {
                        $global_filters =& $filters['*'];
                        foreach($global_filters as &$global_filter) {
                            foreach($this->_routes as &$route) {
                                if(!$route->hasFilterChain()) {
                                    $filter_chain = new __FilterChain();
                                    $route->setFilterChain($filter_chain);
                                    unset($filter_chain);
                                }
                                $route->getFilterChain()->addFilter($global_filter);
                                unset($route);
                            }
                            unset($global_filter);
                        }
                        unset($global_filters);
                    }
                }
                $cache->setData($cache_routes_key, $this->_routes);
            }
        }
    }
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __RouteManager();
        }
        return self::$_instance;
    }
    
    public function addRoute(__Route &$route) {
        $this->_routes[$route->getId()] =& $route;
    }
    
    public function hasRoute($route_id) {
        return key_exists($route_id, $this->_routes);
    }
    
    public function &getRoute($route_id) {
        $return_value = null;
        if(key_exists($route_id, $this->_routes)) {
            $return_value =& $this->_routes[$route_id];
        }
        return $return_value;
    }
    
    public function &getRoutes() {
        return $this->_routes;
    }

    public function &getValidRouteForUrl($url) {
        foreach($this->_routes as &$route) {
            if($route->isValidForUrl($url)) {
                return $route;
            }
        }
        return null;
    }   
    
}

