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

class __JavascriptOnDemandResponseWriter implements __IResponseWriter {
    
    const JS_CODE_POSITION_TOP    = 1;
    const JS_CODE_POSITION_MIDDLE = 2;
    const JS_CODE_POSITION_BOTTOM = 3;
    
    private $_id = null;
    private $_css_files = array();
    private $_js_files  = array();
    private $_load_after_dom_loaded = false;
    private $_load_once = true;
    private $_verifier_js_code = null;
    private $_js_code_at_top = array();
    private $_js_code = array();
    private $_js_code_at_bottom = array();
    private $_force_callback = true;
    private $_response_writers = array();
    private $_enclose_content_in_js_tags = true;
    private $_load_checking_variables = array();
    
    public function __construct($id) {
        if(empty($id)) {
            throw __ExceptionFactory::getInstance()->createException('A valid id is required to instantiate a __JavascriptOnDemandWriterHelper object');
        }
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function addCssFileRef($css_file, $use_local_js_lib = true) {
        if($use_local_js_lib == true) {
            $local_js_lib = __ApplicationContext::getInstance()->getPropertyContent('JS_LIB_DIR');
            $css_file = __UrlHelper::resolveUrl(__UrlHelper::glueUrlParts($local_js_lib, $css_file));
        }
        else {
            $css_file = __UrlHelper::resolveUrl($css_file);
        }
        $this->_css_files[] = '"' . $css_file . '"';
    }
    
    public function addLoadCheckingVariable($variable) {
        $this->_load_checking_variables['typeof ' . $variable . ' != "undefined"'] = true;
    }
    
    public function getLoadCheckingVariables() {
        return $this->_load_checking_variables;
    }
    
    public function getVerifierCode() {
        $return_value = null;
        if($this->_verifier_js_code != null) {
            $return_value = $this->_verifier_js_code;
        }
        else if(count($this->_load_checking_variables) > 0) {
            $checking_variables = array_keys($this->_load_checking_variables);
            $return_value = 'function() { return (' . join(' && ', $checking_variables) . ') ? true : false; }';
        }
        return $return_value;
    }
    
    public function addJsFileRef($js_file, $use_local_js_lib = true) {
        if($use_local_js_lib == true) {
            $local_js_lib = __ApplicationContext::getInstance()->getPropertyContent('JS_LIB_DIR');
            $js_file = __UrlHelper::resolveUrl(__UrlHelper::glueUrlParts($local_js_lib, $js_file));
        }
        else {
            $js_file = __UrlHelper::resolveUrl($js_file);
        }
        $this->_js_files[] = '"' . $js_file . '"';
    }

    public function addVerifierJsCode($verifier_js_code) {
        $this->_verifier_js_code = $verifier_js_code;
    }
    
    public function addJsCode($js_code, $position = self::JS_CODE_POSITION_MIDDLE) {
        switch($position) {
            case self::JS_CODE_POSITION_MIDDLE:
                $this->_js_code[] = $js_code;
                break;
            case self::JS_CODE_POSITION_TOP:
                $this->_js_code_at_top[] = $js_code;
                break;
            default:
                $this->_js_code_at_bottom[] = $js_code;
        }
    }
    
    public function setLoadAfterDomLoaded($load_after_dom_loaded) {
        $this->_load_after_dom_loaded = (bool) $load_after_dom_loaded;
    }
    
    public function setEncloseContentInJsTags($enclose_content_in_js_tags) {
        $this->_enclose_content_in_js_tags = $enclose_content_in_js_tags;
    }
    
    public function getEncloseContentInJsTags() {
        return $this->_enclose_content_in_js_tags;
    }
    
    public function getContent() {
        $return_value = implode("\n", $this->_js_code_at_top);
        $return_value .= implode("\n", $this->_js_code);
        $return_value .= $this->_getChildrensContent();
        $return_value .= implode("\n", $this->_js_code_at_bottom);
        
        $return_value = $this->_addResourcesLoad($return_value);
        if($this->_load_after_dom_loaded) {
            $return_value = $this->_addExecuteAfterDomLoaded($return_value);
        }
        if($this->_enclose_content_in_js_tags) {
            $return_value = $this->_encloseContentInJsTags($return_value);
        }
        return $return_value;
    }
    
    protected function _getChildrensContent() {
        $return_value = '';
        foreach($this->_response_writers as $response_writer) {
            if($response_writer instanceof __JavascriptOnDemandResponseWriter) {
                $response_writer->setEncloseContentInJsTags(false);
                //prevent executing after dom loaded twice:
                if($this->_load_after_dom_loaded) {
                    $response_writer->setLoadAfterDomLoaded(false);
                }
            }
            $return_value .= $response_writer->getContent();
        }
        return $return_value;
    }
    
    public function __toString() {
        return $this->getContent();
    }
    
    public function write(__IResponse &$response) {
        $response->dockContentAtBottom($this->getContent());
    }
    
    public function hasResponseWriter($id) {
        $return_value = false;
        if(key_exists($id, $this->_response_writers)) {
            $return_value = true;
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                if($response_writer->hasResponseWriter($id)) {
                    return true;
                }
            }
        }
        return $return_value;
    }
    
    public function &getResponseWriter($id) {
        $return_value = null;
        if(key_exists($id, $this->_response_writers)) {
            $return_value =& $this->_response_writers[$id];
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                $return_value = $response_writer->getResponseWriter($id);
                if($return_value != null) {
                    return $return_value;
                }
            }
        }
        return $return_value;
    }
    
    public function addResponseWriter(__IResponseWriter $response_writer) {
        $this->_response_writers[$response_writer->getId()] = $response_writer;
    }
    
    public function clear() {
        $this->_response_writers = array();
    }    

    protected function _addResourcesLoad($js_code) {
        $total_files = count($this->_css_files) + count($this->_js_files);
        //if no resources to load, do not nothing:
        if($total_files == 0) {
            return $js_code;
        }
        
        //otherwise, generate code to load on demand:
        $return_value = '';
        if(count($this->_css_files) + count($this->_js_files) > 1) {
            //todo
        }
        
        foreach($this->_css_files as $css_file) {
            $return_value .= 'JIT.addCSS(' . $css_file . ');' . "\n";
        }
        $js_files = '[' . implode(', ', $this->_js_files) . ']';
        
        $load_parameters = array();
        $load_parameters[] = $js_files;
        $verifier_js_code = $this->getVerifierCode();
        if($verifier_js_code != null) {
            $load_parameters[] = $verifier_js_code;               
        }
        else if(!empty($js_code)) {
            throw __ExceptionFactory::getInstance()->createException('Need to specify either a variable to check or a verifier code, otherwise the lazy load won\'t work as expected');
        }
        if(!empty($js_code)) {
            $load_parameters[] = 'function() {' . "\n" . $js_code . "\n}";
        }
        //generate js code:
        $return_value .= 'JIT.loadOnce(' . implode(', ', $load_parameters) . ');' . "\n";
        return $return_value;
    }
    
    protected function _addExecuteAfterDomLoaded($js_code) {
        return "DomLoaded.load(function() {\n" . $js_code . "});\n";
    }
    
    protected function _encloseContentInJsTags($js_code) {
        return "\n
<!-- generated by lion framework -->
<script language=\"JavaScript\" type=\"text/javascript\">
" . $js_code  . "
</script>
";
    }    
    
}