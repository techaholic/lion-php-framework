<?php

class __LogonController extends __ActionController {

    public function defaultAction()
    {
        $model_and_view = new __ModelAndView('logon');
        $request = __Client::getInstance()->getRequest();

        //Check credentials:
        $login    = $request->getParameter('login');
        $password = $request->getParameter('password');

        $user_identity = new __UsernameIdentity();
        $user_identity->setUsername($login);
        $credentials = new __PasswordCredentials();
        $credentials->setPassword($password);
        try {
            $result_logon = __AuthenticationManager::getInstance()->logon($user_identity, $credentials);
        }
        catch (__SecurityException $e) {
            $result_logon  = false;
            $error_message = $e->getMessage();
        }
        if($result_logon == false) {
            //Now will include smarty as ORS template engine:
            if($error_message == '') {
                $error_message = __ResourceManager::getInstance()->getResource('ERR_LOGON_ERROR')->getValue();
            }
            $model_and_view->errorMsg = $error_message;
        }
        else {
            if($request->getParameter('destination_page')) {
                $model_and_view->redirectPage = $request->GetParameter('destination_page');
            }
            else {
                $model_and_view->redirectPage = __UriFactory::getInstance()->createUri()->setActionCode('index')->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_LION_ADMIN_AREA'), 1)->getUrl();
            }
        }
        //Return the view code to use:
        return $model_and_view;
    }

}
