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
 * @package    Core
 * 
 */

set_exception_handler('handleException');
//set_error_handler("handleError", E_ALL ^ E_NOTICE);

function handleException(Exception $e) {
    __ErrorHandler::getInstance()->handleException($e);
}

function handleError($errno, $errstr, $errfile, $errline ) {
    $error_exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);
    __ErrorHandler::getInstance()->handleException($error_exception);
    return false;
}

class __ErrorHandler {

    static private $_instance = null;
    private $_default_error_printer_class = null;

    /**
     * Create a new __ErrorHandler instance.
     */
    private function __construct() {
        $request_type = __Client::getInstance()->getRequestType();
        switch ($request_type) {
            case REQUEST_TYPE_XMLHTTP:
                $this->_default_error_printer_class = '__AsyncMessageErrorPrinter';
                break;
            case REQUEST_TYPE_COMMAND_LINE:
                $this->_default_error_printer_class = '__CommandLineErrorPrinter';    
                break;
            case REQUEST_TYPE_HTTP:
            default:
                $this->_default_error_printer_class = '__HtmlErrorPrinter';
                break;
        }
    }

    /**
     * This method return a singleton instance of __ErrorHandler
     *
     * @return error _manager A singleton reference to the __ErrorHandler
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ErrorHandler();
        }
        return self::$_instance;
    }

    public function getDefaultErrorPrinter() {
        $error_printer_class = $this->_default_error_printer_class;
        return new $error_printer_class();
    }
    
    public function getErrorPrinter() {
        $error_printer_class = null;
        if(__Lion::getInstance()->getStatus() == __Lion::STATUS_RUNNING) {
            $request_type = __Client::getInstance()->getRequestType();
            switch ($request_type) {
            	case REQUEST_TYPE_XMLHTTP:
                    $error_printer_class = __CurrentContext::getInstance()->getPropertyContent('XMLHTTP_ERROR_PRINTER_CLASS');
            	    break;
            	case REQUEST_TYPE_COMMAND_LINE:
                    $error_printer_class = __CurrentContext::getInstance()->getPropertyContent('COMMAND_LINE_ERROR_PRINTER_CLASS');
            	    break;
                case REQUEST_TYPE_HTTP:
                default:
                    $error_printer_class = __CurrentContext::getInstance()->getPropertyContent('HTTP_ERROR_PRINTER_CLASS');
                    break;
            }
        }
        
        if($error_printer_class == null) {        
            $error_printer_class = $this->_default_error_printer_class;
            $error_printer = new $error_printer_class();
        }
        else {
            $error_printer = new $error_printer_class();
            if( !$error_printer instanceof __IErrorPrinter ) {
                $error_printer = $this->getDefaultErrorPrinter();
            }
        }
        return $error_printer;
    }

    /**
     * This method handles exceptions that haven't been trapped
     *
     * @param Exception $exception The exception to handle
     */
    public function handleException(Exception $exception)
    {
        ini_set('display_errors','Off'); 
        
        //clear the response
        $response = __FrontController::getInstance()->getResponse();
        if($response != null) {
            $response->clear();
            //AND DO NOT CACHE THE RESPONSE AT ALL
            $response->setCacheable(false);
        }
        
        //now handle the exception:
        try {
            $error_printer = $this->getErrorPrinter();
            $show_error = true; //by default
            $this->logException($exception);
            if ( $exception instanceof __LionException ) {
                $exception->executeCustomAction();
                if($exception->getExceptionType() != __ExceptionType::CRITICAL) {
                    $show_error = false;
                }
            }
            if($show_error == true) {
                $error_printer->displayError($exception);
            }
        }
        catch (Exception $e) {
            $error_printer = $this->getDefaultErrorPrinter();
            //$exception = new Exception('Critial error while trying to handle an error: ' . $e->getMessage() . print_r($e, true));
            $error_printer->displayError($e);
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        try {
            $exception = new __CoreException($errstr, $errno);
            $error_printer = $this->getErrorPrinter();
            $error_printer->displayError($exception);
        }
        catch (Exception $e) {
            $error_printer = $this->getDefaultErrorPrinter();
            //$exception = new Exception('Critial error while trying to handle an error: ' . $e->getMessage() . print_r($e, true));
            $error_printer->displayError($e);
        }        
    }

    public function logException(Exception $exception) {
        if(class_exists('__CurrentContext') && __CurrentContext::getInstance() != null) {
            $lion_runtime_directives = __Lion::getInstance()->getRuntimeDirectives();
            //log exception just in case the runtime directive is enabled 
            if($lion_runtime_directives->hasDirective('LOG_EXCEPTIONS') && 
               $lion_runtime_directives->getDirective('LOG_EXCEPTIONS')) {
                if ( $exception instanceof __LionException ) {
                    $exception_desc = $exception->getLocalizedMessage(__CurrentContext::getInstance()->getPropertyContent('DEFAULT_LANG_ISO_CODE'));
                }
                else {
                    $exception_desc = $exception->getMessage();
                }
                $log_message = $exception_desc;
                //log extended info associated to the exception if the runtime directive is enabled:
                if($lion_runtime_directives->hasDirective('LOG_DEBUG_INFO') && 
                   $lion_runtime_directives->getDirective('LOG_DEBUG_INFO')) {
                   $log_message .= "\n<exception>";
                   $log_message .= "\n    <description><![CDATA[" . $exception_desc . "\n]]></description>";
                   
                   $request = __FrontController::getInstance()->getRequest(); 
                   $log_message .= "\n    <request><![CDATA[\n" . print_r($request, true) . "\n]]></request>";
                   $log_message .= "\n    <server><![CDATA[\n" . print_r($_SERVER, true) . "\n]]></server>"; 
                   $log_message .= "\n    <trace><![CDATA[\n" . $exception->getTraceAsString() . "\n]]></trace>";
                   $log_message .= "\n</exception>";
                }
                if($exception->getCode() != 55601) {
                    __LogManager::getInstance()->getLogger('error')->error($log_message);
                }                
            }
        }
    }

}

interface __IErrorPrinter {
    
