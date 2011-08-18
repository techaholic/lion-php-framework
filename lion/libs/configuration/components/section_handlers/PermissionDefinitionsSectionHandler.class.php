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
 * This is the section handler in charge of processing &lt;permission-definitions&gt; configuration sections
 *
 */
final class __PermissionDefinitionsSectionHandler extends __CacheSectionHandler {
    
    private $_permissions = array();
    private $_permission_ids = array();
    
    public function &doProcess(__ConfigurationSection &$section) {
        unset($this->_permissions);
        $this->_permissions    = array();
        $this->_permission_ids = array();
        $subsections = $section->getSections();
        foreach($subsections as &$subsection) {
            if(strtoupper($subsection->getName()) == 'PERMISSION') {
                $permission = $this->_createPermission($subsection);
                $this->_permissions[$permission->getId()] =& $permission;
                unset ($permission);
            }
        }
        return $this->_permissions;
    } 
    
    private function &_createPermission(__ConfigurationSection &$permission_section) {
        $permission_id = strtoupper($permission_section->getAttribute('id'));
        if($permission_id == 'PERMISSION_ALL') {
            throw new __ConfigurationException('Can not create a permission with name ' . $permission_id . ' reserved for an special permission');
        }
        else if(key_exists($permission_id, $this->_permission_ids)) {
            throw new __ConfigurationException('Double declaration of permission ' . $permission_id . '. If your intention was to reference an already existing permission, use a REF tag instead of PERMISSION');
        }
        $this->_permission_ids[$permission_id] = true;
        $subsections = $permission_section->getSections();
        if(count($subsections) == 0) {
            $binary_representation = null;
        }
        else {
            $binary_representation = 0;
        }
        if($permission_section->hasAttribute('class')) {
            $class = $permission_section->getAttribute('class');
        }
        else {
            $class = '__Permission';
        }
        if(class_exists($class)) {
            $permission = new $class($permission_id, $binary_representation);
            if(!$permission instanceof __Permission) {
                throw new __ConfigurationException('Unexpected class for permission: ' . $class);
            }
        }
        else {
            throw new __ConfigurationException('Unknown class for permission: ' . $class);
        }
        foreach($subsections as $subsection) {
            if(strtoupper($subsection->getName()) == 'JUNIOR-PERMISSIONS') {
                $junior_permission_sections = $subsection->getSections();
                foreach($junior_permission_sections as $junior_permission_section) {
                    switch( strtoupper($junior_permission_section->getName()) ) {
                        case 'REF':
                            $junior_permission_id = strtoupper($junior_permission_section->getAttribute('id'));
                            if($junior_permission_id == 'PERMISSION_ALL') {
                                throw new __ConfigurationException('Special permission ' . $junior_permission_id . ' can not be referenced by a permission definition');
                            }
                            if(key_exists($junior_permission_id, $this->_permissions)) {
                                $junior_permission =& $this->_permissions[$junior_permission_id];
                                $permission->addJuniorPermission($junior_permission);
                                unset($junior_permission);
                            }
                            else {
                                throw new __ConfigurationException('Unknow permission reference: ' . $junior_permission_id . '. If the permission already exist but has been declared below the reference, move the declaration before the reference.');
                            }
                            break;
                        case 'PERMISSION':
                            $junior_permission = $this->_createPermission($junior_permission_section);
                            $this->_permissions[$junior_permission->getId()] =& $junior_permission;
                            $permission->addJuniorPermission($junior_permission);
                            unset($junior_permission);
                            break;
                    }
                }
            }
        }
        return $permission;
    }
    
}