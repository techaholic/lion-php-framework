<?php

class __BootstrapController extends __ActionController {
    
    private $_bootstrap_successful = false;
    
    public function defaultAction()
	{
        if( __ModelProxy::getInstance()->prepareAndValidateBootstrapEnvironment(APP_DIR) ) {	
            $uri = __UriFactory::getInstance()->createUri()->setRoute('bootstrap')->setController('bootstrap')->setAction('form');
            __FrontController::getInstance()->forward($uri);
        }        
	}
	
	public function formAction() {
        $lion_admin_area_url = __UriFactory::getInstance()->createUri()
                                     ->setControllerCode('index')
                                     ->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_LION_ADMIN_AREA'), 1)
                                     ->getUrl();
        
        $mav = new __ModelAndView('bootstrap');
        $mav->welcome_message = __ResourceManager::getInstance()
                                           ->getResource('WELCOME_MESSAGE')
                                           ->setParameters(array(
                                               APP_DIR, 
                                               $lion_admin_area_url,
                                               APP_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.ini',
                                               __ApplicationContext::getInstance()->getPropertyContent('APP_NAME'),
                                               LION_VERSION_NUMBER
                                               ));
        return $mav;
	}
    
	public function successAction() {
        $lion_admin_area_url = __UriFactory::getInstance()->createUri()
                                     ->setControllerCode('index')
                                     ->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_LION_ADMIN_AREA'), 1)
                                     ->getUrl();
        
        $mav = new __ModelAndView('bootstrapSucess');
        $mav->welcome_message = __ResourceManager::getInstance()
                                           ->getResource('WELCOME_MESSAGE')
                                           ->setParameters(array(
                                               APP_DIR, 
                                               $lion_admin_area_url,
                                               APP_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.ini',
                                               __ApplicationContext::getInstance()->getPropertyContent('APP_NAME'),
                                               LION_VERSION_NUMBER
                                               ));
        return $mav;
	}

	/**
	 * This action just output an "OK".
	 * It's used by the bootstrap environment validation process in order to check that
	 * rewrite engine is working as expected.
	 *
	 */
    public function testAction() {
        echo 'OK';
        exit;
    }   
	
	public function postExecute()
    {
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('decorator', 'footer'));
    }  	
	
}