    public function displayError(Exception $exception);
        
}

class __HtmlErrorPrinter implements __IErrorPrinter {

    public function displayError(Exception $exception) {
        if ( $exception instanceof __LionException ) {
            $error_title = $exception->getErrorTitle();
        }
        else {
            $error_title = 'Core Error';
        }
        $error_message = $exception->getMessage();
        $error_code    = $exception->getCode();

        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            $extended_trace = new __ExtendedTrace($exception);
            $trace_as_html = $extended_trace->toHtml();
        }
        
        if(ob_get_length() !== false) {
            @ob_end_clean();
        }

        if (eregi("MSIE",$_SERVER['HTTP_USER_AGENT'])) {
            $use_mhtml = true;
        }
        else {
            $use_mhtml = false;
        }

        if($use_mhtml){
            //see http://www.w3schools.com/media/media_mimeref.asp for details about Content-type:message/rfc822
            header('Content-type:message/rfc822');
        }
        header('Cache-Control: no-cache');
        header('Cache-Control: no-store');
        header('Cache-Control: private');

        $template = $this->_getTemplate($use_mhtml);
        $template = str_replace('{error_code}', $error_code, $template);
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DISPLAY_EXCEPTIONS')) {
            $template = str_replace('{error_title}', $error_title, $template);
            $template = str_replace('{error_message}', nl2br(htmlentities($error_message)), $template);
        }
        else {
            $template = str_replace('{error_title}', 'Internal Server Error', $template);
            $template = str_replace('{error_message}', 'An internal server error occurred.', $template);
        }
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE') &&
           __Lion::getInstance()->getRuntimeDirectives()->getDirective('DISPLAY_DEBUG_INFO')) {
            $template = str_replace('{error_location_label}', '<p><h3>Trace:</h3>' . $trace_as_html . '</p>', $template);
        }
        else {
            $template = str_replace('{error_location_label}', '<br><br><br>', $template);
        }
        
        echo $template;
        exit;
    }

    private function _getTemplate($use_mhtml = false) {

        $return_value = <<<CODE
<!doctype html public '-//w3c//dtd html 4.01//en' 'http://www.w3.org/tr/html4/strict.dtd'>
<html>
<head>
<style>
body {background:url({error_background_image.jpg}); background-color: #ddd; text-align:center; font-family: verdana, helvetica, arial, sans-serif; font-size: 11px;}
.curvy { font-family: verdana, helvetica, arial, sans-serif; font-size: 11px; align: center; position:relative; width: auto; height: 100%; background:#fff; color:#000; }
.t {background:url({dot_image.gif}) 0 0 repeat-x; width: auto; height: 100%;}
.b {background:url({dot_image.gif}) 0 100% repeat-x; height: 100%;}
.l {background:url({dot_image.gif}) 0 0 repeat-y; height: 100%;}
.r {background:url({dot_image.gif}) 100% 0 repeat-y; height: 100%;}
.bl { z-index: 99;background:url({bl_image.gif}) 0 100% no-repeat; height: 100%; }
.br { z-index: 99;background:url({br_image.gif}) 100% 100% no-repeat; height: 100%;}
.tl { z-index: 99;background:url({tl_image.gif}) 0 0 no-repeat;height: 100%;}
.tr { z-index: 99;background:url({tr_image.gif}) 100% 0 no-repeat;height: 100%;} 
.icon { position: absolute; top: 20px; left: 25px; }
.txt {text-align: left; position:relative; width: auto; height: auto; top:5px; left:5px; padding-top: 5px; padding-bottom: 1em; margin-left:90px; margin-right:90px; color:#000; }
a:link, a:visited, a:hover { text-size: small; color: #888; }
h1 { font-size: 14pt; font-weight: bold; }
.big_title{ font-size: 14pt; font-weight: bold; color: #37170A; font-family: Verdana, Arial, sans-serif; }
</style>
  <title>{error_title}</title>
 </head>
 <body>
   <table id=  "container-table" cellpadding=  "0" cellspacing=  "0" border=  "0" width=  40% height=  300 align=  "center"><tr><td valign=  "top">
   <div class=  "curvy">
   <div class=  "t"><div class=  "b"><div class=  "l"><div class=  "r"><div class=  "bl"><div class=  "br"><div class=  "tl"><div class=  "tr">
    <div class=  "icon"><img src=  "{alert_icon_image.jpg}" alt=  "Error"/></div>
    <div class=  "txt">
     <font class=  "big_title">{error_title}</font>
     <br><img border=  "0" src=  "{title_line_image.gif}" width=  "200" height=  "1"><br>
     <p><h3>Error {error_code}:</h3>{error_message}</p>
     {error_location_label}
     </div>
    </div></div></div></div></div></div></div></div> 
    </div>
    </td></tr></table>    
 </body>
</html>


CODE;

        //Now will embed all images:
        $images   = $this->_getImages();
        if($use_mhtml) {
            $mhtml_header = <<<CODE
MIME-Version: 1.0
Content-Type: multipart/related; boundary="----=_NextPart";

------=_NextPart
Content-Location: local:/
Content-Transfer-Encoding: quoted-printable
Content-Type: text/html; charset="us-ascii"


CODE;
            $return_value = $mhtml_header . $return_value;

            foreach ( $images as $image ) {
                $return_value = str_replace('{' . $image->getName() . '}', $image->getMhtmlUri(), $return_value);
                $return_value .= $image->getMhtmlNextPart();
            }
        }
        else {
            foreach ( $images as $image ) {
                $return_value = str_replace('{' . $image->getName() . '}', $image->getDataUri(), $return_value);
            }
        }

        return $return_value;
    }



    private function _getImages() {
        $return_value = array();
    
        $base64_data = <<<EOD
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
EOD;
        $image = new __Image('alert_icon_image.jpg');
        $image->setMimeType('image/jpg');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;
    
        $base64_data = <<<EOD
R0lGODlhGQAZAMQAALGxsfv7+9nZ2ezs7Pb29vT09MbGxtzc3NDQ0Ovr65ycnMHBwZSUlMLCwqSkpPPz89XV1eLi4ujo6Nvb27Ozs7i4uKGhob29vZeXl6ysrL+/v66urqqqqqioqP7+/v///yH5BAAAAAAALAAAAAAZABkAAAVwIPONZGmeJOahLKusbUw6sBwDgS03kh4fGl/LY6kJTZXHESW4GJejTA5aQhie0A2BWppQsMsFBCz0AAZcUoJTSI8inbY7whmQfQnA2O1ZUAh3OhMbBgGBNggZFw+HMgIVFhoShh6NJx4HDQAOChgMIQA7
EOD;
        $image = new __Image('bl_image.gif');
        $image->setMimeType('image/gif');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        $base64_data = <<<EOD
R0lGODlhGQAZAMQAALGxsfv7+9nZ2ezs7Pb29vT09MbGxtzc3NDQ0Ovr65ycnMHBwZSUlMLCwqSkpPPz89XV1eLi4ujo6Nvb27Ozs7i4uKGhob29vZeXl6ysrL+/v66urqqqqqioqP7+/v///yH5BAAAAAAALAAAAAAZABkAAAVy4CeOZGmOzKmqHra+pKfAtOfQcADgr9TwK80BePJYPETTo5IseS6CJimQkY48BoRVRNhsPx7KZOuBLL4DANJa4CS2hU7E6hlw5tIy4N30ECgLa0QeAQYbY0keDxcZWjgekAESGhYVUTwMGAoOAA0HgjwhADs=
EOD;
        $image = new __Image('br_image.gif');
        $image->setMimeType('image/gif');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        $base64_data = <<<EOD
R0lGODlhAQABAIAAAJSUlAAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==
EOD;
        $image = new __Image('dot_image.gif');
        $image->setMimeType('image/gif');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        $base64_data = <<<EOD
/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAARgAA/+4ADkFkb2JlAGTAAAAAAf/bAIQABAMDAwMDBAMDBAYEAwQGBwUEBAUHCAYGBwYGCAoICQkJCQgKCgwMDAwMCgwMDQ0MDBERERERFBQUFBQUFBQUFAEEBQUIBwgPCgoPFA4ODhQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgXcAAUAwERAAIRAQMRAf/EAFAAAQEBAQEAAAAAAAAAAAAAAAABAgMIAQEAAAAAAAAAAAAAAAAAAAAAEAEBAQAAAAAAAAAAAAAAAAAAERIRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/APYGQAMgAZBYDYJAICgAA0AADoDILAUAFgLkAAGgAAbgIADQGQAWAQGwWAQCAoGQAdAAAAWAQFyCwEyDcAgEBqAQCA1AIBAagEBQWAQCA1AIBAagKCQGoCAsBcgZAyDQANAAAA1kEgKDQJAUGgSAgLAUAAAAGgAAWAoJAUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/2Q==
EOD;
        $image = new __Image('error_background_image.jpg');
        $image->setMimeType('image/jpg');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        $base64_data = <<<EOD
R0lGODlhGQAZAMQAAKenp76+vrm5uZycnJqampeXl5GRkfr6+sbGxrOzs6ysrPHx8ba2tvDw8JOTk+jo6KSkpP39/dPT0/Pz88/Pz+Tk5KKioqGhoczMzOPj48DAwJ+fn/7+/uHh4eLi4v///yH5BAAAAAAALAAAAAAZABkAAAWLYCeKXmmWo4gkwFA4Rtqdp6oQQnVw/CfTpoCF0eB8jsgPkIaAaHbJ6BKVUEyMUelSAqBEsuAl5vLAgrUnzGZxPtMkF3Y7bOoAHnO3KUEx55MmCApff1kmEBOFdAEafopHJRYHj1kZCAyOlBkKDZSGA5mUCAKeWQkVpVEAk6lIoK1IBaGUDrOPBrBIIQA7
EOD;
        $image = new __Image('tl_image.gif');
        $image->setMimeType('image/gif');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        $base64_data = <<<EOD
R0lGODlhGQAZAMQAAKenp7m5uZycnJqampeXl5GRkfr6+qysrMbGxrOzs6SkpMzMzLS0tJOTk/Hx8eTk5KCgoKKioujo6MDAwNPT076+vuPj4/Dw8M/Pz/39/fPz87a2tv7+/uHh4eLi4v///yH5BAAAAAAALAAAAAAZABkAAAWKYNEQAsAgnaeqXeu+7cfNxhMMB7LCfPf9QNllE6l4erCgkmOYKHSr6EpJ5WgOiZR0SqVmMADKVtXtciSQxbhcdqS37DZEHI2XJQAt2V7FJOp8XjlcgUoaCoSFQBwTRh6KVAYRe5CLGwgWlUoXB5maiwKPn0ABCKNADwynPwYAqzICrxwEsg2vHwUhADs=
EOD;
        $image = new __Image('tr_image.gif');
        $image->setMimeType('image/gif');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        $base64_data = <<<EOD
R0lGODlhYwEBAKIAAAAAAP///20+K+zl4////wAAAAAAAAAAACH5BAEAAAQALAAAAABjAQEAAAMVGDrS/jDKSau9OOvNu/9gKI7kOAQJADs=
EOD;
        $image = new __Image('title_line_image.gif');
        $image->setMimeType('image/gif');
        $image->setBase64Data($base64_data);
        $return_value[] = $image;

        return $return_value;

    }

}


class __CommandLineErrorPrinter implements __IErrorPrinter {

    public function displayError(Exception $exception) {
        if ( $exception instanceof __LionException ) {
            $error_title = $exception->getErrorTitle();
        }
        else {
            $error_title = 'Core Error';
        }
        $error_message = $exception->getMessage();
        $error_code    = $exception->getCode();
        $trace_as_string = $exception->getTraceAsString();
        
        echo "$error_title ($error_code):\n$error_message\n";
        echo "============================\n";
        echo "$trace_as_string\n";
        echo "============================\n\n";
        exit;
        
    }

}

class __AsyncMessageErrorPrinter implements __IErrorPrinter {

    public function  displayError(Exception $exception) {
        if ( $exception instanceof __LionException ) {
            $error_title = $exception->getErrorTitle();
        }
        else {
            $error_title = 'Core Error';
        }
        $error_message = $exception->getMessage();
        $error_code    = $exception->getCode();
                
        $message = new __AsyncMessage();
        $message->getHeader()->setStatus(__AsyncMessageHeader::ASYNC_MESSAGE_STATUS_ERROR);
        if(__Lion::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            $message->getHeader()->setMessage( "$error_title ($error_code):\n$error_message" );
        }
        __FrontController::getInstance()->getResponse()->addContent($message->toJson());
        __FrontController::getInstance()->getResponse()->flush();
        exit;
        
    }

}


