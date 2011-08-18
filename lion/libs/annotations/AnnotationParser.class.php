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
 * @package    Annotation
 * 
 */

/**
 * This is the class in charge of parsing classes by detecting comment-based annotation
 *
 */
class __AnnotationParser {

    static private $_instance = null;
    private $_annotations = null;
    
    private function __construct() {
    }
    
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __AnnotationParser();
        }
        return self::$_instance;
    }
    
    public function _loadAnnotations() {
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE') == false) {
            $cache = __ApplicationContext::getInstance()->getCache();
            $cached_annotations = $cache->getData('annotations');
            if(is_array($cached_annotations)) {
                $this->_annotations = $cached_annotations;
            }
            else {
                $this->_annotations = array();
            }
        }
        else {
            $this->_annotations = array();
        }
    }
    
    protected function _saveAnnotations() {
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE') == false) {
            $cache = __ApplicationContext::getInstance()->getCache();
            $cache->setData('annotations', $this->_annotations);
        }
    }
    
    public function getAnnotations($class_name, $annotation_id = 'lion') {
        //annotations lazy initialization:
        if($this->_annotations == null) {
            $this->_loadAnnotations();
        }
        $class_key = strtoupper($class_name);
        if(key_exists($class_key, $this->_annotations)) {
            $return_value = $this->_annotations[$class_key];
        }
        else {
            $return_value = new __AnnotationsCollection();
            $class = new ReflectionClass($class_name);
            $methods = $class->getMethods();
            foreach($methods as $method) {
                $method_name = $method->getName();
                $method_doc_comments = $method->getDocComment();
                $lion_annotations_matched = array();
                if(preg_match_all('/\@' . $annotation_id . '\s+([^\(\s]+)(\((.+)\))?/', $method_doc_comments, $lion_annotations_matched)) {
                    $annotations_count = count($lion_annotations_matched[0]);
                    for($i = 0; $i < $annotations_count; $i++) {
                        $annotation_name = $lion_annotations_matched[1][$i];
                        if(count($lion_annotations_matched) >= 4) {
                            $annotation_arguments = __ParameterSplitter::splitParameters($lion_annotations_matched[3][$i]);
                        }
                        else {
                            $annotation_arguments = array();
                        }
                        $annotation = new __Annotation($class_name, $method_name, $annotation_name, $annotation_arguments);
                        $return_value->add($annotation);
                        unset($annotation);
                    }
                }
            }
            $this->_annotations[$class_key] = $return_value;
            $this->_saveAnnotations();
        }
        return $return_value;
    }
    
    
}
