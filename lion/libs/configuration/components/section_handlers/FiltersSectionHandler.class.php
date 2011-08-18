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
 * @package    Configuration
 * 
 */

/**
 * This is the section handler in charge of processing &lt;filters&gt; configuration sections
 *
 */
class __FiltersSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        //initialize the return value with all the filter chains for all the available routes:
        $return_value = array();
        $filter_sections = $section->getSections();
        foreach($filter_sections as &$filter_section) {
            $filter_name = $filter_section->getAttribute('name');
            $filter_class = $filter_section->getAttribute('class');
            $filter = new $filter_class();
            if($filter_section->hasAttribute('order')) {
                $filter->setOrder($filter_section->getAttribute('order'));
            }
            if($filter_section->hasAttribute('execute-before-cache')) {
                $filter->setExecuteBeforeCache(__ConfigurationValueResolver::toBool($filter_section->getAttribute('execute-before-cache')));
            }
            if($filter instanceof __IFilter) {
                $routes_to_apply_to = $filter_section->getSection('apply-to');
                if($routes_to_apply_to != null) {
                    $routes_to_apply_to_sections = $routes_to_apply_to->getSections();
                    foreach($routes_to_apply_to_sections as $route_section) {
                        switch(strtoupper($route_section->getName())) {
                            case 'ALL-ROUTES':
                                if(!key_exists('*', $return_value)) {
                                    $return_value['*'] = array();
                                }
                                $return_value['*'][] =& $filter;
                                break;
                            case 'ROUTE':
                                $route_id = $route_section->getAttribute('id');
                                if(!key_exists($route_id, $return_value)) {
                                    $return_value[$route_id] = new __FilterChain();
                                }
                                $filter_chain =& $return_value[$route_id];
                                $filter_chain->addFilter($filter);
                                unset($filter_chain);
                                break;
                        }
                    }
                    unset($routes_to_apply_to_sections);
                }
                unset($routes_to_apply_to);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Unexpected class ' . get_class($filter) . '. A class implementing __IFilter was expected.');
            }
            unset($filter);
        }
        return $return_value;
    }    
    
}