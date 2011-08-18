<?php

class Controller_Index extends __ActionController {

    public function action_default() {
        return new __ModelAndView('index');
    }

}
?>