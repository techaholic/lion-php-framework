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
 * @package    ComponentModel
 * 
 */


class __ProtectedSectionHtmlWriter extends __ComponentWriter {

    public function canRenderChildrenComponents(__IComponent &$component) {
        $return_value = false;
        $permission_id = $component->getPermission();
        $condition = $component->getCondition();
        if(__PermissionManager::getInstance()->hasPermission($permission_id)) {
            $permission = __PermissionManager::getInstance()->getPermission($permission_id);
            if($condition == __ProtectedSectionComponent::IF_HAS_PERMISSION &&
               __AuthorizationManager::getInstance()->hasPermission($permission)) {
                $return_value = true;
            }
            else if($condition == __ProtectedSectionComponent::IF_NOT_HAS_PERMISSION &&
               !__AuthorizationManager::getInstance()->hasPermission($permission)) {
                $return_value = true;
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unknow permission id: ' . $permission_id);
        }
        return $return_value;
    }
    
}