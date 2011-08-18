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

class __FilterChain {

    /**
     * Enter description here...
     *
     * @var __Collection
     */
    protected $_filters = null;
    
    /**
     * Enter description here...
     *
     * @var __Iterator
     */
    protected $_filters_iterator = null;
    
    protected $_front_controller_callback = null;
    
    public function __construct() {
        $this->_filters = new __Collection();
        $this->reset();
    }
    
    public function reset() {
        $this->_filters_iterator = $this->_filters->getIterator();
        $this->_filters_iterator->rewind();
    }
    
    public function addFilter(__Filter &$filter) {
        $this->_filters->add($filter);
        $this->_filters->sort(array($this, 'cmp'));
    }
    
    public function cmp($a, $b)
    {
        $return_value = 0; //by default
        if($a->getExecuteBeforeCache() == $b->getExecuteBeforeCache()) {
            if($a->getOrder() === $b->getOrder()) {
                $return_value = 0;
            }
            else if($a->getOrder() === null) {
                $return_value = 1;
            }
            else if($b->getOrder() === null) {
                $return_value = -1;
            }
            else if($a->getOrder() < $b->getOrder()) {
                $return_value = -1;
            }
            else {
                $return_value = 1;
            }
        }
        else if($a->getExecuteBeforeCache()) {
            $return_value = -1;
        }
        else {
            $return_value = 1;
        }
        return $return_value;
    }    
    
    public function setFrontControllerCallback(&$front_controller, $method_name) {
        if( $front_controller instanceof __IFrontController ) {
            $this->_front_controller_callback = new __Callback($front_controller, $method_name);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_FRONT_CONTROLLER_INSTANCE', array(get_class($front_controller)));
        }
    }
    
    public function execute(__IRequest &$request, __IResponse &$response) {
        if(!$this->_filters_iterator->isDone()) {
            //get the current filter:
            $current_filter = $this->_filters_iterator->currentItem();
            //move the iterator a position for next call to execute:
            $this->_filters_iterator->next();
            if($current_filter instanceof __IFilter) {
                $current_filter->execute($request, $response, $this);
            }
        }
        else if($this->_front_controller_callback != null) {
            $parameters = array(&$request, &$response);
            $this->_front_controller_callback->execute($parameters);
        }
    }
    
}