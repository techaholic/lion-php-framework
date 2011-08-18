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

/**
 * This is the controller executed when requesting the index.php file from the browser.
 * This controller will check the server configuration and will alert about whatever problem found.
 * 
 * In case the configuration is ok, will advice about the usage of beautifull urls instead of executing the index.php
 *
 */
class __IndexDotPhpController extends __ActionController {

    public function defaultAction() {
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            //perform the validation:
            __ServerConfigurationValidator::validate();
            $url = __UriFactory::getInstance()->createUri()->setController('index')->getAbsoluteUrl();
            $file = basename($url);
            $baseurl = str_replace($file, '', $url);
            
            $message = <<<CODESET

<h1>Do not execute index.php from your browser</h1><br>
Lion intercepts all requests in the form of <b>$baseurl...</b> and redirects them to the MVC in order to show the page corresponding to each one.<br>
i.e. you may use $url, which will be intercepted by lion and redirected to the index controller<br> 
<br>
Go to <a href="$url">$file</a><br>
<br>
<br>
See the <a href="http://www.lionframework.org/documentation">documentation</a> for more information about urls and the MVC.
CODESET;
            echo $message;            
        }
    }
    
}
