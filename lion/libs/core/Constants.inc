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

if(!defined('LION_AUTODISPATCH_CLIENT_REQUEST')) {
    define('LION_AUTODISPATCH_CLIENT_REQUEST', true);
}

//Define the application base path:
if(!defined('APP_DIR')) {
    define ('APP_DIR', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));
}

//Define the framework base dir as two levels up from this script ( "/libs/startup" -> "/libs", and "/libs" -> "/"):
define ('LION_DIR', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));

if(!defined('ADMIN_DIR')) {
    define ('ADMIN_DIR', LION_DIR . DIRECTORY_SEPARATOR . 'admin');
}

//Framework version:
define ('LION_VERSION_NUMBER',      '1.4.29');
define ('LION_VERSION_BUILD_DATE',  '2011/01/27 02:27:49');
define ('LION_VERSION_CHANGE_LIST', '256');

//DEFAULT_CONFIGURATION_DIR is the one within the framework directory as /config/default/...
define ('DEFAULT_CONFIGURATION_DIR', LION_DIR . DIRECTORY_SEPARATOR . 
                                     'libs'    . DIRECTORY_SEPARATOR . 
                                     'configuration' . DIRECTORY_SEPARATOR . 
                                     'defaultvalues');
//Directory where common language strings are located on:
define ('FW_LANGUAGE_DIR',     LION_DIR . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'language');

//Define paths:
define('LION_LIBS_DIR', LION_DIR . DIRECTORY_SEPARATOR . 'libs');
define('LION_CORE_DIR', LION_LIBS_DIR . DIRECTORY_SEPARATOR . 'core');
define('LION_CACHE_DIR', LION_LIBS_DIR . DIRECTORY_SEPARATOR . 'cache');

//REQMETHOD_ALL represent that all request methods are valid for a request, so, it will be the addition of all request method values:
define('REQMETHOD_NONE',        0x00000000);
define('REQMETHOD_GET',         0x00000001);
define('REQMETHOD_POST',        0x00000010);
define('REQMETHOD_PUT',         0x00000100);
define('REQMETHOD_DELETE',      0x00001000);
define('REQMETHOD_HEAD',        0x00010000);
define('REQMETHOD_COMMAND_LINE',0x00100000);
define('REQMETHOD_ALL',         0x11111111);

//Request types
define('REQUEST_TYPE_HTTP', 1);
define('REQUEST_TYPE_COMMAND_LINE', 2);
define('REQUEST_TYPE_XMLHTTP', 3);

//SERVER PORTS:
define('DEFAULT_HTTP_PORT',   80);
define('DEFAULT_HTTPS_PORT', 443);

//The server http protocol and port used:
if(isset($_SERVER['HTTPS'])) {
    define ('HTTP_PROTOCOL', 'https');
    if(!defined('HTTP_PORT') && key_exists('SERVER_PORT', $_SERVER) && $_SERVER['SERVER_PORT'] != DEFAULT_HTTPS_PORT) {
        define ('HTTP_PORT', ':' . $_SERVER['SERVER_PORT']);
    }
}
else {
    define ('HTTP_PROTOCOL', 'http');
    if(!defined('HTTP_PORT') && key_exists('SERVER_PORT', $_SERVER) && $_SERVER['SERVER_PORT'] != DEFAULT_HTTP_PORT) {
        define ('HTTP_PORT', ':' . $_SERVER['SERVER_PORT']);
    }
}
if(!defined('HTTP_PORT')) {
	define('HTTP_PORT', '');
}

if(!empty($_SERVER['REQUEST_URI'])) {
    //Define the application base URL:
    $app_url_path = dirname($_SERVER['PHP_SELF']);
    //fix the dirname to use the windows directory separator: 
    if(DIRECTORY_SEPARATOR != '/') {
        $app_url_path = str_replace(DIRECTORY_SEPARATOR, '/', $app_url_path);
    }
    define ('APP_URL_PATH', $app_url_path);
}

//EVENT_TYPES:
//This event is raised when any system event is raised
define('EVENT_ON_ANY_EVENT',     -1);
//This event is raised when the i18n main instance has changed the locale configuration
define('EVENT_ON_LOCALE_CHANGE',  1);
//This event is raised when a request has been started
define('EVENT_ON_REQUEST_START',  4);
//This event is raised when a request has been ended
define('EVENT_ON_REQUEST_FINISH', 5);
//This event is raised when a session has been started
define('EVENT_ON_SESSION_START',  6);
//This event is raised when an user has performed a logon successfully
define('EVENT_ON_USER_LOGON',     7);
//This event is raised when an user has performed a logout successfully
define('EVENT_ON_USER_LOGOUT',    8);
//This event is raised everytime a System Resource is created
define('EVENT_ON_SYSTEM_RESOURCE_CREATE', 9);
//This event is raised whenever an exception is captured by the Lion exception handler method
define('EVENT_ON_EXCEPTION', 10);
//Raised when a required permission is assigned to a system resource:
define('EVENT_ON_REQUIRED_PERMISSION_ASSIGNMENT', 11);

//PERSISTENCE_LEVELS:
define('PERSISTENCE_LEVEL_SESSION', "session");
define('PERSISTENCE_LEVEL_REQUEST', "request");
define('PERSISTENCE_LEVEL_ACTION',  "action");

define('CONFIGURATION_TYPE_XML', 'XML');
define('CONFIGURATION_TYPE_INI', 'INI');
