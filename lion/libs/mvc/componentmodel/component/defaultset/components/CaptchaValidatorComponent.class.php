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

class __CaptchaValidatorComponent extends __ValidationRuleComponent {

    protected $_captcha_image_component = null;
    
    public function setCaptcha($captcha_image_component) {
        if(is_string($captcha_image_component)) {
            $this->_captcha_image_component = $captcha_image_component;
        }
    }
    
    /**
     * Get the component to be validated by the current validator
     *
     * @return __IComponent
     */
    public function getCaptcha() {
        $return_value = null;
        $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
        if($component_handler != null) {
            if($component_handler->hasComponent($this->_captcha_image_component)) {
                $return_value = $component_handler->getComponent($this->_captcha_image_component, $this->_captcha_component_index);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Component to validate not found: ' . $this->_component);
            }
        }
        return $return_value;
    }    
    
    protected function _doValidation(__IComponent &$component) {
        $this->_validation_result = true;
        if( $component instanceof __IValueHolder && $component->getEnabled() && $component->getVisible()) {
            $value = $component->getValue();
            $captcha_image_component = $this->getCaptcha();
            if(!$captcha_image_component->check($value)) {
                $this->setErrorMessage( 'The code is invalid' );
                $this->_validation_result = false;
            }            
        }
        if($this->_validation_result == true) {
            $this->_error_message = null;
        }
        return $this->_validation_result;
    }
    
}
