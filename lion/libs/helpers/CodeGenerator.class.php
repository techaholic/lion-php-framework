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
 * @package    Helpers
 * 
 */

class __CodeGenerator {

    /**
     * This function validate a grantId for parameters and page request
     *
     * @param string $ The grant id to validate
     * @param string $ The page id for current grant id
     * @return boolean true if the grant id is correct for the specified page id
     */
    static function validateGrantId($grantId, $pageID)
    {
        // While not implemented:
        return false;
    }

    static function generateGrantId($pageID, $formID)
    {
        return "";
    }    
    
    /**
     * This function generate a submitCode according to a pageID and formID
     *
     * @param string $ The id of the page
     * @param string $ The id of the form
     * @return string The requested submitCode
     */
    static function getSubmitCode($action_code, $form_id = null)
    {
        $submit_code = $action_code;
        if($form_id != null) {
            $submit_code .= "~" . $form_id;
        }
        $submit_code = md5($submit_code);
        return $submit_code;
    }
    
    static function getUnikeCode() {
        $return_value = md5(uniqid(rand(), true));
        return $return_value;
    }
    
    /**
     * This function generate a submitCode according to a pageID and formID
     *
     * @param string $ The id of the page
     * @param string $ The id of the form
     * @return string The requested submitCode
     */
    static function generateSubmitCode($pageID, $formID)
    {
        return self::GetSubmitCode($pageID, $formID);
    }    
    
}

?>