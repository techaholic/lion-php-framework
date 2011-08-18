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
 * This is the section handler in charge of processing &lt;error-codes&gt; configuration sections
 *
 */
class __ErrorCodesSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = new __ErrorTable();
        $error_groups = $section->getSections();
        foreach($error_groups as &$error_group) {
            $this->_processErrorGroup($error_group, $return_value);
        }
        return $return_value;
    }

    private function _processErrorGroup(__ConfigurationSection &$error_group, __ErrorTable &$error_table) {
        $group_id = $error_group->getAttribute('id');
        $exception_class = $error_group->getAttribute('exception-class');
        $error_table->registerExceptionClass($group_id, $exception_class);
        $error_codes =& $error_group->getSections();
        foreach($error_codes as &$error_code) {
            $error_table->registerErrorCode($error_code->getAttribute('code'), $error_code->getAttribute('id'), $group_id);
        }
    }
            
    
    
}