<?php

class __NotifierController extends __ActionController {
    
    public function defaultAction()
	{
    	$model_and_view = new __ModelAndView('notifier');
    	
        $url = basename(__UriFactory::getInstance()->createUri()->setController('index')->getUrl());
        $parameters = array('default_url' => $url );
    	
    	$request = __ActionDispatcher::getInstance()->getRequest();
    	if($request->hasParameter('notification_title')) {
    	    $notification_title = $request->getParameter('notification_title');
            $model_and_view->notification_title = __ResourceManager::getInstance()->getResource($notification_title)->setParameters($parameters)->getValue();
    	}
    	if($request->hasParameter('notification_description')) {
            $notification_description = $request->getParameter('notification_description');
            $model_and_view->notification_description = __ResourceManager::getInstance()->getResource($notification_description)->setParameters($parameters)->getValue();
    	}
    	
    	return $model_and_view;
	}
    
	public function postExecute()
    {
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('decorator', 'footer'));
    }  	
	
}