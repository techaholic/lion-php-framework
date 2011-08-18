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

class __UrlBuilderFactory {
    
    /**
     * Creates and returns an __UrlBuilder instance for an specific route
     * 
     * @param __Route $route The route to use to create the __UrlBuilder instance
     * @return __UrlBuilder The __UrlBuilder instance
     * 
     * @see __UrlBuilder
     * 
     */
    static public function createUrlBuilder(__Route $route) {
        //create the UrlBuilder
        $return_value  = new __UrlBuilder();
        //and populate it according to the selected route:
        $return_value->setUrlPattern($route->getUrlPattern());

        $var_patterns = $route->getVariablePatterns();
        foreach($var_patterns as $var_name => $var_pattern) {
            $return_value->addVariablePattern($var_name, $var_pattern);
        }
        
        $parameters = $route->getFixedParameters();
        foreach($parameters as $parameter_name => $parameter_value) {
            if(preg_match('/^\$(.+)$/', trim($parameter_value))) {
                $variable_name = trim($parameter_value);
                $return_value->addVariable($parameter_name, $variable_name);
            }
        }

        $if_isset_conditions = $route->getIfIssetConditions();
        foreach($if_isset_conditions as $variable_name => $parameters) {
            foreach($parameters as $parameter_name => $parameter_value) {
                //If the parameter has a value, will set a variable instead of it:
                $return_value->addVariableIfParameterEquals($parameter_name, $parameter_value, $variable_name);
            }
        }
        $if_equals_conditions = $route->getIfEqualsConditions();
        foreach($if_equals_conditions as $variable_name => $variable_values) {
            foreach($variable_values as $variable_value => $parameters) {
                foreach($parameters as $parameter_name => $parameter_value) {
                    $return_value->addVariableIfParameterEquals($parameter_name, $parameter_value, $variable_name, $variable_value);
                }
            }
        }

        $action_identity = $route->getActionIdentity();
        if($action_identity != null) {
            if($action_identity->getActionCode() != null && preg_match('/^\$(.+)$/', $action_identity->getActionCode())) {
                $return_value->setVariableForActionCode($action_identity->getActionCode());
            }
            if(preg_match('/^\$(.+)$/', $action_identity->getControllerCode())) {
                $return_value->setVariableForControllerCode($action_identity->getControllerCode());
            }
        }
        return $return_value;
    }    
    
}