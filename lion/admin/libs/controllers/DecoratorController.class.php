<?php

class __DecoratorController extends __ActionController {

    public function headerAction()
	{
    	$application_in_edition = __ResourceManager::getInstance()->getResource('APPLICATION_IN_EDITION')->setParameters(array('app_name' => __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('APP_NAME')));
	    $model_and_view  = new __ModelAndView('header');
    	$model_and_view->application_in_edition = $application_in_edition;
		if( __ApplicationContext::getInstance()->getPropertyContent('LION_ADMIN_AUTH_REQUIRED') == true ) {
		   	$model_and_view->logout_access = true;
		}	
		else {	
		   	$model_and_view->logout_access = false;
		}
		return $model_and_view;
	}
    
    public function footerAction()
    {
    	$model_and_view = new __ModelAndView('footer');
    	$model_and_view->lion_version_number = LION_VERSION_NUMBER;
    	$model_and_view->lion_version_build_date = LION_VERSION_BUILD_DATE;
    	$model_and_view->lion_version_change_list = LION_VERSION_CHANGE_LIST;
    	$model_and_view->language = __i18n::getInstance()->getLastLocale()->getLanguageIsoCode();
        return $model_and_view;
    }  
    
    public function footerI18nAction()
    {
        $model_and_view = $this->footerAction();
        $model_and_view->I18n_enabled = true;    	
        return $model_and_view;
    }     
    
}