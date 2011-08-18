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
 * This is the section handler in charge of processing &lt;routes-section&gt; configuration sections
 *
 */
class __RoutesSectionHandler extends __CacheSectionHandler {

    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array();
        $subsections = $section->getSections();
        foreach($subsections as &$subsection) {
            if($subsection->getName() == 'route') {
                $route = $this->_createRoute($subsection);
                $return_value[$route->getId()] =& $route;
                unset($route);
            }
        }
        return $return_value;
    }
    
    protected function &_createRoute(__ConfigurationSection &$section) {
        $route = new __Route();
        $route->setId($section->getAttribute('id'));
        if($section->hasAttribute('supercache')) {
            $route->setSuperCache(__ConfigurationValueResolver::toBool($section->getAttribute('supercache')));
        }
        else if($section->hasAttribute('cache')) {
            $route->setCache(__ConfigurationValueResolver::toBool($section->getAttribute('cache')));
        }
        if($section->hasAttribute('cache-ttl')) {
            $cache_ttl = $section->getAttribute('cache-ttl');
            if(is_numeric($cache_ttl)) {
                $cache_ttl = (int) $cache_ttl;
            }
            $route->setCacheTtl($cache_ttl);
        }
        if($section->hasAttribute('redirect-to')) {
            $route_to_redirect_to = $section->getAttribute('redirect-to');
            if($section->hasAttribute('redirection-code')) {
                $redirection_code = $section->getAttribute('redirection-code'); 
            }
            else {
                $redirection_code = 302;
            }
            $route->setRouteToRedirectTo($route_to_redirect_to, $redirection_code);
            
        }
        
        $url_regular_expression = $section->getAttribute('uri-pattern');
        $route->setUrlPattern($url_regular_expression);
        $var_patterns = array();
        preg_match_all('/\$[_A-Za-z][_A-Za-z0-9]*/', $url_regular_expression, $var_order);
        foreach($var_order[0] as $varpattern_name) {
            $varpattern_section = $section->getSection('variable', null, array('name' => $varpattern_name));
            if($varpattern_section != null) {
                $varpattern = $varpattern_section->getAttribute('var-pattern');
                $var_patterns[$varpattern_name] = $varpattern;
            }
        }
        $route->setVariablePatterns($var_patterns);
        $sub_sections = $section->getSections();
        foreach($sub_sections as &$sub_section) {
            switch($sub_section->getName()) {
                case 'parameter':
                    $route->addFixedParameter($sub_section->getAttribute('name'), $sub_section->getAttribute('value'));
                    break;
                case 'if-isset':
                    $variable = $sub_section->getAttribute('variable');
                    $parameter_sections = $sub_section->getSections();
                    $parameters = array();
                    foreach($parameter_sections as $parameter_section) {
                        $parameters[$parameter_section->getAttribute('name')] = $parameter_section->getAttribute('value');
                    }
                    $route->addIfIssetCondition($variable, $parameters);
                    break;
                case 'if-equals':
                    $variable = $sub_section->getAttribute('variable');
                    $value = $sub_section->getAttribute('value');
                    $parameter_sections = $sub_section->getSections();
                    $parameters = array();
                    foreach($parameter_sections as $parameter_section) {
                        $parameters[$parameter_section->getAttribute('name')] = $parameter_section->getAttribute('value');
                    }
                    $route->addIfEqualsCondition($variable, $value, $parameters);
                    break;
                case 'front-controller':
                    $route->setFrontControllerClass($sub_section->getAttribute('class'));
                    break;
                case 'flow':
                    $route->setFlowId($sub_section->getAttribute('id'));
                    break;
                case 'action':
                    $action_identity = new __ActionIdentity();
                    if($sub_section->hasAttribute('controller')) {
                        $action_identity->setControllerCode($sub_section->getAttribute('controller'));
                    }
                    if($sub_section->hasAttribute('code')) {
                        $action_identity->setActionCode($sub_section->getAttribute('code'));
                    }
                    else if($sub_section->hasAttribute('action')) {
                        $action_identity->setActionCode($sub_section->getAttribute('action'));
                    }
                    $route->setActionIdentity($action_identity);
                    break;
            }
        }
        //resolves route components before caching to avoid repeating this calculation on each request
        $route->resolveRouteComponents();
        return $route;
    }
    
}