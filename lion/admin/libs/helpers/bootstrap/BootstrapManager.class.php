<?php

class __BootstrapManager {

    private $_app_template_dir    = null;
    private $_bootstrap_variables = null;
    
    private $_minimum_environment_files = array('.htaccess', 'index.php', 'var');
    
    public function __construct() {
        $this->_app_template_dir = LION_DIR . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app_template';
        if(__Client::getInstance()->getRequestType() == REQUEST_TYPE_COMMAND_LINE) {
            $rewrite_base_directive = '#RewriteBase /base/url/to/your/application';
        }
        else {
            $rewrite_base_directive = 'RewriteBase ' . dirname($_SERVER['SCRIPT_NAME']);
        }
        $this->_bootstrap_variables = array (
                               'REWRITE_BASE_DIRECTIVE' => $rewrite_base_directive,
                               'LIONDIR'  => LION_DIR,
                               'LIONFILE' => LION_DIR . DIRECTORY_SEPARATOR . 'lion.php'
                               );
    }
    
    /**
     * This method do a bootstrap of a new Lion application on the specified location
     *
     * @param string $bootstrap_location where to do the bootstrap
     */
    public function doBootstrap($bootstrap_location) {
        $return_value = false; //by default
        if($this->_validateEnvironment() && $this->_validateBootstrapLocation($bootstrap_location)) {
            try {
                $this->_copy_tree($this->_app_template_dir, $bootstrap_location);
                if(mkdir($bootstrap_location . DIRECTORY_SEPARATOR . 'forms', 0755 ) === false) {
                    throw __ExceptionFactory::getInstance()->createException('The directory ' . $bootstrap_location . DIRECTORY_SEPARATOR . 'forms is not writable. Please fix the permissions in order to perform the bootstrap.');
                }
                $this->_copy_tree(LION_DIR . DIRECTORY_SEPARATOR . 'forms', $bootstrap_location . DIRECTORY_SEPARATOR . 'forms');
                $this->_createSettingsFile($bootstrap_location);
                //final steps: delete the bootstrap file
                unlink($bootstrap_location . DIRECTORY_SEPARATOR . 'bootstrap.php');
                //clear the session + the cache:
                __ApplicationContext::getInstance()->getSession()->clear();
                __ApplicationContext::getInstance()->getCache()->clear();
                $return_value = true;
            }
            catch (Exception $e) {
                //if any exception is raised, will rollback the bootstrap and
                $this->_rollbackBootstrap($bootstrap_location);
                //re
                throw $e;
            }
        }
        return $return_value;
    }

    private function _copy_tree( $source, $target ) {
        $permissions = array();
        if ( is_dir( $source )) {
            if(!file_exists( $target )) {
                if(mkdir( $target, 0755 ) === false) {
                    throw __ExceptionFactory::getInstance()->createException('The directory ' . $target . DIRECTORY_SEPARATOR . $entry . ' is not writable. Please fix the permissions in order to perform the bootstrap.');
                }
            }
            $d = dir( $source );
            while ( false !== ( $entry = $d->read() ) ) {
                if ( $entry != '.' && $entry != '..' ) {
                    if ( is_dir( $source . DIRECTORY_SEPARATOR . $entry ) ) {
                        if(!file_exists( $target . DIRECTORY_SEPARATOR . $entry ) && mkdir( $target . DIRECTORY_SEPARATOR . $entry, 0755 ) === false) {
                            throw __ExceptionFactory::getInstance()->createException('The directory ' . $target . DIRECTORY_SEPARATOR . $entry . ' is not writable. Please fix the permissions in order to perform the bootstrap.');
                        }
                        $this->_copy_tree( $source . DIRECTORY_SEPARATOR . $entry, $target . DIRECTORY_SEPARATOR . $entry );
                        continue;
                    }
                    else {
                        if( preg_match('/^mode\.(\d+)$/', $entry, $permissions ) ) {
                            chmod($target, intval($permissions[1], 8));
                        }
                        else if ($entry != 'create.me') {
                            $this->_copy_file($source . DIRECTORY_SEPARATOR . $entry, $target . DIRECTORY_SEPARATOR . $entry);
                        }
                    }
                }
            }
            $d->close();
        }
        else if (is_file($source)) {
            $this->_copy_file($source, $target);
        }
    }
    
    private function _copy_file($source, $target) {
        if(is_file($source)) {
            $file = basename($source);
            $basedir = dirname($source);
            if( copy( $source, $target ) === false ) {
                throw __ExceptionFactory::getInstance()->createException('Error while copying the ' . $file . ' file to the target directory while bootstraping the application.');
            }
            else {
                $this->_fillVariables($target);
            }
        }
    }
    
