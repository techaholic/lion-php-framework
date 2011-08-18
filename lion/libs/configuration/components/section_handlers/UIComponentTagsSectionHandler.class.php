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
 * This is the section handler in charge of processing &lt;ui-component-tags&gt; configuration sections
 *
 */
class __UIComponentTagsSectionHandler extends __CacheSectionHandler {

    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array();
        $ui_component_tag_sections = $section->getSections();
        foreach($ui_component_tag_sections as &$ui_component_tag_section) {
            $tag_name = $ui_component_tag_section->getAttribute('tag-name');
            $component_class = $ui_component_tag_section->getAttribute('component-class');
            $component_tag = new __ComponentTag($tag_name);
            $component_tag->setComponentClass($component_class);
            $component_writer_sections = $ui_component_tag_section->getSections();
            foreach($component_writer_sections as $component_writer_section) {
                $component_section_name = strtoupper($component_writer_section->getName());
                switch($component_section_name) {
                    case 'UI-COMPONENT-WRITER':
                        $client   = $component_writer_section->getAttribute('client');
                        $component_writer_class = $component_writer_section->getAttribute('class');
                        $component_writer_subsections = $component_writer_section->getSections();
                        $component_writer_decorator_classes = array();
                        foreach($component_writer_subsections as $component_writer_subsection) {
                            $section_name = strtoupper($component_writer_subsection->getName());
                            switch($section_name) {
                                case 'DECORATORS':
                                    $decorator_sections = $component_writer_subsection->getSections();
                                    $next_order = 10000;
                                    foreach($decorator_sections as $decorator_section) {
                                        if($decorator_section->hasAttribute('order')) {
                                            $order = $decorator_section->getAttribute('order');
                                        }
                                        else {
                                            $order = $next_order;
                                            $next_order++;
                                        }
                                        $component_writer_decorator_classes[$order] = $decorator_section->getAttribute('class');
                                    }
                                    break;

                                case 'VIEW':
                                    $view_definition = new __ViewDefinition();
                                    $view_definition->setViewClass($component_writer_subsection->getAttribute('class'));
                                    $view_definition_subsections = $component_writer_subsection->getSections();
                                    foreach($view_definition_subsections as $view_definition_subsection) {
                                        switch (strtoupper($view_definition_subsection->getName())) {
                                            case 'PROPERTY':
                                                $property_name  = $view_definition_subsection->getAttribute('name');
                                                $property_value = $view_definition_subsection->getAttribute('value');
                                                $view_definition->addProperty($property_name, $property_value);
                                                break;
                                        }
                                    }
                                    $component_tag->addViewDefinition($client,$view_definition); 
                                    unset($view_definition);
                                    break;
                            }
                        }
                        $component_tag->addComponentWriterClass($client, $component_writer_class, $component_writer_decorator_classes);
                        break;
            
                    case 'UI-COMPONENT-INTERFACE':
                        $ui_component_interface = $this->_getComponentInterfaceSpec($component_writer_section);
                        $component_tag->setComponentInterfaceSpec($ui_component_interface);
                        break;
                }
            }
            $return_value[$tag_name] = $component_tag;
            unset($component_tag);
        }
        return $return_value;
    }
    
    private function &_getComponentInterfaceSpec(__ConfigurationSection $comp_properties_map_section) {
        $return_value = new __UICompositeComponentInterfaceSpec();
        $subsections = $comp_properties_map_section->getSections();
        foreach($subsections as $subsection) {
            $section_name = strtoupper($subsection->getName());
            switch($section_name) {
                case 'PROPERTY':
                    $property_spec = $this->_getComponentPropertySpec($subsection);
                    $return_value->addComponentPropertySpec($property_spec);
                    unset($property_spec);
                    break;
            }
        }
        return $return_value;
    }

    private function &_getComponentPropertySpec(__ConfigurationSection $component_property_section) {
        $return_value = null;
        if($component_property_section->hasAttribute('name')) {
            $property_name = $component_property_section->getAttribute('name');
            if($component_property_section->hasAttribute('target-member')) {
                $return_value = new __ComponentProperty2MemberSpec($property_name);
                $return_value->setMember($component_property_section->getAttribute('target-member'));
            }
            else if($component_property_section->hasAttribute('target-component')) {
                $return_value = new __ComponentProperty2ComponentSpec($property_name);
                $return_value->setComponent($component_property_section->getAttribute('target-component'));
            }
            if($return_value != null && $component_property_section->hasAttribute('target-property')) {
                $return_value->setProperty($component_property_section->getAttribute('target-property'));
            }
        }
        else {
            throw new __ConfigurationException('Missing property name in ui-component-interface section');
        }
        return $return_value;
    }

}

