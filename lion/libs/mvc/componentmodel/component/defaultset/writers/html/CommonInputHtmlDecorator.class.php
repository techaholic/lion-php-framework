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
 * @package    ComponentModel
 * 
 */

class __CommonInputHtmlDecorator extends __ComponentWriterDecorator {

    public function endRender(__IComponent &$component) {
        $component_id = $component->getId();
        $return_value = $this->_component_writer->endRender($component);
        $context_help = $component->getContextHelp();
        if(!empty($context_help)) {
            if(__ResponseWriterManager::getInstance()->hasResponseWriter('formcontexthelp')) {
                $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('formcontexthelp');
            }
            else {
                $jod_response_writer = new __JavascriptOnDemandResponseWriter('formcontexthelp');
                $jod_response_writer->addCssFileRef('formcontexthelp/formcontexthelp.css');
                $jod_response_writer->addJsFileRef('formcontexthelp/formcontexthelp.js');
                $jod_response_writer->addLoadCheckingVariable('prepareInputsForHints');
                $jod_response_writer->addJsCode("\nprepareInputsForHints();\n");
                $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
                $javascript_rw->addResponseWriter($jod_response_writer);              
            }
            
            $return_value .= '<span id="' . $component_id . '_contextHelp" class="hint" style="display: none;">' . $context_help . '<span id="' . $component_id . '_contextHelpPointer" class="hint-pointer" style="display: none;">&nbsp;</span></span>';
        }
        $example = str_replace("'", "\\'", $component->getExample());
        if(!empty($example)) {
            if(__ResponseWriterManager::getInstance()->hasResponseWriter('inputexamples')) {
                $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('inputexamples');
            }
            else {
                $jod_response_writer = new __JavascriptOnDemandResponseWriter('inputexamples');
                $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
                $javascript_rw->addResponseWriter($jod_response_writer);
                $code = <<<CODESET
                
window['showAllExamples'] = function() {
    $$('input').each(function(inputElement) {
        showExampleValueOnInput(inputElement);
    });
};
           
var showExampleValueOnInput = function(input) {
  if(input['exampleValue']) {
      input.onfocus = function () {
          if(input.exampleValue == input.value) {
              if(input.originalType == 'password') {
                  input.type = 'password';
              }
              input.value = '';
              input.style.color = '';
          }
      }
      input.onblur = function () {
          if(input.value == '') {
              if(input.type == 'password') {
                  input.originalType = 'password';
                  input.type = 'text';
              }
              input.value = input.exampleValue;
              input.style.color = '#858585';
          }
      }
      
      if(input.value == '') {
          if(input.type == 'password') {
              input.originalType = 'password';
              input.type = 'text';
          }
          input.value = input.exampleValue;
      }
      if(input.value == input.exampleValue) {
          if(input.type == 'password') {
              input.originalType = 'password';
              input.type = 'text';
          }
          input.style.color = '#858585';
      }
  }  
};
CODESET;
                $jod_response_writer->addJsCode($code);
            }
            $code = <<<CODESET
var $component_id = $('$component_id'); 
$component_id.exampleValue = '$example';
showExampleValueOnInput($component_id);
CODESET;
            $jod_response_writer->addJsCode($code);
        }
        
        $mask = $component->getMask();
        if(!empty($mask)) {
            if(__ResponseWriterManager::getInstance()->hasResponseWriter('inputmask')) {
                $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('inputmask');
            }
            else {
                $jod_response_writer = new __JavascriptOnDemandResponseWriter('inputmask');
                $jod_response_writer->addJsFileRef('inputmask/inputmask.js');
                $jod_response_writer->addLoadCheckingVariable('__InputMask');
                $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
                $javascript_rw->addResponseWriter($jod_response_writer);              
            }
            $jod_response_writer->addJsCode("(__InputMask.getInstance()).addMask(\"$component_id\", \"$mask\");\n");
            
        }
        
        return $return_value;
   }    

    
    
}
