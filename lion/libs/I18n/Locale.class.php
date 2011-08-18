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
 * @package    I18n
 * 
 */


/**
 * This class contain a complete locale information used to handle the i18n and l10n
 *
 */
class __Locale {

    private $_locale_code   = null;
    private $_language_code = null;    
    private $_country_code  = null;
    
    /**
     * Create a new __Locale instance
     */
    function __construct($locale_code) {
        $this->setCode($locale_code);    
    }
    
    public function isEqual($locale) {
        $return_value = false;
        if ($locale->getCode() == $this->getCode()) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setCode($locale_code) {
        $return_value = false; //by default
        if($this->_isValidLocaleCode($locale_code)) { 
            $this->_locale_code = $locale_code;
            $locale_components = preg_split('/[_-]/', $locale_code, 2, PREG_SPLIT_NO_EMPTY);
            $this->_language_code = $locale_components[0];
            if(count($locale_components) > 1) {
                $this->_country_code  = $locale_components[1];
            }
            else {
                $this->_country_code = null;
            }
            $return_value = true; 
        }
        return $return_value;
        
    }

    public function getCode() {
        return $this->_locale_code;
    }
    
    public function getLanguageCode() {
        return $this->_language_code;
    }
    
    public function getCountryCode() {
        return $this->_country_code;
    }

    public function getLanguageIsoCode() {
        return $this->getLanguageCode();
    }
    
    public function getCountryIsoCode() {
        return $this->getCountryCode();
    }
    
    /**
     * @todo improve this method
     *
     * @param unknown_type $locale_code
     * @return unknown
     */
    private function _isValidLocaleCode($locale_code) {
        $return_value = false;
        if(!empty($locale_code)) {
            if(strlen(trim($locale_code)) >= 2) {
                $return_value = true;
            }
        }
        return $return_value;
    }
        
    public function formatCurrency($amount, $format = null) {
        if($format == null) {
            $format = __ApplicationContext::getInstance()->getPropertyContent('DEFAULT_CURRENCY_FORMAT');
        }
        $locale_code = setlocale(LC_MONETARY, 0);
        if($locale_code != $this->_currency_code) {
            setlocale(LC_MONETARY, $this->_currency_code);
        }
        return money_format($format, $amount);
    }
    
}

