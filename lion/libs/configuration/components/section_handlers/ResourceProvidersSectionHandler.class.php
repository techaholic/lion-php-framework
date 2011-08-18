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
 * This is the section handler in charge of processing &lt;resource-providers&gt; configuration sections
 *
 */
class __ResourceProvidersSectionHandler  extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array( PERSISTENCE_LEVEL_SESSION => array(),
                               PERSISTENCE_LEVEL_ACTION  => array() );        
        $subsections = $section->getSections();
        foreach($subsections as $subsection) {
            switch(strtoupper($subsection->getName())) {
                case 'RESOURCE-PROVIDER':
                    $resource_provider = $this->_loadResourceProvider($subsection);
                    $persistence_level = $resource_provider->getPersistenceLevel();
                    if(!in_array($resource_provider, $return_value[$persistence_level])) {
                        $return_value[$persistence_level][] =& $resource_provider;
                    }
                    unset($resource_provider);
                    break;
            }
        }
        return $return_value;
    }

    private function &_loadResourceProvider(__ConfigurationSection &$section) {
        //Now will load resources provider for current __ResourceDictionary instance:
        $resource_provider_class = $section->getAttribute('class');
		//and now create a new instance of readed class:
		$return_value = new $resource_provider_class();
		switch (strtoupper($section->getAttribute('persistence-level'))) {
		    case 'SESSION':
        		$return_value->setPersistenceLevel(PERSISTENCE_LEVEL_SESSION);
        		break;
		    case 'ACTION':
        		$return_value->setPersistenceLevel(PERSISTENCE_LEVEL_ACTION);
        		break;
		    default:
        		$return_value->setPersistenceLevel($section->getAttribute('persistence-level'));
        		break;
		}
		$return_value->setDescription($section->getAttribute('description'));
        $sub_sections = $section->getSections();
        foreach($sub_sections as &$sub_section) {
            switch(strtoupper($sub_section->getName())) {
                case 'RESOURCES-TYPE':
                    $return_value->setResourceType($sub_section->getAttribute('class'));
                    break;
                case 'PROPERTIES':
                    $properties_sub_sections = $sub_section->getSections();
                    foreach($properties_sub_sections as $property_sub_section) {
                        $property_name  = $property_sub_section->getAttribute('name');
                        $property_value = $property_sub_section->getAttribute('value');
                        $setter = 'set' . ucfirst($property_name);
                        if(method_exists($return_value, $setter)) {
                            $return_value->$setter($property_value);
                        }
                        else {
                            throw new __ConfigurationException('Error found on resource-providers configuration section: Setter not found on ' . get_class($return_value) . ': ' . $setter);
                        }
                    }
                    break;
            }
        }
        return $return_value;
        
    }
    
}
