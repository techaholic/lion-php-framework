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

class __DateBoxHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __HtmlElementProperty($component->getId(), 'value'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_id = $component->getId();
        $date_format  = $component->getDateFormat();
        $datebox_button_id = $component_id . '_calbutton';
        
        if(__ResponseWriterManager::getInstance()->hasResponseWriter('datebox')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('datebox');
            $jod_setup_response_writer = $jod_response_writer->getResponseWriter('datebox-setup');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('datebox');
            $jod_response_writer->addCssFileRef('jscalendar/calendar-green.css');
            $jod_response_writer->addJsFileRef('jscalendar/calendar.js');
            $jod_response_writer->addLoadCheckingVariable('Calendar');
            
            $jod_language_response_writer = new __JavascriptOnDemandResponseWriter('datebox-language');
            $jod_language_response_writer->addJsFileRef('jscalendar/lang/calendar-en.js');
            $jod_language_response_writer->addLoadCheckingVariable('Calendar._DN');
            $jod_response_writer->addResponseWriter($jod_language_response_writer);
            
            $jod_setup_response_writer = new __JavascriptOnDemandResponseWriter('datebox-setup');
            $jod_setup_response_writer->addJsFileRef('jscalendar/calendar-setup.js');
            $jod_setup_response_writer->addLoadCheckingVariable('Calendar.setup');
            $jod_language_response_writer->addResponseWriter($jod_setup_response_writer);
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }

        $js_code = <<<CODESET
Calendar.setup({
	inputField:"$component_id",
	ifFormat:"$date_format",
	button:"$datebox_button_id",
	showsTime:false
});
CODESET;

        $jod_setup_response_writer->addJsCode($js_code);

        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'type="text"';
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'value="' . $component->getValue() . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        
        $local_js_lib = __ApplicationContext::getInstance()->getPropertyContent('JS_LIB_DIR');
        $calendar_image_url = __UrlHelper::resolveUrl('jscalendar/calendar.gif', $local_js_lib);

        $return_value = '<input onchange="this.fire(\'lion:validate\');" ' . implode(' ', $properties) . '>&nbsp;<input type="image" src="' . $calendar_image_url . '"  id="' . $datebox_button_id . '" width="16" height="16" border="0">';
        
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '';
    }
    
    
}
