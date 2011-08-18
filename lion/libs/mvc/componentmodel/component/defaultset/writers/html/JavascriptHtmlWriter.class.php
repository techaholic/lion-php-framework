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

class __JavascriptHtmlWriter extends __ComponentWriter {

    public function startRender(__IComponent &$component) {
        return '';
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $group_id = $component->getGroup();
        if($group_id == null) {
            $group_id = $component->getId();
        }
        
        if(__ResponseWriterManager::getInstance()->hasResponseWriter($group_id)) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter($group_id);
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter($group_id);
            $js_files = $component->getJsFiles();
            foreach($js_files as $js_file) {                
                $jod_response_writer->addJsFileRef($js_file);                
            }
            $checking_variables = $component->getCheckingVariables();
            foreach($checking_variables as $checking_variable) {
                $jod_response_writer->addLoadCheckingVariable($checking_variable);
            }
            $javascript_rw = null;
            if($component->hasContainer()) {
                $container = $component->getContainer();
                if( $container instanceof __JavascriptComponent ) {
                    $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter($container->getId());
                }
            }
            if($javascript_rw == null) {
                $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            }
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
        $jod_response_writer->setLoadAfterDomLoaded($component->getAfterDomLoaded());
        $jod_response_writer->addJsCode($enclosed_content);
    }
    
    public function endRender(__IComponent &$component) {
        return '';
    }
        
    
}
