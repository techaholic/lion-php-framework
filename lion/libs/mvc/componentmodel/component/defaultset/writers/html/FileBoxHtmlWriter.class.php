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

class __FileBoxHtmlWriter extends __ComponentWriter {
    
    public function startRender(__IComponent &$component) {
        $component_id = $component->getId();

        if(__ResponseWriterManager::getInstance()->hasResponseWriter('uploadfile')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('uploadfile');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('uploadfile');
            $jod_response_writer->setLoadAfterDomLoaded(true);
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }

        $jod_response_writer->addJsCode($component_id . ' = new __FileUploader($("' . $component_id . '"));' . "\n");        
        
        if($component->getStatus() == __IUploaderComponent::UPLOAD_STATUS_DONE) {
            $filename = $component->getFilename();
            $icon = $component->getIcon();
            if($icon != null) {
                $filename = "<img src='$icon' width='32' height='32' valign='absmiddle'>&nbsp;" . $filename;
            }
            $jod_response_writer->addJsCode($component_id . '.renderAsUploaded("' . $filename . '");');
        }
        
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'type = "file"';
        $properties[] = 'id = "' . $component_id . '"';
        $properties[] = 'name = "' . $component_id . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }

        $input_file_properties = implode(' ', $properties);

        $return_value = <<<CODE
    <input type="file" $input_file_properties>
CODE;

        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '</input>';
    }
        
    
}
