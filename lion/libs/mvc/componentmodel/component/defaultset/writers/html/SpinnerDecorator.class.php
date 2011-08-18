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

class __SpinnerDecorator extends __ComponentWriterDecorator {
    
    public function endRender(__IComponent &$component) {
        
        $return_value = $this->_component_writer->endRender($component);
        $spinner = $component->spinner;
        if(empty($spinner)) {
            $spinner = true; //by default
        }
        else {
            $spinner = $this->_toBool($spinner);
        }
        if($spinner) {
            $component_id = $component->getId();
            $spinner_id   = $component_id . '_spinner';
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            //setup spinner
            if($component->hasProperty('spinnerTargetComponent')) {
                $target_component_name = $component->getProperty('spinnerTargetComponent');
                $view_code = $component->getViewCode();
                $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($view_code);
                if($component_handler != null && $component_handler->hasComponent($target_component_name)) {
                    $spinner_target = $component_handler->getComponent($target_component_name)->getId();
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('Target spinner component not found: ' . $target_component_name);                    
                }
            }
            else if($component->hasProperty('spinnerTargetElement')) {
                $spinner_target = $component->getProperty('spinnerTargetElement');                
            }
            else {
                $spinner_target = $component_id;
            }
            $spinner_target = 
            $javascript_rw->addJsCode($spinner_id . ' = new __Spinner("' . $spinner_target . '");');
            //setup waiting message:
            $waiting_message = $component->waitingMessage;
            if(!empty($waiting_message)) {
                $javascript_rw->addJsCode($spinner_id . '.setWaitingMessage("' . nl2br(stripslashes($waiting_message)) . '");');
            }
            //setup progress bar:
            $show_progress = $this->_toBool($component->showProgress);
            if($show_progress) {
                $js_code = <<<CODE
var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler("$component_id");
if(typeof(progressHandler) != "undefined") {
    progressHandler.registerUiProgressIndicator($spinner_id);
}
CODE;
                $javascript_rw->addJsCode($js_code);
                
            }
            
        }
        return $return_value;
    }
   
    protected function _toBool($value) {
        if(is_string($value)) {
            switch(strtoupper($value)) {
                 case 'FALSE':
                 case 'NO':
                     $value = false;
                     break;
                 default:
                     $value = true;
                     break;
            }
        }
        else {
            $value = (bool) $value;
        }       
        return $value;
   }
   
    
}