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
 * @package    Filter
 * 
 */

abstract class __Filter implements __IFilter {
    
    protected $_order = null;
    protected $_execute_before_cache = false;
    
    public function execute(__IRequest &$request, __IResponse &$response, __FilterChain &$filter_chain) {
        
        $this->preFilter($request, $response);
        
        $filter_chain->execute($request, $response);
        
        $this->postFilter($request, $response);
        
    }
        
    public function preFilter(__IRequest &$request, __IResponse &$response) {}

    public function postFilter(__IRequest &$request, __IResponse &$response) {}

    public function setOrder($order) {
        $this->_order = $order;
    }
    
    public function getOrder() {
        return $this->_order;
    }
    
    public function setExecuteBeforeCache($execute_before_cache) {
        $this->_execute_before_cache = (bool) $execute_before_cache;
    }
    
    public function getExecuteBeforeCache() {
        return $this->_execute_before_cache;
    }
    
}