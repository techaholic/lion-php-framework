<?php

class __CacheController extends __LionController {

    public function defaultController() {
        return new __ModelAndView('cache');
    }
    
    public function clearAction() {
        __ApplicationContext::getInstance()->getSession()->destroy();
        __ModelProxy::getInstance()->clearCache();
        $mav = new __ModelAndView('confirmation');
        $mav->title = 'Cache cleared!';
        return $mav; 
    }
    
    public function viewAction() {
        
    }
    
}
