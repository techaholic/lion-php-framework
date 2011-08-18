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

class __WebflowExpressionHelper {
    
    static public function resolveExpression($expression) {
        $expression = trim($expression);
        $matched_subexpression = array();
        if(preg_match('/^\{\$([^\}]+)\}$/', $expression, $matched_subexpression)) {
            $subexpression = trim($matched_subexpression[1]);
            $matched_subexpression_components = array();
            if(preg_match('/^([^\.]+)\.(.+)$/', $subexpression, $matched_subexpression_components)) {
                $scope_name = $matched_subexpression_components[1];
                $attribute = $matched_subexpression_components[2];
                $return_value = new __FlowAttribute();
                $scope = self::resolveScope($scope_name);
                $return_value->setScope($scope);
                $return_value->setAttribute($attribute);
            }
        }
        else {
            $return_value = $expression;
        }
        return $return_value;
    }
    
    static public function resolveScope($scope_name) {
        $return_value = null;
        switch(strtoupper($scope_name)) {
            case 'FLOW':
            case 'FLOWSCOPE':
                $return_value = __FlowDefinition::SCOPE_FLOW;
                break;
            case 'REQUEST':
            case 'REQUESTSCOPE':
                $return_value = __FlowDefinition::SCOPE_REQUEST;
                break;
            case 'SESSION':
            case 'SESSIONSCOPE':
                $return_value = __FlowDefinition::SCOPE_SESSION;
                break;
            default:
                throw new __ConfigurationException('Unknown scope ' . $scope_name);
                break;
        }
        return $return_value;
    }
    
   
}