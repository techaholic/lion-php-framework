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


class __ResourceView extends __SimpleView {

    public function execute() {
        $resource = $this->_getResourceToRender();
        if($resource != null) {
            $resource->display();        
        }
    }
    
    protected function _getResourceToRender() {
        $return_value = null;
    	if($this->isAssigned('resource')) {
            $resource_to_load = $this->getAssignedVar('resource');
            if( $this->_isValidResource($resource_to_load) ) {
                if(file_exists($resource_to_load) && strpos(realpath($resource_to_load), APP_DIR) !== false) {
                    $resource_content = file_get_contents($resource_to_load);
                }
                else {
        	        $resource_content = file_get_contents(LION_DIR . DIRECTORY_SEPARATOR . $resource_to_load);
                }
        	    $return_value = new __FileResource();
        	    $return_value->setValue($resource_content);
        	    $return_value->setFileName($resource_to_load);
            }
    	}
    	else if($this->isAssigned('resource_id')) {
    	    $resource_id     = $this->getAssignedVar('resource_id');
    	    $return_value    = __Resourcemanager::getInstance()->getResource($resource_id);
    	    __ResourceManager::getInstance()->removeResource($resource_id);
    	}
    	return $return_value;        
    }    
    
	protected function _isValidResource($resource_to_load) {
	    $return_value = false; //by default is not valid
        if(strpos($resource_to_load, '..') === false &&
           preg_match('/(\.gif|\.jpg|\.png|\.css|\.js|\.htm|\.html)$/i', $resource_to_load)) {
            $return_value = true;
        }
	    return $return_value;
	}    
    
}

