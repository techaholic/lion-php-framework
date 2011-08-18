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


class __SmartyView extends __TemplateEngineView {

    private $_smarty;
	
    public function __construct() {
    	$this->_smarty = new Smarty();
        //Will also register filters executor methods:
    	$this->_smarty->registerFilter('pre', array(&$this, 'executePreFilters'));
    	$this->_smarty->registerFilter('post', array(&$this, 'executePostFilters'));
    	$this->_smarty->registerFilter('output', array(&$this, 'executeOutputFilters'));
    }
    
    public function getComponentParserClass() {
        return '__SmartyComponentParser';
    }
    
    public function setCaching($caching) {
        $caching = (bool) $caching;
        $this->_smarty->caching = $caching;
    }
    
    public function setCompileDir($compile_dir) {
    	$this->_smarty->compile_dir = $compile_dir;
    }

    public function assign($key, $value) {
    	$this->_smarty->assign($key, $value);
    }
        
    public function assignByRef($key, $value) {
    	$this->_smarty->assign_by_ref($key, $value);
    }
    
    public function isAssigned($key) {
    	$return_value = false;
    	$assignement = $this->_smarty->get_template_vars($key);
    	if($assignement !== null) {
    		$return_value = true;
    	}
    	return $return_value;
    }

    public function getAssignedVar($key) {
        $return_value = $this->_smarty->get_template_vars($key);
        return $return_value;        
    }
    
    protected function templatize($template_file) {
        $this->_smarty->assign('__view_code__', $this->getCode());
        return $this->_smarty->fetch($template_file);
    }

    protected function registerFunction($name, $value) {
    	$this->_smarty->register_function($name, $value);
    }
    
    protected function setCompileCheck($flag) {
    	$this->_smarty->compile_check = $flag;
    }
}
