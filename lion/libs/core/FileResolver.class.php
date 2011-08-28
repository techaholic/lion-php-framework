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
 * @package    Core
 * 
 */

class __FileResolver {

    /**
     * Get an expression and returns a collection of files matching the expression
     * 
     * Expression may be in the form of:
     * <br>
     * path/.../*.xml  All xml files in whatever subdirectory under the specified path<br>
     * path/*.xml All xml files under the specified path (subdirectories not included)<br>
     * path/specificFile.xml An specific file under the specified path<br>
     *
     * @param string $expression
     * @return array Files matching the expression
     */
    public static function resolveFiles($expression) {
        $file_pattern = basename($expression);
        $ellipse_position = strpos($expression, '...');
        if($ellipse_position !== false) {
            $basedir = substr($expression, 0, $ellipse_position);
            $basedir = rtrim($basedir, DIRECTORY_SEPARATOR);
            $return_value = self::getFilesMatchingPattern($file_pattern, $basedir, true);
        }
        else {
            $basedir = dirname($expression);
            $wildcard_position = strpos($expression, '*');
            if($wildcard_position !== false) {
                $return_value = self::getFilesMatchingPattern($file_pattern, $basedir, false);
            }
            else {
                $return_value = array($expression);
            }
        }
        return $return_value;
    }
    
    /**
     * Get a pattern and a directory, and returns an array of filenames matching the given pattern
     *
     * @param string $file_pattern
     * @param string $current_dir
     * @param boolean $recursively
     * @return array An array of files matching the given pattern
     */
    public static function getFilesMatchingPattern($file_pattern, $current_dir, $recursively = true) {
        $return_value = array();
        if(is_readable($current_dir) && is_dir($current_dir)) {
            $dir = dir($current_dir);
            while (false !== ($file = $dir->read())) {
                $current_file = $current_dir . DIRECTORY_SEPARATOR . $file;
                $position_of_dot = strpos($file, ".");                
                if(is_readable($current_file) && $position_of_dot !== 0) {
                    if(is_file($current_file)) {
                        $file_matched = array();
                        if(preg_match('/^' . str_replace('*', '(.+?)', $file_pattern) . '$/', $file, $file_matched)) {
                            $return_value[strtoupper($current_file)] = $current_file;
                        }
                    }
                    else if(is_dir($current_file) && $recursively) {
                        $return_value = array_merge(self::getFilesMatchingPattern($file_pattern, $current_file), $return_value);
                    }
                }
            }
        }
        else {
            throw new Exception('The directory ' . $current_dir . ' specified in the includepath does not exists or is not readable.');
        }
        return $return_value;
    }

}
?>