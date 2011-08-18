<?php
/**
 * This file is part of lion framework.
 * 
 * Copyright (c) 2011 Antonio Parraga Navarro
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 *
 * @copyright  Copyright (c) 2011 Antonio Parraga Navarro
 * @author     Antonio Parraga Navarro
 * @link       http://www.lionframework.org
 * @license    http://www.lionframework.org/license.html
 * @version    1.4
 * @package    Configuration
 * 
 */

/**
 * This is the section handler in charge of processing &lt;controller-definitions&gt; configuration sections
 *
 */
class __ControllerDefinitionsSectionHandler extends __CacheSectionHandler {
    
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array( 'dynamic_rules' => array(), 
                               'static_rules'  => array() );
        $controller_definition_sections = $section->getSections();
        foreach($controller_definition_sections as $controller_definition_section) {
            $controller_definition = new __ActionControllerDefinition();
            $controller_definition->setCode($controller_definition_section->getAttribute('code'));
            $controller_definition->setClass($controller_definition_section->getAttribute('class'));
            $controller_definition_subsections = $controller_definition_section->getSections();
            foreach($controller_definition_subsections as $controller_definition_subsection) {
                switch (strtoupper($controller_definition_subsection->getName())) {
                    case 'PROPERTY':
                        switch (strtoupper($controller_definition_subsection->getAttribute('name'))) {
                            case 'HISTORIABLE':
                                $controller_definition->setHistoriable($controller_definition_subsection->getAttribute('value'));
                                break;
                            case 'REQUESTABLE':
                                $controller_definition->setRequestable($controller_definition_subsection->getAttribute('value'));
                                break;
                            case 'VALIDREQUESTMETHOD':
                                $valid_request_method = $controller_definition_subsection->getAttribute('value');
                                if(!is_numeric($valid_request_method)) {
                                    if(is_string($valid_request_method) && strpos($valid_request_method, 'REQMETHOD_') !== false && defined($valid_request_method)) {
                                        $valid_request_method = constant($valid_request_method);
                                    }
                                    else {
                                        //raise an exception here, please!!!!
                                    }
                                }
                                $controller_definition->setValidRequestMethod($valid_request_method);
                                break;
                            case 'REQUIRESSL':
                                $controller_definition->setRequireSsl($controller_definition_subsection->getAttribute('value'));
                                break;
                        }
                        break;
                    case 'PERMISSION':
                        $controller_definition->setRequiredPermission($controller_definition_subsection->getAttribute('id'));
                        break;
                    case 'I18N-RESOURCES':
                        $controller_definition->setI18nResourceGroups($this->_getResourceGroups($controller_definition_subsection));
                }
            }
            if(strpos($controller_definition->getCode(), '*') !== false) {
                $clasify = 'dynamic_rules';
            }
            else {
                $clasify = 'static_rules';
            }
            $return_value[$clasify][strtoupper($controller_definition->getCode())] =& $controller_definition;
            unset($controller_definition);
        }
        return $return_value;
    }
    
    protected function _getResourceGroups(__ConfigurationSection &$section) {
        $return_value = array();
        $i18n_resources_sections =& $section->getSections();
        foreach($i18n_resources_sections as &$i18n_resources_section) {
            if(strtoupper($i18n_resources_section->getName()) == 'RESOURCES-GROUP') {
                $return_value[] = $i18n_resources_section->getAttribute('id');
            }
        }
        return $return_value;        
    }
    
}