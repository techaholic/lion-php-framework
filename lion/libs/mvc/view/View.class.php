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

/**
 * Abstract out of the box class implementing the __IView interface.
 * All out of the box __IView implementation are classes subclassing this one.
 *
 */
abstract class __View implements __IView {
                                          
    /**
     * This variable stores the code for current view<br>
     * This code idenfity the view from the rest of views, and normally it's the View derived class name.
     *
     * @var string
     */
    protected $_code = null;   
    
    protected $_event_handler_class = null;
        
    /**
     * This method sets a code for current view
     *
     * @param string A code for current view
     */
    public function setCode($code) {
        if(is_string($code) && !empty($code)) {
            $this->_code = $code;
        }
    }    
    
    /**
     * This method gets the code associated to the current view. If it's not setted, it will taked from the name of the class instead of
     *
     * @return string The code associated to the current view
     */
    public function getCode() {
        $return_value = $this->_code;
        return $return_value;
    }    

    final public function setEventHandlerClass($event_handler_class) {
        $this->_event_handler_class = $event_handler_class;
    }
    
    final public function getEventHandlerClass() {
        if($this->_event_handler_class != null && class_exists($this->_event_handler_class)) {
            $return_value = $this->_event_handler_class;
        }
        else if($this->_code != null && class_exists($this->_code . 'EventHandler')) {
            $return_value = $this->_code . 'EventHandler';
        }
        else {
            $return_value = __CurrentContext::getInstance()->getPropertyContent('DEFAULT_EVENT_HANDLER_CLASS');
        }
        return $return_value;
    }
    
    /**
     * Assign a given portion of the model to current view.
     *
     * @param __ModelMap The model map to assign to
     */
    final public function assignModel(__ModelMap &$model_map) {
        //Now will assign all model objects:
        $model_iterator = $model_map->getIterator();
        $model_iterator->first();
        $model_value_resolver = new __ModelValueResolver($this);
        while(!$model_iterator->isDone()) {
            $key   = $model_iterator->currentKey();
            $value = $model_iterator->currentItem();
            if( !is_string($value) ) {
                $value = $model_value_resolver->resolveValue($value);
            }
            $this->assign($key, $value);
            $model_iterator->next();
        }        
    }

    /**
     * Assign resources to current view
     *
     * @param __ModelMap The model map to assign to
     */
    final public function assignResources(array &$resources) {
        //Now will assign all model objects:
        foreach($resources as $resource) {
            $this->assign($resource->getKey(), $resource->getValue());
        }
    }
    
    protected function _startupRendererEngine() {}
    
    public function setCacheDir($cache_dir) {}
    
    public function setCaching($caching) {}
        
}

?>