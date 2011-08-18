<?php

class IndexController extends __ActionController {

    public function defaultAction() {
        return new __ModelAndView('index');
    }

}