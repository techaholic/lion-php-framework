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
 * This is the section handler in charge of processing &lt;model-services&gt; configuration sections
 *
 */
class __ModelServicesSectionHandler extends __CacheSectionHandler {
    
    const MODEL_RECEIVER_CLASS = 1;
    const MODEL_RECEIVER_INSTANCE = 2;
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array();
        $subsections = $section->getSections();
        foreach($subsections as &$subsection) {
            switch(strtoupper($subsection->getName())) {
                case 'CLASS':
                    $return_value = $this->_createModelServices($subsection, self::MODEL_RECEIVER_CLASS, $return_value);
                    break;
                case 'INSTANCE':
                    $return_value = $this->_createModelServices($subsection, self::MODEL_RECEIVER_INSTANCE, $return_value);
                    break;
            }
        }
        
        return $return_value;
    }

    public function &_createModelServices(__ConfigurationSection &$section, $receiver_type, array $return_value) {
        if($receiver_type == self::MODEL_RECEIVER_CLASS) {
            $receiver = $section->getAttribute('name');
        }
        else {
            $receiver = $section->getAttribute('id');
        }
        $subsections = $section->getSections();
        foreach($subsections as $service_section) {
            switch (strtoupper($service_section->getName())) {
                case 'SERVICE':
                    $name          = $service_section->getAttribute('name');
                    $class_method  = $service_section->getAttribute('class-method');
                    $model_service_definition = new __ModelServiceDefinition($name);
                    if($receiver_type == self::MODEL_RECEIVER_CLASS) {
                        $model_service_definition->setClass($receiver);
                    }
                    else if($receiver_type == self::MODEL_RECEIVER_INSTANCE) {
                        $model_service_definition->setInstance($receiver);
                    }
                    $model_service_definition->setService($class_method);
                    if($service_section->hasAttribute('cache')) {
                        $model_service_definition->setCache(__ConfigurationValueResolver::toBool($service_section->getAttribute('cache')));
                    }
                    if($service_section->hasAttribute('cache-ttl')) {
                        $model_service_definition->setCacheTtl($service_section->getAttribute('cache-ttl'));
                    }
                    if($service_section->hasAttribute('remote')) {
                        $model_service_definition->setRemote($service_section->getAttribute('remote'));
                    }
                    $service_subsections = $service_section->getSections();
                    foreach($service_subsections as &$service_subsection) {
                        switch (strtoupper($service_subsection->getName())) {
                            case 'PERMISSION':
                                $model_service_definition->setRequiredPermission($service_subsection->getAttribute('id'));
                                break;
                            case 'SERVICE-ARG':
                                $model_service_argument = new __ModelServiceArgument();
                                if($service_subsection->hasAttribute('name')) {
                                    $model_service_argument->setName($service_subsection->getAttribute('name'));
                                }
                                else {
                                    throw __ExceptionFactory::getInstance()->createException('Missing name attribute in model service argument definition: ' . $name);
                                }
                                if($service_subsection->hasAttribute('index')) {
                                    $model_service_argument->setIndex($service_subsection->getAttribute('index'));
                                }
                                $model_service_definition->addArgument($model_service_argument);
                                unset($model_service_argument);
                                break;
                        }
                    }
                    if(key_exists($model_service_definition->getAlias(), $return_value)) {
                        throw new __ConfigurationException('Duplicate definition of model service: ' . $model_service_definition->getAlias());   
                    }
                    else {
                        $return_value[$model_service_definition->getAlias()] =& $model_service_definition;
                        unset($model_service_definition);
                    }
                    break;
            }
        }
        return $return_value;
    }
    
}

