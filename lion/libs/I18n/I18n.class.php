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
 * This class is the class in charge of manage all internationalization (i18n) stuffs.
 * 
 */
class __I18n {

    /**
     * An array of all {@link Locale} instances used in the current session
     *
     * @var array
     */
    private $_locales = null;
    
    /**
     * This is the singleton static variable that contains current shared instance
     *
     * @var __I18n
     */
    static private $_instance = null;

    /**
     * Create a new __I18n instance
     */
    private function __construct()
    {
        $session = __CurrentContext::getInstance()->getSession();
        //Locales configuration will be loaded from the Session (if exist):
        $this->_locales =& $session->getData('__I18n::_locales');
        if($this->_locales == null) {
  			$this->_locales = array();
            $locale_negociator = __LocaleNegociatorFactory::createLocaleNegociator();
            $locales = $locale_negociator->negociateLocales();
            $this->setLocales($locales);
			$session->setData('__I18n::_locales', $this->_locales);
        }
    }
    
    /**
     * This method returns a singleton instance of __I18n
     *
     * @return __I18n A singleton reference to the __I18n
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __I18n();
        }
        return self::$_instance;
    }
        
    /**
     * This method sets all locales to the specified array of Locale instances.
     *
     * @param array The array of Locale instances to set
     * @return boolean true if locales has been setted, else false
     */
    public function setLocales(array &$locales) {
        $return_value = true;
        if($return_value) {
            $this->_locales =& $locales;
            __EventDispatcher::getInstance()->broadcastEvent(new __Event($this, EVENT_ON_LOCALE_CHANGE));
        }
        return $return_value;
    }
    
    /**
     * This method adds a new __Locale to the internal collection of locales, and set it as the default locale
     *
     * @param Locale The locale to set
     * @return boolean true if the locale has been setted successfully, else false
     */
    public function addLocale(__Locale &$locale) {    
        $return_value = true;
        //If locale exists in the _locales list, will delete it previous to insert in the hight:
        for($i=0, $total_locale=count($this->_locales); $i<$total_locale; $i++) {
            if($this->_locales[$i]->isEqual($locale)) {
                unset($this->_locales[$i]);
                $reindexed_locales = array_values($this->_locales);
                $this->_locales = $reindexed_locales;                    
                break;
            }
        }
        //Now will insert the specified locale in the hight:
        last($this->_locales);
        $this->_locales[] =& $locale;
        __EventDispatcher::getInstance()->broadcastEvent(new __Event($this, EVENT_ON_LOCALE_CHANGE));
        return $return_value;
    }
    
    /**
     * This method returns all stored locales as an array of locales
     *
     * @return array All setted locales as an array
     */
    public function &getLocales() {
        $return_value =& $this->_locales;
        return $return_value;    
    }
    
    /**
     * This method return the last setted locale (that it's also the default locale)
     *
     * @return Locale The requested locale, or null if no locales are stored
     */
    public function &getLastLocale() {
        $return_value = null;
        $last_item_index = count($this->_locales) - 1;
        if($last_item_index >= 0) {
            $return_value =& $this->_locales[$last_item_index];
        }
        return $return_value;
    }

    /**
     * This method removes all stored locales, resetting the internal array of locales to zero components
     *
     */
    public function removeLocales() {
        $this->_locales = array();
        __EventDispatcher::getInstance()->broadcastEvent(new __Event($this, EVENT_ON_LOCALE_CHANGE));
    }
    
    /**
     * This method removes the last setted locale (that it's also the default locale), making the previous setted locale the default one (if exists).
     *
     * @return boolean true if the remove operation has been performed successfully, else false
     */
    public function removeLastLocale() {
        $return_value = false;
        $last_item_index = count($this->_locales) - 1;
        if($last_item_index >= 0) {
            unset($this->_locales[$last_item_index]);
            __EventDispatcher::getInstance()->broadcastEvent(new __Event($this, EVENT_ON_LOCALE_CHANGE));
            $return_value = true;
        }
        return $return_value;
    }

    
}
    
    
    