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

class __UrlHelper {
    
    /**
     * Resolves an url depending on the specified base. 
     * If no base is specified, the APP_URL_PATH will be used as base.
     * 
     * Note: Absolute url won't be altered.
     * The same if applicable to an url starting with the '/' character if no base is specified.
     * 
     * i.e.
     * <code>
     * echo __UrlHelper::resolveUrl('path/to/foo.php', 'base/url/');
     * --> /base/url/path/to/foo.php
     * 
     * echo __UrlHelper::resolveUrl('path/to/foo.php', 'http://domain.com/');
     * --> http://domain.com/path/to/foo.php
     * 
     * echo __UrlHelper::resolveUrl('path/to/foo.php');
     * --> /base/url/path/to/foo.php (being APP_URL_PATH = "/base/url/")
     * 
     * echo __UrlHelper::resolveUrl('/path/to/foo.php');
     * --> /path/to/foo.php
     * </code>
     * 
     * @param string $url The url to resolve to
     * @param string $base A base path to apply to
     * @return string The resultant url
     */
    static public function resolveUrl($url, $base = null) {
        if(preg_match('/^\w+\:\/\//', $url)) {
            return $url; 
        }
        else if(!empty($base)) {
            $return_value = self::glueUrlParts($base, $url);
            if(!preg_match('/^\w+\:\/\//', $return_value)) {
                $return_value = '/' . $return_value;
            }            
        }
        else if(substr($url, 0, 1) != '/') {
            if(defined("APP_URL_PATH")) {
                $url_path = APP_URL_PATH;
            }
            else {
                $url_path = __ApplicationContext::getInstance()->getPropertyContent('APP_URL_PATH');
            }           
            $return_value = '/' . self::glueUrlParts($url_path, $url);
        }
        else {
            $return_value = $url;
        }
        return $return_value;
    }
    
    static public function glueUrlParts($base, $url) {
        $base = trim($base, '/');
        $return_value = ltrim($url, '/');
        if(!empty($base)) {
            $return_value = $base . '/' . $return_value;
        }
        return $return_value;        
    }
    
    
}