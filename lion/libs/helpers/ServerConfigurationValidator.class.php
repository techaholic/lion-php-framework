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
 * @package    Helpers
 * 
 */

class __ServerConfigurationValidator {
    
    static public function validate() {
        self::_validatePHP();
        self::_validateRewriteEngine();
    }
    
    static private function _validateRewriteEngine() {
        $rewrite_engine_working = false; //by default
        if(__Client::getInstance()->getRequestType() != REQUEST_TYPE_COMMAND_LINE) {
            $test_url = __UriFactory::getInstance()->createUri()->setRoute('testResponse')->getUrl();
            $test_url = __UrlHelper::resolveUrl($test_url, 'http://' . $_SERVER['SERVER_NAME']);
            if ($stream = @fopen($test_url, 'r')) {
                // print all the page starting at the offset 10
                $test_content = stream_get_contents($stream);
                fclose($stream);
                if($test_content == 'OK') {
                    $rewrite_engine_working = true;
                }
            }
            if($rewrite_engine_working == false) {
                throw __ExceptionFactory::getInstance()->createException('Either mod rewrite is not enabled in your server or is not well configured.');
            }
        }
    }

    static private function _validatePHP() {
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            throw __ExceptionFactory::getInstance()->createException('Lion requires PHP 5.2.0 or greater in order to work. Current PHP version is ' . PHP_VERSION . '.');
        }    
        
        $php_extensions = get_loaded_extensions();
        if(in_array('domxml', $php_extensions) || in_array('php_domxml', $php_extensions)) {
            throw __ExceptionFactory::getInstance()->createException('php_domxml extension detected: Need to be disabled.');
        }
    }

}

