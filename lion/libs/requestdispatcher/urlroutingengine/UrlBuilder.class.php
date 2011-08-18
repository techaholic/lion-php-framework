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


class __UrlBuilder {

    private $_url_pattern = null;
    private $_variable_patterns = array();
    private $_parameters        = array();
    private $_variable_maps     = array();
    private $_parameter_rules   = array();
    private $_reserved_variable_values = array();
    private $_fixed_parameters  = array();
    private $_action_code_variable = null;
    private $_controller_code_variable = null;  
    
    public function __clone() {
        $this->_url_pattern = clone $this->_url_pattern;
    }
    
    public function setUrlPattern($url_pattern) {
        $this->_url_pattern = $this->_createExpressionTemplate($url_pattern);
    }

    public function setVariableForActionCode($variable_name) {
        $this->_action_code_variable = $variable_name;
    }

    public function setVariableForControllerCode($variable_name) {
        $this->_controller_code_variable = $variable_name;
    }
    
    public function setActionIdentity(__ActionIdentity $action_identity) {
        //if the controller code is dynamic, meaning that it's a variable:
        if($this->_controller_code_variable != null) {
            $this->setControllerCode($action_identity->getControllerCode());
        }
        //if the action code is dynamic, meaning that it's a variable:
        if($this->_action_code_variable != null) {
            $this->setActionCode($action_identity->getActionCode());
        }
    }
    
    public function setActionCode($action_code) {
        if( $this->_action_code_variable != null ) {
            $this->_url_pattern->setVariableValue($this->_action_code_variable, $action_code);
        }
        else {
            $request_action_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_ACTION_CODE');
            $this->addParameter($request_action_code, $action_code);
        }
    }

    public function setControllerCode($controller_code) {
        if( $this->_controller_code_variable != null ) {
            $this->_url_pattern->setVariableValue($this->_controller_code_variable, $controller_code);
        }
        else {
            $request_controller_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_CONTROLLER_CODE');
            $this->addParameter($request_controller_code, $controller_code);
        }
    }
    
    public function addFixedParameter($parameter_name) {
        $this->_fixed_parameters[$parameter_name] = true;
    }
    
    public function addVariablePattern($variable_name, $variable_pattern) {
        $this->_variable_patterns[$variable_name] = $variable_pattern;
    }
    
    public function addVariable($parameter_name, $variable_name) {
        if(!key_exists($parameter_name, $this->_variable_maps)) {
            $this->_variable_maps[$parameter_name] = array();
        }
        $this->_variable_maps[$parameter_name][] = $variable_name;
    }
    
    public function addVariableIfParameterEquals($parameter_name, $parameter_value, $variable_name, $variable_value = null) {
        if(!key_exists($parameter_name, $this->_parameter_rules)) {
            $this->_parameter_rules[$parameter_name] = array();
        }
        if(!key_exists($parameter_value, $this->_parameter_rules[$parameter_name])) {
            $this->_parameter_rules[$parameter_name][$parameter_value] = array();
        }
        $variable_array = array('variable_name' => $variable_name);
        if($variable_value !== null) {
            $variable_array['variable_value'] = $variable_value;
        }
        $this->_parameter_rules[$parameter_name][$parameter_value][] = $variable_array;
        if(!key_exists($variable_name, $this->_reserved_variable_values)) {
            $this->_reserved_variable_values[$variable_name] = array();
        }
        $this->_reserved_variable_values[$variable_name][$variable_value] = true;
    }
    
    public function getUrl() {
        //Set default values for non-defined variables:
        foreach($this->_variable_patterns as $variable_name => $variable_pattern) {
            if($this->_url_pattern->getVariableValue($variable_name) === null && !$this->_url_pattern->isOptionalVariable($variable_name)) {
                $this->_url_pattern->setVariableDefaultValue($variable_name, $this->_getValidValueForVariable($variable_name));
            }
        }
        //Get the inverse expression (this will be the url without parameters)
        $return_value = $this->_url_pattern->getREInverse();
        if(defined("APP_URL_PATH")) {
            $url_path = APP_URL_PATH;
        }
        else {
            $url_path = __ApplicationContext::getInstance()->getPropertyContent('APP_URL_PATH');
        }
        $return_value = __UrlHelper::resolveUrl($return_value, $url_path);
        //Add parameters to the url:
        if(count($this->_parameters) > 0) {
            $return_value .= '?' . http_build_query($this->_parameters);
        }
        return $return_value;
    }
    
