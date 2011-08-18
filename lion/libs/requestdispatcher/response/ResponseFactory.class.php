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
 * @package    Response
 * 
 */

class __ResponseFactory {
    
    static private $_instance       = null;

    /**
     * This is the class constructor:
     */
    private function __construct()
    {
    }

    /**
     * This method return a singleton instance of __SessionManager
     *
     * @return __SessionManager A singleton reference to the __SessionManager
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ResponseFactory();
        }
        return self::$_instance;
    }
    
    public function &createResponse($request_type = null ) {
        $return_value = null;
        if($request_type == null) {
            $request_type = __Client::getInstance()->getRequestType();
        }
        switch ($request_type) {
            case REQUEST_TYPE_COMMAND_LINE:
                $response_class = __CurrentContext::getInstance()->getPropertyContent('COMMAND_LINE_RESPONSE_CLASS');
                break;
            case REQUEST_TYPE_XMLHTTP:
                $response_class = __CurrentContext::getInstance()->getPropertyContent('XML_HTTP_RESPONSE_CLASS');
                break;
            default:
                $response_class = __CurrentContext::getInstance()->getPropertyContent('HTTP_RESPONSE_CLASS');
                break;
        }
        if(class_exists($response_class)) {
            $return_value = new $response_class();
        }
        if(!($return_value instanceof __IResponse)) {
            __ExceptionFactory::getInstance()->createException('Wrong response class: ' . $response_class . '. The response class must implement the __IResponse');
        }
        return $return_value;
    }
    
}