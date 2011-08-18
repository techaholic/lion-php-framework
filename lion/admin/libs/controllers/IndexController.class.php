<?php

class __IndexController extends __LionController {
	
    public function defaultAction()
    {
        return new __ModelAndView('index');
    }
}
