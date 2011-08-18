<?php

class __LogoutController extends __ActionController {

    public function defaultAction()
    {
        //Will force a logout in the user:
        __AuthenticationManager::getInstance()->logout();
        //Now will process the index action (will render the login page)
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('index'));
        //No views will be returned by this action:
        return null;
    }
    
}
