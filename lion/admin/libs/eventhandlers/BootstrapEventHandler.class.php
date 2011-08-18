<?php

class __BootstrapEventHandler extends __EventHandler {

    public function bootstrap_form_submit(__UIEvent &$event) {
        if(__ModelProxy::getInstance()->doBootstrap(APP_DIR)) {
            $uri = __UriFactory::getInstance()->createUri()->setRoute('lion')->setController('bootstrap')->setAction('success');
            __FrontController::getInstance()->redirect($uri);
        }
    }
    
}
