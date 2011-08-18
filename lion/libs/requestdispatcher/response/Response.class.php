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
 * @package    Response
 * 
 */

abstract class __Response extends __ContentContainer implements __IResponse {
		
    protected $_view_codes = array();
    protected $_cacheable = true;
    protected $_processed_content = null;
    
    public function prepareToSleep() {
        if($this->_processed_content === null) {
            $this->_processed_content = $this->_doGetContent();
        }
    }
    
    public function getContent() {
        if($this->_processed_content !== null) {
            $return_value = $this->_processed_content;
            $this->_processed_content = null;
        }
        else {
            $return_value = $this->_doGetContent();
        }
        return $return_value;
    }
    
    protected function _doGetContent() {
        if(__Client::getInstance()->getRequestType() == REQUEST_TYPE_XMLHTTP ||
           $this == __FrontController::getInstance()->getResponse()) {
            __ResponseWriterManager::getInstance()->write($this);
            __ResponseWriterManager::getInstance()->clear();
        }
        $return_value = implode($this->_top_content) . 
                        implode($this->_content)     . 
                        implode($this->_bottom_content);
        return $return_value;
    }
    
    public function addViewCode($view_code, $cacheable = false) {
        $this->_view_codes[$view_code] = $cacheable;
        if($cacheable === false) {
            $this->setCacheable(false);
        }
    }
    
    public function getViewCodes() {
        return array_keys($this->_view_codes);
    }
    
    public function getCacheableViews() {
        $return_value = array();
        foreach($this->_view_codes as $view_code => $cacheable) {
            if($cacheable) {
                $return_value[] = $view_code;
            }
        }
        return $return_value;
    }
    
    public function setCacheable($cacheable) {
        $this->_cacheable = (bool) $cacheable;
    }
    
    public function isCacheable() {
        //anonymous users in non-debug mode are candidates to cache the response
        if(__AuthenticationManager::getInstance()->isAnonymous() &&
          !__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            $return_value = $this->_cacheable;
        }
        else {
            $return_value = false;
        }
        return $return_value;
    }

}