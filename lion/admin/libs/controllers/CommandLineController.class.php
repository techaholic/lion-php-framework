<?php

/**
 * This is the default controller for command line requests.
 * It's executed in case no controllers have been specified on command line
 *
 */
class __CommandLineController extends __ActionController {
    
    protected function _readCommandLineRequest() {
        $args = array('clearcache');
        $short_options = array();
        $long_options  = array('clearcache');
        $console_getopt = new Console_Getopt($args, $short_options, $long_options);
        $parameters = $console_getopt->readPHPArgv();
    }
    
    public function defaultAction()	{
	    //handle special bootstrap command:
	    if(basename($_SERVER['SCRIPT_FILENAME']) == 'bootstrap.php') {
	        if(__ModelProxy::getInstance()->doBootstrap(APP_DIR)) {
	           echo "Bootstrap completed!\n";
  	        }
	    }
	    else if(__FrontController::getInstance()->getRequest()->hasParameter('clearcache')) {
	        if(__ModelProxy::getInstance()->clearCache()) {
                echo "Cache cleared!\n";
	        }
	    }
        else if(__FrontController::getInstance()->getRequest()->hasParameter('lioninfo')) {
            $this->_printLionInfo();
        }
	    else {
	        $this->_printUsage();
	    }
	    
	}
	
	private function _printUsage() {
	    echo 'Lion framework ' . LION_VERSION_NUMBER . ' (built: ' . LION_VERSION_BUILD_DATE . ")\n";
	}
	
    private function _printLionInfo() {
        echo 'Lion framework ' . LION_VERSION_NUMBER . ' (built: ' . LION_VERSION_BUILD_DATE . ")\n";
        echo "\n";
        echo "Runtime Directives\n";
        echo "------------------\n";
        $lion_directives = __Lion::getInstance()->getRuntimeDirectives()->getDirectives();
        $runtime_directives_values = array();
        foreach($lion_directives as $key => $value) {
            if(is_bool($value)) {
                if($value) {
                    $value = 'true';
                }
                else {
                    $value = 'false';
                }
            }
            echo "$key: $value\n";
        }
        echo "\nApplication Settings\n";
        echo "--------------------\n";
        $configuration = __ApplicationContext::getInstance()->getConfiguration();
        $settings = $configuration->getSettings();
        $setting_values = array();
        foreach($settings as $key => $setting) {
            $value = $configuration->getPropertyContent($key);
            if(is_bool($value)) {
                if($value) {
                    $value = 'true';
                }
                else {
                    $value = 'false';
                }
            }
            
            echo "$key: $value\n";
        }
        
    }	
    
}