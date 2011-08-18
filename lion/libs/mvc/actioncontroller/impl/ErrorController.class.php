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
 * @package    ActionController
 * 
 */

class __ErrorController extends __ActionController {

    protected function &defaultAction()
    {
        $request = __ActionDispatcher::getInstance()->getRequest();
        $error_parameters = array();
        //1. If REQUEST_ERROR_CODE has been found on request:
        if( $request->hasParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ERROR_CODE'))) {
            $error_code = $request->getParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ERROR_CODE'));
            switch($error_code) {
                case 55601:
                    $error_parameters[] = $request->getUri()->getAbsoluteUrl();
                    break;
            }
        }
        //2. Else we don't know the exception to show (use the unknow exception):
        else {
            $error_code = __ExceptionFactory::getInstance()->getErrorTable()->getErrorCode('ERR_UNKNOW_ERROR');
        }
        throw __ExceptionFactory::getInstance()->createException($error_code, $error_parameters);
    }
}