    /**
     * This method adds parameters associated to the current building url.
     * 
     * It analyzes each one in order to know how to represent in the url building.
     * Note that some parameters are converted to url variables and embedded as specified
     * in the url regular expression. Other need to be present as it in the url.
     *
     * @param string $parameter_name The name of the parameter
     * @param string $parameter_value The value of the parameter
     */
    public function addParameter($parameter_name, $parameter_value) {
        if($parameter_value !== null && !key_exists($parameter_name, $this->_fixed_parameters)) {
            $defined = false;
            //if there is a direct correspondence between the parameter and some variables,
            //then set all related variables with the parameter value in the url:
            if(key_exists($parameter_name, $this->_variable_maps)) {
                foreach($this->_variable_maps[$parameter_name] as $variable_name) {
                    $this->_url_pattern->setVariableValue($variable_name, $parameter_value);
                }
                $defined = true;
            }
            else if(key_exists($parameter_name, $this->_parameter_rules)) {
                $parameter_rules = $this->_parameter_rules[$parameter_name];
                //if rules exist for current parameter with current value, will apply it:
                if(@key_exists($parameter_value, $parameter_rules)) {
                    foreach($parameter_rules[$parameter_value] as $parameter_rule) {
                        $variable_name  = $parameter_rule['variable_name'];
                        $variable_value = key_exists('variable_value', $parameter_rule) ? $parameter_rule['variable_value'] : $parameter_value;
                        $this->_url_pattern->setVariableValue($variable_name, $variable_value);
                    }
                    $defined = true;
                }
                //otherwise, let's search for rules associated to variables without value restrictions:
                else {
                    foreach($parameter_rules as $value_as_variable => $candidate_parameter_rules) {
                        if(preg_match('/^\$.+$/', $value_as_variable)) {
                            foreach($candidate_parameter_rules as $parameter_rule) {
                                if(!key_exists('variable_value', $parameter_rule)) {
                                    $variable_name  = $parameter_rule['variable_name'];
                                    $variable_value = $parameter_value;
                                    $this->_url_pattern->setVariableValue($variable_name, $variable_value);
                                    $defined = true;                                    
                                }
                            }
                        }
                    }
                }
            }
            
            //If the parameter doesn't correspond with any variable value, will set as it in the parameters list:
            if(!$defined) {
                $this->_parameters[$parameter_name] = $parameter_value;
            }
        }
    }
    
    public function _getValidValueForVariable($variable_name) {
        $return_value = "1"; //by default if no patterns has been defined
        if(key_exists($variable_name, $this->_variable_patterns)) {
            $pattern_builder = $this->_createExpressionTemplate($this->_variable_patterns[$variable_name]);
            $defined = false;
            while($defined == false) {
                $variable_value = $pattern_builder->getREInverse();
                if(key_exists($variable_name,  $this->_reserved_variable_values) 
                && key_exists($variable_value, $this->_reserved_variable_values[$variable_name])) {
                    $defined = false;
                }
                else {
                    $defined = true;
                }
            }
            $return_value = $variable_value;
        }
        return $return_value;
    }

    /**
     * This function is cached since the number of string pattern that it will recive is limited.
     *
     * @param string $string_pattern The string pattern
     * @return __ExpressionTemplate
     */
    private function _createExpressionTemplate($string_pattern) {
        $return_value = __ApplicationContext::getInstance()->getCache()->getData($string_pattern);
        if($return_value == null) {
            $return_value = new __ExpressionTemplate();
            try {
                $lex    = new __REInverseLexer($string_pattern);
                $parser = new __REInverseParser($lex);
                while ($lex->yylex()) {
                    $parser->doParse($lex->token, $lex->value);
                }
                $parser->doParse(0, 0);
                $return_value = $parser->getResult();
            }
            catch (Exception $e) {
                throw new __CoreException($e->getMessage());
            }
            __ApplicationContext::getInstance()->getCache()->setData($string_pattern, $return_value);
        }
        return $return_value;
    }
    
}
