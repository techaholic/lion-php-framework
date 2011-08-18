<?php

class __CacheServices {
    
    public function clearCache() {
        __ApplicationContext::getInstance()->getCache()->clear();
        return true;
    }
    
    
}