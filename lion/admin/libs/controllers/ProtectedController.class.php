<?php

abstract class __ProtectedController extends __ActionController {

    public function onAccessError() {
        if( __ApplicationContext::getInstance()->getPropertyContent('LION_ADMIN_AUTH_REQUIRED') == true ) {
            //logout the user:
            __AuthenticationManager::getInstance()->logout();
            
            $uri = __UriFactory::getInstance()->createUri()->setRoute('lion')->setController('login');
            __FrontController::getInstance()->forward($uri);
        }
        //Else, it's an unknow profile error: redirect to the error page:
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_ACTION_PERMISSION_ERROR', array('action_code' => $this->getCode()));
        }
    }    
    
}
