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
 * This is the section handler in charge of processing &lt;view-definitions&gt; configuration sections
 *
 */
class __ViewDefinitionsSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array( 'dynamic_rules' => array(), 
                               'static_rules'  => array() );
        $view_definition_sections = $section->getSections();
        foreach($view_definition_sections as $view_definition_section) {
            $view_definition = new __ViewDefinition();
            $view_definition->setViewCode($view_definition_section->getAttribute('code'));
            $view_definition->setViewClass($view_definition_section->getAttribute('class'));
            $view_definition_subsections = $view_definition_section->getSections();
            foreach($view_definition_subsections as $view_definition_subsection) {
                switch (strtoupper($view_definition_subsection->getName())) {
                    case 'PROPERTY':
                        $property_name  = $view_definition_subsection->getAttribute('name');
                        $property_value = $view_definition_subsection->getAttribute('value');
                        $view_definition->addProperty($property_name, $property_value);
                        break;
                }
            }            
            if(strpos($view_definition->getViewCode(), '*') !== false) {
                $clasify = 'dynamic_rules';
            }
            else {
                $clasify = 'static_rules';
            }
            $return_value[$clasify][strtoupper($view_definition->getViewCode())] =& $view_definition;
            unset($view_definition);
        }
        return $return_value;
    }
    
}