<?php
/**
 * This file is part of lion framework.
 * 
 * Copyright (c) 2009 Antonio Parraga Navarro
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
 * @copyright Copyright (c) 2010 Antonio Parraga Navarro
 * @author    Antonio Parraga Navarro
 * @link      http://www.lionframework.org
 * @package   Lion
 * @license   http://www.lionframework.org/license.html
 * @version   1.4.29
 */

__Lion::getInstance()->startup();

/**
 * This is the Lion engine class.
 * 
 * It exposes the {@link startup()} method to create the application context and forward the request flow to the request dispatcher.
 * 
 * It also contains the runtime directives used by Lion.
 *
 */
final class __Lion {

    static private $_instance = null;
    
    private $_started = false;
    private $_runtime_directives = null;
    private $_status = self::STATUS_STOPPED; 
    
    const STATUS_STOPPED = 0;
    const STATUS_LOADING = 1;
    const STATUS_RUNNING = 2;
    
    private function __construct() {
    }
    
    /**
     * Get the singleton instance of __Lion
     *
     * @return __Lion
     */
    final static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __Lion();
        }
        return self::$_instance;
    }
    
    /**
     * Get current runtime directives.
     *
     * @return __RuntimeDirectives
     */
    final public function &getRuntimeDirectives() {
        return $this->_runtime_directives;
    }
    
    final public function getStatus() {
        return $this->_status;
    }
    
    /**
     * Starts the Lion engine. 
     * 
     * This method is called automatically by just including the current file.
     *
     */
    final public function startup() {
        if( $this->_started == false ) {
            $this->_started = true;
            $this->_status  = self::STATUS_LOADING;
            $this->_startupLionCore();
            __ContextManager::getInstance()->createApplicationContext();
            $this->_status  = self::STATUS_RUNNING;
            if(LION_AUTODISPATCH_CLIENT_REQUEST == true) {
                __FrontController::getInstance()->dispatchClientRequest();
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_LION_ENGINE_ALREADY_STARTED');
        }
    }
    
    private function _startupLionCore() {
        
        //Include lion constants:
        include realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Constants.inc';
        
        //Include runtime directives:
        include LION_CORE_DIR . DIRECTORY_SEPARATOR . 'RuntimeDirectives.class.php';
        $this->_runtime_directives = new __RuntimeDirectives();
        
        //Include bootstrap classes:
        include LION_CORE_DIR . DIRECTORY_SEPARATOR . 'FileLocator.class.php';
        include LION_CORE_DIR . DIRECTORY_SEPARATOR . 'FileResolver.class.php';
        include LION_CORE_DIR . DIRECTORY_SEPARATOR . 'ClassLoader.class.php';
        include LION_CACHE_DIR . DIRECTORY_SEPARATOR . 'Cache.class.php';
        include LION_CACHE_DIR . DIRECTORY_SEPARATOR . 'CacheManager.class.php';
        include LION_CACHE_DIR . DIRECTORY_SEPARATOR . 'ICacheHandler.interface.php';
        include LION_CACHE_DIR . DIRECTORY_SEPARATOR . 'CacheHandler.class.php';
        include LION_CACHE_DIR . DIRECTORY_SEPARATOR . 'CacheHandlerFactory.class.php';        
        
        //load framework includepath:
        __ClassLoader::getInstance()->addClassFileLocator(new __ClassFileLocator(LION_DIR));

        include LION_CORE_DIR . DIRECTORY_SEPARATOR . 'ErrorHandling.php';
    }
    
}
