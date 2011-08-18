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
 * @package    Stream
 * 
 */

class __StreamStorageFactory {
        
    private static $_instance = null;
    private $_storage_classes = array();
    
    private function __construct() {
        //startup storage classes:
        $this->addStorageClass(STREAM_STORAGE_FILE_SYSTEM, '__FileSystemStreamStorage');
    }
    
    public function addStorageClass($storage_media, $storage_class) {
        if(class_exists($storage_class)) {
            $this->_storage_classes[$storage_media] = $storage_class;
        }
        else {
            throw new __StreamException("Unknow stream storage class: '" . $storage_class . "'");
        }
    }
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __StreamStorageFactory();
        }
        return self::$_instance;
    }    
    
    public function createStreamStorage(__StorageInfo &$storage_info) {
        $storage_media      = $storage_info->getStorageMedia();
        $storage_parameters = $storage_info->getStorageParameters();
        return $this->_createStreamStorageOfMedia($storage_media, $storage_parameters);
    }
    

    private function _createStreamStorageOfMedia($storage_media, array $storage_parameters = array()) {
        $return_value = null;
        if(key_exists($storage_media, $this->_storage_classes)) {
            $storage_class = $this->_storage_classes[$storage_media];
            $return_value = new $storage_class($storage_parameters);
        }
        return $return_value;
    }
    
    
}