    private function _createSettingsFile($bootstrap_location) {
        if( copy( LION_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'settings.ini', $bootstrap_location . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.ini' ) === false ) {
            throw __ExceptionFactory::getInstance()->createException('Error trying to create the settings.ini file in the target directory while bootstraping the application.');
        }
        else {
            $this->_fillVariables($bootstrap_location . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.ini');
        }
    }
    
    private function _fillVariables($target_file) {
        $file_contents = file_get_contents($target_file);
        foreach($this->_bootstrap_variables as $variable_name => $variable_value) {
            $file_contents = str_replace('[' . $variable_name . ']', $variable_value, $file_contents);
        }
        if(basename($target_file) == 'settings.ini') {
            $file_contents = preg_replace('/JS_LIB_DIR\s*\=\s*\"[^\"]*\"/', 'JS_LIB_DIR            = "forms"', $file_contents);
            $file_contents = preg_replace('/APPLICATION_DOMAIN\s*\=\s*\"[^\"]*\"/', 'APPLICATION_DOMAIN = "' . $_SERVER['SERVER_NAME'] . '"', $file_contents);
        }
        $fh = fopen($target_file, "w");
        if($fh !== false) {
            fwrite($fh, $file_contents);
            fclose($fh);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Error trying to configure the ' . $target_file . ' file.');
        }
    }
    
    private function _rollbackBootstrap($boostrap_location) {
        $d = dir( $boostrap_location );
        while ( false !== ( $entry = $d->read() ) ) {
            if ( $entry != '.' && $entry != '..' && $entry != 'bootstrap.php') {
                unlink($entry);
            }
        }
        $d->close();
    }
    
    /**
     * Validates lion framework environment requirements and add the .htaccess to
     * help redirectiong the resource route to the framework during the bootstrapping process
     *
     * @param string $bootstrap_location
     * @return bool true if success
     */
    public function prepareAndValidateBootstrapEnvironment($bootstrap_location) {
        $return_value = false; //by default
        if($this->_validateEnvironment() && $this->_validateBootstrapLocation($bootstrap_location)) {
            try {
                $this->_prepareEnvironment($bootstrap_location);
                //will check mod_rewrite validations
                $this->_validateRewriteEngine();
                $return_value = true;
            }
            catch (Exception $e) {
                $this->_rollbackBootstrap($bootstrap_location);
                throw $e;
            }
        }
        return $return_value;         
    }
    
    private function _prepareEnvironment($bootstrap_location) {
        foreach($this->_minimum_environment_files as $file) {
            $source_file = $this->_app_template_dir . DIRECTORY_SEPARATOR . $file;
            $target_file = $bootstrap_location . DIRECTORY_SEPARATOR .  $file;
            $this->_copy_tree($source_file, $target_file);
        }
    }

    private function _validateRewriteEngine() {
        $return_value = false;
        if(__Client::getInstance()->getRequestType() != REQUEST_TYPE_COMMAND_LINE) {
            $test_url = __UriFactory::getInstance()->createUri()->setRoute('lion')->setController('bootstrap')->setAction('test')->getUrl();
            $test_url = __UrlHelper::resolveUrl($test_url, 'http://' . $_SERVER['SERVER_NAME']);
            if ($stream = @fopen($test_url, 'r')) {
                // print all the page starting at the offset 10
                $test_content = stream_get_contents($stream);
                fclose($stream);
                if($test_content == 'OK') {
                    $return_value = true;
                }
            }
            if($return_value == false) {
                throw __ExceptionFactory::getInstance()->createException('Mod Rewrite is not enabled in your server or is not well configured.');
            }
        }
        return $return_value;
    }
    
    /**
     * Validates the environment before perform the bootstrap.
     * 
     * - validates that mod_rewrite is enabled
     * - validates that php-domxml extenssion is disabled
     *
     * @return bool true if the environment is valid to perform the bootstrap
     */
    private function _validateEnvironment() {
        if(function_exists('apache_get_modules')) {
            $apache_modules = apache_get_modules();
            if(is_array($apache_modules) && count($apache_modules) > 0) {
                if(!in_array('mod_rewrite', $apache_modules)) {
                    throw __ExceptionFactory::getInstance()->createException('Error on Apache configuration: Mod Rewrite needs to be enabled.');
                }
            }
        }
        $php_extensions = get_loaded_extensions();
        if(in_array('domxml', $php_extensions) || in_array('php_domxml', $php_extensions)) {
            throw __ExceptionFactory::getInstance()->createException('php_domxml extension detected and need to be disabled.');
        }
        return true;
    }
    
    private function _validateBootstrapLocation($bootstrap_location) {
        //check if it's a directory:
        if(!is_dir($bootstrap_location) || !is_readable($bootstrap_location)) {
            throw __ExceptionFactory::getInstance()->createException('The specified location for bootstrapping the new application (' . $bootstrap_location . ') is not a valid directory or has not the appropriate permissions. Please ensure that the target is a writable directory.');
        }
        //check if the bootstrap file already exists
        if(!is_file($bootstrap_location . DIRECTORY_SEPARATOR . 'bootstrap.php')) {
            throw __ExceptionFactory::getInstance()->createException('The bootstrap.php file has not been found in the ' . $bootstrap_location . ' directory and it\'s required to do the bootstrap.');
        }
        //check if the target directory just contains the bootstrap.php file:
        $directory_to_validate = dir($bootstrap_location);
        while (false !== ($file_in_directory_to_validate = $directory_to_validate->read())) {
            if($file_in_directory_to_validate != '.' &&
            $file_in_directory_to_validate != '..' &&
            $file_in_directory_to_validate != 'bootstrap.php' &&
            !in_array($file_in_directory_to_validate, $this->_minimum_environment_files)) {
                throw __ExceptionFactory::getInstance()->createException('Can not perform the bootstrap: to bootstrap a new application, the target directory must contain just the bootstrap.php file.');
            }
        }
        return true;
    }
    
    
    
}