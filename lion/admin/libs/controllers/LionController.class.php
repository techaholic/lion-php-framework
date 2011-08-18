<?php

class __LionController extends __ProtectedController {
    
	public function preExecute()
    {
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('decorator', 'header'));
    }

	public function postExecute()
    {
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('decorator', 'footerI18n'));
    }
    
}
