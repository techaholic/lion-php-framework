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
 * This is the section handler in charge of processing &lt;role-definitions&gt; configuration sections
 *
 */
final class __RoleDefinitionsSectionHandler extends __CacheSectionHandler {
    
    private $_roles = array();
    private $_role_ids = array();
    
    public function &doProcess(__ConfigurationSection &$section) {
        unset($this->_roles);
        $this->_roles    = array();
        $this->_role_ids = array();
        $subsections = $section->getSections();
        foreach($subsections as &$subsection) {
            if(strtoupper($subsection->getName()) == 'ROLE') {
                $role = $this->_createRole($subsection);
                $this->_roles[$role->getId()] =& $role;
                unset ($role);
            }
        }
        return $this->_roles;
    } 
    
    private function &_createRole(__ConfigurationSection &$role_section) {
        $role_id = strtoupper($role_section->getAttribute('id'));
        if(key_exists($role_id, $this->_role_ids)) {
            throw new __ConfigurationException('Double declaration of role ' . $permission_id . '. If your intention was to reference an already existing role, use a REF tag instead of ROLE');
        }
        else {
            $this->_role_ids[$role_id] = true;
        }
        $role = new __Role($role_id);
        $subsections = $role_section->getSections();
        foreach($subsections as $subsection) {
            switch(strtoupper($subsection->getName())) {
                case 'JUNIOR-ROLES':
                    $this->_setJuniorRoles($role, $subsection);
                    break;
                case 'PERMISSIONS':
                    $this->_setPermissions($role, $subsection);
                    break;
            }
        }
        return $role;
    }

    private function _setJuniorRoles(__Role &$role, __ConfigurationSection &$section) {
        $junior_role_sections = $section->getSections();
        foreach($junior_role_sections as $junior_role_section) {
            switch( strtoupper($junior_role_section->getName()) ) {
                case 'REF':
                    $junior_role_id = strtoupper($junior_role_section->getAttribute('id'));
                    if(key_exists($junior_role_id, $this->_roles)) {
                        $junior_role =& $this->_roles[$junior_role_id];
                        $role->addJuniorRole($junior_role);
                        unset($junior_role);
                    }
                    else {
                        throw new __ConfigurationException('Unknow role reference: ' . $junior_role_id . '. If the role already exist but has been declared below the reference, move the declaration before the reference.');
                    }
                    break;
                case 'ROLE':
                    $junior_role = $this->_createRole($junior_role_section);
                    $this->_roles[$junior_role->getId()] =& $junior_role;
                    $role->addJuniorRole($junior_role);
                    unset($junior_role);
                    break;
            }
        }
    }
    
    private function _setPermissions(__Role &$role, __ConfigurationSection &$section) {
        $permission_sections = $section->getSections();
        foreach($permission_sections as &$permission_section) {
            if(strtoupper($permission_section->getName()) == 'PERMISSION') {
                $permission_id = $permission_section->getAttribute('id');
                $permission    = __PermissionManager::getInstance()->getPermission($permission_id);
                $role->addPermission($permission);
                unset($permission);
            }
        }
        
    }
    
}