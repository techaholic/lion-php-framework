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
 * @package    I18n
 * 
 */


/**
 * This class is an utility class for __ResourceManager class, that will be the class in charge of contain all the resource dictionaries
 * that are handled by the __ResourceManager instance.
 * 
 */
class __ResourceTable {

    /**
     * This is the array that contains all the resources, classified by languages
     *
     * @var array
     */
    private $_resources = array();

    /**
     * This variable stores pairs [action_start_pointer, action_number_of_items] associated for each action in _resources array
     *
     * @var array
     */
    private $_action_resources_pointers = array();

    /**
     * This method add a set of resources to the _resources internal array
     *
     * @param array $resources Resources to add to
     */
    public function addResources(array $resources, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        $resources = array_change_key_case($resources, CASE_UPPER);
        if(!$this->hasLanguage($language_iso_code)) {
            $this->addLanguage($language_iso_code);
        }
        $this->_resources[$language_iso_code] = array_merge($resources, $this->_resources[$language_iso_code]);
    }

    public function addResource($resource, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        $resource_key = strtoupper($resource->getKey());
        if(!$this->hasLanguage($language_iso_code)) {
            $this->addLanguage($language_iso_code);
        }
        $this->_resources[$language_iso_code][$resource_key] = $resource;
    }
    
    public function addActionResources(array $resources, __ActionIdentity $action_identity, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        $resources = array_change_key_case($resources, CASE_UPPER);
        if(!$this->hasLanguage($language_iso_code)) {
            $this->addLanguage($language_iso_code);
        }
        if(!key_exists($language_iso_code, $this->_action_resources_pointers)) {
            $this->_action_resources_pointers[$language_iso_code] = count($this->_resources[$language_iso_code]);
        }
        $this->_resources[$language_iso_code] = array_merge($this->_resources[$language_iso_code], $resources);
    }

    public function addLanguage($language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        if(!$this->hasLanguage($language_iso_code)) {
            $this->_resources[$language_iso_code] = array();
            $this->_action_resources_pointers[$language_iso_code] = null;
        }
    }

    public function unloadResources() {
        unset($this->_resources);
        $this->_resources = array();
    }

    public function unloadActionResources() {
        foreach($this->_action_resources_pointers as $language_iso_code => $action_resources_pointer) {
            if($this->_action_resources_pointers[$language_iso_code] != null) {
                array_splice($this->_resources[$language_iso_code], $action_resources_pointer);
                $this->_action_resources_pointers[$language_iso_code] = null;
            }
        }
    }

    public function hasLanguage($language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        return key_exists($language_iso_code, $this->_resources);
    }

    public function hasResource($resource_id, $language_iso_code) {
        $resource_id = strtoupper($resource_id);
        $language_iso_code = strtoupper($language_iso_code);
        $return_value = false;
        if(key_exists($language_iso_code, $this->_resources) && key_exists($resource_id, $this->_resources[$language_iso_code])) {
            $return_value = true;
        }
        return $return_value;
    }

    /**
     * This method returns a concrete resource identified by an id.
     *
     * @param string $resource_id The id of the resource to load to
     * @param string $language_iso_code The language that you want to retrieve the resource from. If omitted, the current one will be taken from the execution
     * @return __ResourceBase The requested resource
     */
    public function getResource($resource_id, $language_iso_code) {
        $resource_id = strtoupper($resource_id);
        $language_iso_code = strtoupper($language_iso_code);
        $return_value = null;
        if(key_exists($language_iso_code, $this->_resources) && key_exists($resource_id, $this->_resources[$language_iso_code])) {
            $return_value = $this->_resources[$language_iso_code][$resource_id];
        }
        return $return_value;
    }


}





