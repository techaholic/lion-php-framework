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
 * This is the base class for configuration storages representing the media where configuration files are stored in
 *
 */
abstract class __ConfigurationStorage {
    
    public function load($filename, __Configuration &$configuration) {
        if(is_readable($filename) && is_file($filename)) {
            try {
                $file_content = file_get_contents($filename);
                $this->parse($file_content, $configuration);
            }
            catch (Exception $e) {
                throw new __ConfigurationException("Error parsing configuration file '$filename':\n\n" . $e->getMessage());
            }
        }
    }    
    
    abstract public function parse($content, __Configuration &$configuration);
    
    abstract public function save($filename, __Configuration &$configuration);

    abstract public function toString(__ConfigurationComponent &$configuration_component);
    
    protected function _parseValue($value, __ComplexConfigurationComponent &$configuration_component) {
        return __ConfigurationValueResolver::resolveValue($value);
    }
    
}