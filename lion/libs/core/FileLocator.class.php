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

abstract class __FileLocator {
    
    protected $_search_dirs = array();
    
    final public function __construct() {
        $search_dirs = $this->getSearchDirs();
        foreach($search_dirs as $search_dir) {
            $this->addSearchDir($search_dir);
        }
    }
    
    final public function addSearchDir($search_dir) {
        $this->_search_dirs[] = $search_dir;
    }
    
    final public function reverseSearchDirs() {
        $this->_search_dirs = array_reverse($this->_search_dirs);
    }
    
    /**
     * Returns the path corresponding to a given file name
     *
     * @param string $file_name The file name
     * @return string The requested dirs
     */
	final public function locateFile($file_name) {
	    if($file_name != null) {
    	    foreach($this->_search_dirs as $search_dir) {
    	        $file_path = $search_dir . DIRECTORY_SEPARATOR . $file_name;
    	        if(is_readable($file_path) && is_file($file_path)) {
    	            return $file_path;
    	        }
    	    }
	    }
	    return null;
	}

	/**
	 * This method returns an array of all dirss to search
	 *
	 * @return array
	 */
	abstract public function getSearchDirs();
	
}