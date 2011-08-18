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

class __SliderHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __JavascriptObjectProperty($component->getId(), 'value'));
    }
    
    public function startRender(__IComponent &$component) {
        if(__ResponseWriterManager::getInstance()->hasResponseWriter('sliderjscode')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('sliderjscode');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('sliderjscode');
            $jod_response_writer->addJsFileRef('scriptaculous/src/slider.js');
            $jod_response_writer->addLoadCheckingVariable('Control');            
            $jod_response_writer->addLoadCheckingVariable('Control.Slider');            
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
        
        $component_id = $component->getId();
        $handle = $component_id . '_handle';
        $track = $component_id . '_track';
        $initial_value = $component->getValue();
        $input_receiver = $component->getInputReceiver();
        $decimals = $component->getDecimals();
        if($decimals > 0) {
            $v = 'Math.round(v*1' . str_pad('', $decimals, '0') . ')/1' . str_pad('', $decimals, '0');
        }
        else {
            $v = 'Math.round(v)';
        }
        
        $onreceiver_keyup_js = '';
        $onslide_js = '';
        if($input_receiver != null) {
            $input_receiver_id = $input_receiver->getId();
            $onslide_js = '';
            if($input_receiver instanceof __InputBoxComponent){
                $onslide_js = 'onSlide: function(v) { $(\'' . $input_receiver_id . '\').value = ' . $v . '; },';
                $onreceiver_keyup_js = <<<CODE

            $("$input_receiver_id").onKeyUp = function() {
                var value = $("$input_receiver_id").value;
                if (value == '') return;
                if (isNaN(value))
                    $('$component_id').setValue(0);
                else
                    $('$component_id').setValue(value);
            }                
                
CODE;
                        }
            else if($input_receiver instanceof __LabelComponent ) {
                $onslide_js = 'onSlide: function(v) { $(\'' . $input_receiver_id . '\').update(' . $v . '); },';
            }
        }
        
        $lower_limit = $component->getLowerLimit();
        $upper_limit = $component->getUpperLimit();
        
        $axis = $component->getAxis();
        
        $js_code = <<<CODE

        window['$component_id'] = new Control.Slider("$handle", "$track", {
                $onslide_js
                onChange: function(v) { (__ClientEventHandler.getInstance()).sendEvent("change", {}, "$component_id"); },
                range: \$R($lower_limit,$upper_limit),
                sliderValue: $initial_value,
                axis: "$axis"
        });
        $onreceiver_keyup_js  
        
CODE;

        $jod_response_writer->addJsCode($js_code);
        
        $width = $component->getWidth();
        $height = $component->getHeight();
        $handle_width = $component->getHandleWidth();
        $handle_height = $component->getHandleHeight();
        
        $track_css_class = $component->getTrackCssClass();
        $left_track_css_class = $component->getLeftTrackCssClass();
        $handle_img = $component->getHandleImg();
        
        if(empty($track_css_class)) {
            $track_style = 'style="width:' . $width . '; background-color:#ccc; height: ' . $height . ';"';
        }
        else {
            $track_style = 'class="' . $track_css_class . '"';
        }
        
        if(empty($handle_img)) {
            $handle_style = 'style="width:' . $handle_width . '; height:' . $handle_height . ';background-color:#f00; cursor:move;"';
        }
        else {
            $handle_style = 'style="width:' . $handle_width . '; height:' . $handle_height . ';"';
        }
        
        $return_value = <<<CODE
        <div id="$component_id" style="width:$width; height: $handle_height;">
        <div $track_style id="$track" style="width:$width; height:$height;">
            <div class="$left_track_css_class"></div><div id="$handle" $handle_style><img src="$handle_img" alt="" style="float: left;" /></div>
        </div>            
        </div>
CODE;
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }    
    
    
    
}
