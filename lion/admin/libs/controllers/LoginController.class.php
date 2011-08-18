<?php

class __LoginController extends __ActionController {

    public function defaultAction()
    {
    	$model_and_view = new __ModelAndView('login');
    	$welcome_to_lion = __ResourceManager::getInstance()->getResource('WELCOME_TO_LION_LABEL')
    	                                     ->setParameters(array(__ApplicationContext::getInstance()->getPropertyContent('APP_NAME')))->getValue();
        $model_and_view->welcome_to_lion = $welcome_to_lion;
        return $model_and_view;
    }

	public function postExecute()
    {
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('decorator', 'footerI18n'));
    }      
       
}