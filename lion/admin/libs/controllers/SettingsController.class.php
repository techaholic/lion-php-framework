<?php

class __SettingsController extends __LionController {

    public function defaultAction() {
        
        $mav = new __ModelAndView('settings');
        try {
            $mav->lion_version = LION_VERSION_NUMBER;
            $mav->lion_build_date = LION_VERSION_BUILD_DATE;
            $mav->lion_build_changelist = LION_VERSION_CHANGE_LIST;
            
            $configuration = __ApplicationContext::getInstance()->getConfiguration();
            $settings = $configuration->getSettings();
            $setting_values = array();
            foreach($settings as $key => $setting) {
                $value = $configuration->getPropertyContent($key);
                if(is_bool($value)) {
                    if($value) {
                        $value = 'true';
                    }
                    else {
                        $value = 'false';
                    }
                }
                $setting_values[] = array('name' => $key, 'value' => $value);
            }
            $mav->settings = $setting_values;
            
            $lion_directives = __Lion::getInstance()->getRuntimeDirectives()->getDirectives();
            $runtime_directives_values = array();
            foreach($lion_directives as $key => $value) {
                if(is_bool($value)) {
                    if($value) {
                        $value = 'true';
                    }
                    else {
                        $value = 'false';
                    }
                }                
                $runtime_directives_values[] = array('name' => $key, 'value' => $value);
            }
            $mav->runtime_directives = $runtime_directives_values;
        }
        catch (Exception $e) {
            $mav->status = 'ERROR';
        }
        return $mav;
    }

    
    
}
        
 