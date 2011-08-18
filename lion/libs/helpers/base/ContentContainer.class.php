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
 * @package    Base
 * 
 */

class __ContentContainer {
    
    protected $_top_content = array();

    protected $_content = array();

    protected $_bottom_content = array();
    
    protected $_filters = array();

    /**
     * Contains the id for current position where to append content to
     *
     * @var string
     */
	protected $_current_content_position = null;
    
    public function prependContent($content, $id = null) {
        $content = $this->_getStringRepresentation($content);
        if($id == null) {
            $id = uniqid();
        }
        $this->_content = array($id => $content) + $this->_content;
    }

    public function appendContent($content, $id = null) {
        return $this->addContent($content, $id);
    }
    
    public function dockContentOnTop($content, $id = null) {
        $content = $this->_getStringRepresentation($content);
        if($id == null) {
            $id = uniqid();
        }
        $this->_top_content[$id] = $content; 
    }
    
    public function dockContentAtBottom($content, $id = null) {
        $content = $this->_getStringRepresentation($content);
        if($id == null) {
            $id = uniqid(); 
        }
        $this->_bottom_content[$id] = $content; 
    }
    
    public function addContent($content, $id = null, $after_content_id = null) {
        $content = $this->_getStringRepresentation($content);
        if($after_content_id != null) {
            if(!key_exists($after_content_id, $this->_content)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_CONTENT_ID_NOT_FOUND', array($after_content_id));
            }
            if($id == null) {
                $id = uniqid();
            }
            $this->_content = $this->_push_after($this->_content, array($id => $content), $after_content_id);
        }
        else if($id != null) {
            $this->_content[$id] = $content;
            $this->_current_content_position = null;
        }
        else {
            if($this->_current_content_position != null && key_exists($this->_current_content_position, $this->_content)) {
                $this->_content[$this->_current_content_position] .= $content;
            }
            else {
                $id = uniqid();
                $this->_content[$id] = $content;
                $this->_current_content_position = $id;
            }
        }
    }    
    
    protected function _getStringRepresentation($content) {
        if(is_string($content)) {
            return $content;
        }
        else if(method_exists($content, '__toString')) {
            return $content->__toString();
        }
        else {
            return print_r($content, true);
        }
    }
    
    public function addPostFilter($content_container_postfilter) {
        $this->_postfilters[] = $content_container_postfilter;
    }
    
    public function clearContent($id = null) {
        if($id == null) {
            $this->_top_content    = array();
            $this->_content        = array();
            $this->_bottom_content = array();
        }
        else {
            if(key_exists($id, $this->_top_content)) {
                unset($this->_top_content[$id]);
            }
            if(key_exists($id, $this->_content)) {
                unset($this->_content[$id]);
            }
            if(key_exists($id, $this->_bottom_content)) {
                unset($this->_bottom_content[$id]);
            }
        }
    }
    
    /**
     * Alias of clearContent
     *
     * @param unknown_type $id
     */
    public function clear($id = null) {
        $this->clearContent($id);
    }
    
    /**
     * @return array
     * @param array $src
     * @param array $in
     * @param int|string $pos
    */
    private function _push_before($src,$in,$pos){
        if(is_int($pos)) $R=array_merge(array_slice($src,0,$pos), $in, array_slice($src,$pos));
        else{
            foreach($src as $k=>$v){
                if($k==$pos)$R=array_merge($R,$in);
                $R[$k]=$v;
            }
        }return $R;
    }
    
    /**
     * @return array
     * @param array $src
     * @param array $in
     * @param int|string $pos
    */
    private function _push_after($src,$in,$pos){
        if(is_int($pos)) $R=array_merge(array_slice($src,0,$pos+1), $in, array_slice($src,$pos+1));
        else{
            foreach($src as $k=>$v){
                $R[$k]=$v;
                if($k==$pos)$R=array_merge($R,$in);
            }
        }return $R;
    }     
    
    public function getContent() {
        $return_value = implode($this->_top_content) . 
                        implode($this->_content)     . 
                        implode($this->_bottom_content);
        foreach($this->_postfilters as $postfilter) {
            $return_value = $postfilter->filter($return_value);
        }
        return $return_value;
    }    
    
}