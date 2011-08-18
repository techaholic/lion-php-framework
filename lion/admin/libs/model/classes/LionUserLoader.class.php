<?php

class __LionUserLoader extends __UserLoader {
    
    public function &loadUser(__IUserIdentity $user_identity) {
        $return_value = null;
        if( $user_identity instanceof __AnonymousIdentity ) {
            return $this->loadAnonymousUser();
        }
        else if( $user_identity instanceof __UsernameIdentity ) {
            $login = $user_identity->getUsername();
            if($login == __ApplicationContext::getInstance()->getPropertyContent('LION_ADMIN_LOGIN')) {
                $return_value = new __User();
                $credentials  = new __PasswordCredentials();
                $credentials->setPassword(__ApplicationContext::getInstance()->getPropertyContent('LION_ADMIN_PASSWORD'));
                $return_value->setCredentials($credentials);
                $return_value->addRole(__RoleManager::getInstance()->getRole('admin'));
            }
        }
        return $return_value;
    }
    
    public function &loadAnonymousUser() {
        $return_value = new __User();
        //If there is not need to authenticate the user, will set the admin role:
        if( __ContextManager::getInstance()->getContext('LION_ADMIN_AREA')->getPropertyContent('LION_ADMIN_AUTH_REQUIRED') == false) {
            $admin_role = __RoleManager::getInstance()->getRole('ADMIN');
            $return_value->addRole($admin_role);
        }
        $return_value->setCredentials(new __AnonymousCredentials());
        return $return_value;
    }
    
}