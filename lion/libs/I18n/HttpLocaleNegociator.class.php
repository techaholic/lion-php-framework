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


class __HttpLocaleNegociator implements __ILocaleNegociator {

    public function negociateLocales() {
        $return_value = array();
        //If defined and have value HTTP_ACCEPT_LANGUAGE, will switch to this language:
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if($_SERVER['HTTP_ACCEPT_LANGUAGE'] != '') {
                $accepted_languages = preg_split('/,/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach($accepted_languages as $language) {
                    $candidate_language_iso_code = $this->_getLanguageIsoCode($language);
                    if(!empty($candidate_language_iso_code)) {
                        if(__ResourceManager::getInstance()->isSupportedLanguage($candidate_language_iso_code)) {
                            $candidate_locale = new __Locale($candidate_language_iso_code);
                            $return_value[] = $candidate_locale;
                        }
                    }
                }
            }
        }
        if(count($return_value) === 0) {
            $default_lang_iso_code = __ContextManager::getInstance()->getCurrentContext()->getPropertyContent('DEFAULT_LANG_ISO_CODE');
            if(__ResourceManager::getInstance()->isSupportedLanguage($default_lang_iso_code)) {
                $default_locale = new __Locale($default_lang_iso_code);
                $return_value[] = $default_locale;
            }
        }
        //Now will reverse the array according to the order used by the __I18n:
        $return_value = array_reverse($return_value);
        
        return $return_value;
    }
    
    private function _getLanguageIsoCode($language) {
        $return_value = trim(preg_replace('/(.*)\;(.*)/', "$1", $language)); //eliminate right part when a ";" is encountered
        //if language have "-", will cut the string in the "-" symbol (f.e. for "en_us" will use "en")
        if(strpos($return_value, "-") !== false) {
            $return_value = preg_replace('/([^-]*)\-(.*)/', "$1", $return_value);
        }
        return $return_value;
    }
     
    
}

