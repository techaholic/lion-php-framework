<?php
/**
 * This class is based on PEAR::Cache_Lite by Fabien Marty under GNU LESSER GENERAL PUBLIC LICENSE (http://www.opensource.org/licenses/lgpl-license.php)
 * 
 * It was created on 12/23/2006
 * 
 * @package Cache
 * 
 */
class __CacheLite extends __CacheHandler {

    /**
     * This is the general options array, that will contain (depending on the cache child class) caching options to use.
     *
     * @var array
     */
    protected $_options = array();

    protected $_file;

    protected $_file_name;
    
    protected $_cache_directory = null;
    
    protected $_group = 'LION_FRAMEWORK';
    
    /**
     * By default, the ttl is set to null (never expire) 
     *      
     */
    protected $_default_ttl = null;      
    
    final public function __construct()
    {
        $this->_options = array('refresh_time'              => null,
                                'protect_content'           => true,
                                'write_control'             => true,
                                'read_control'              => true,
                                'read_control_type'         => 'crc32',
                                'automatic_serialization'   => true,
                                'automatic_cleaning_factor' => 0,
                                'hashed_directory_level'    => 1,
                                'hashed_directory_umask'    => 0777,
                                'file_locking'              => false
                                );
                                
        $cache_dir = $this->_getCacheDirectory();
        if(is_dir($cache_dir) && is_writable($cache_dir)) {
            $this->setOption('cache_dir', $cache_dir . DIRECTORY_SEPARATOR);
        }
                                
    }

    protected function _getCacheDirectory() {
        if($this->_cache_directory == null) {
            $cache_dir = __Lion::getInstance()->getRuntimeDirectives()->getDirective('CACHE_FILE_DIR') . DIRECTORY_SEPARATOR;
            if( preg_match( '/^\//', $cache_dir ) || preg_match('/^\w+:/', $cache_dir)) {
                $this->_cache_directory = $cache_dir;
            }
            else {
                $this->_cache_directory = APP_DIR . DIRECTORY_SEPARATOR . $cache_dir;
            }
        }
        return $this->_cache_directory;
    }    
    
    public function setOption($key, $value) {
        $this->_options[$key] = $value;
    }
    
    public function load($key, $ttl = null)
    {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }
        $return_value = null;
        $data = false;
        $this->_setRefreshTime($ttl);
        $key = $this->_getUnikeNameForIdAndGroup($key, $this->_group);
        $this->_setFileName($key);
        clearstatcache();
        if (empty($this->_refresh_time)) {
            if (file_exists($this->_file)) {
                $return_value = $this->_read($ttl);
            }
        } else {
            if ((file_exists($this->_file)) && (@filemtime($this->_file) > $this->_refresh_time)) {
                $return_value = $this->_read($ttl);
            }
        }
        if (($this->_options['automatic_serialization']) and (is_string($return_value))) {
            $return_value = unserialize($return_value);
        }
        return $return_value;
    }
    
    public function save($key, $data, $ttl = null)
    {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }
        $return_value = false;
        if ($this->_options['automatic_serialization']) {
            $data = serialize($data);
        }
        $key = $this->_getUnikeNameForIdAndGroup($key, $this->_group);
        $this->_setFileName($key);
        if ( $this->_options['automatic_cleaning_factor'] > 0 ) {
            $rand = rand(1, $this->_options['automatic_cleaning_factor']);
            if ( $rand == 1 ) {
                $this->clean(false, 'old');
            }
        }
        if ($this->_options['write_control']) {
            $return_value = $this->_writeAndControl($data, $ttl);
            if (is_bool($return_value)) {
                if ($return_value) {
                    return true;  
                }
                //else if $return_value is false, we need to invalidate the cache
                @touch($this->_file, time() - 2 * abs($ttl));
                return false;
            }
        } else {
            $return_value = $this->_write($data);
        }
        return $return_value;
    }

    public function remove($key)
    {
        $key = $this->_getUnikeNameForIdAndGroup($key, $this->_group);
        $return_value = $this->_removeFromStorage($key);
        return $return_value;
    }

    public function clear()
    {
        $return_value = $this->_cleanDir($this->_options['cache_dir'], $this->_group, 'ingroup');
        return $return_value;
    }
        
    public function extendExpiration()
    {
        $this->_extendExpirationFromStorage();
    }

    public function callCacheUserFunction()
    {
        $arguments = func_get_args();
        $id = $this->_makeId($arguments);
        $data = $this->getData($id, 'cache_function');
        if ($data != null) {
            $array = unserialize($data);
            $output = $array['output'];
            $return_value = $array['result'];
        } else {
            ob_start();
            ob_implicit_flush(false);
            $target = array_shift($arguments);
            if (is_array($target)) {
                // in this case, $target is for example array($obj, 'method')
                $object = $target[0];
                $method = $target[1];
                $return_value = call_user_func_array(array(&$object, $method), $arguments);
            } else {
	            if (strstr($target, '::')) { // classname::staticMethod
	                list($class, $method) = explode('::', $target);
	                $return_value = call_user_func_array(array($class, $method), $arguments);
	            } else if (strstr($target, '->')) { // object->method
	                // use a stupid name ($objet_123456789 because) of problems where the object
	                // name is the same as this var name
	                list($object_123456789, $method) = explode('->', $target);
	                global $$object_123456789;
	                $return_value = call_user_func_array(array($$object_123456789, $method), $arguments);
	            } else { // function
	                $return_value = call_user_func_array($target, $arguments);
	            }
            }
            $output = ob_get_contents();
            ob_end_clean();
            $array['output'] = $output;
            $array['result'] = $return_value;
            $this->setData(serialize($array), $id, 'cache_function');
        }
        echo($output);
        return $return_value;
    }
    
    public function removeCacheUserFunction()
    {
        $id = $this->_makeId(func_get_args());
        $key = $this->_getUnikeNameForIdAndGroup($id, 'cache_function');
        $this->_removeFromStorage($key);
    }
    
    protected function _setRefreshTime($ttl) 
    {
        if (empty($ttl)) {
            $this->_refresh_time = null;
        } else {
            $this->_refresh_time = time() - $ttl;
        }
    }
    
    protected function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return sprintf('% 32d', crc32($data));
        case 'strlen':
            return sprintf('% 32d', strlen($data));
        default:
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_CONTROLTYPE');
        }
    }
    
    protected function _makeId($arguments) 
    {
        $id = serialize($arguments); // Generate a cache id
        if (!$this->_options['protect_content']) {
            $id = md5($id);
        }    
        return $id;
    }    
    
    protected function _getUnikeNameForIdAndGroup($id, $group) {
        if ($this->_options['protect_content']) {
            $unike_name = 'code_cache_'.md5($group).'_'.md5($id);
        } else {
            $unike_name = 'code_cache_'.$group.'_'.$id;
        }
        return $unike_name;
    }        
    
    protected function _removeFromStorage($key)
    {
        $this->_setFileName($key);
        return $this->_unlink($this->_file);
    }
    
    protected function _getLastModifiedFromStorage() 
    {
        return @filemtime($this->_file);
    }

    protected function _extendExpirationFromStorage()
    {
        @touch($this->_file);
    }
    
    
    protected function _setFileName($file_name)
    {
        $root = $this->_options['cache_dir'];
        if ($this->_options['hashed_directory_level'] > 0) {
            $hash = md5($file_name);
            for ($i=0 ; $i<$this->_options['hashed_directory_level'] ; $i++) {
                $root = $root . 'code_cache_' . substr($hash, 0, $i + 1) . '/';
            }   
        }
        $this->_file_name = $file_name;
        $this->_file      = $root . $file_name;
    }
    
    protected function _writeAndControl($data, $ttl)
    {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }
        $result = $this->_write($data);
        if (is_object($result)) {
            return $result;
        }
        $dataRead = $this->_read($ttl);
        if (is_object($dataRead)) {
            return $result;
        }
        if ((is_bool($dataRead)) && (!$dataRead)) {
            return false; 
        }
        return ($dataRead==$data);
    }
    
    protected function _read($ttl)
    {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }
        $fp = @fopen($this->_file, "rb");
        if ($this->_options['file_locking']) @flock($fp, LOCK_SH);
        if ($fp) {
            clearstatcache();
            $length = @filesize($this->_file);
            $mqr = get_magic_quotes_runtime();
            if ($this->_options['read_control']) {
                $hashControl = @fread($fp, 32);
                $length = $length - 32;
            } 
            if ($length) {
                $data = @fread($fp, $length);
            } else {
                $data = '';
            }
            if ($this->_options['file_locking']) @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($this->_options['read_control']) {
                $hashData = $this->_hash($data, $this->_options['read_control_type']);
                if ($hashData != $hashControl) {
                    if (!(empty($ttl))) {
                        @touch($this->_file, time() - 2*abs($ttl)); 
                    } else {
                        @unlink($this->_file);
                    }
                    return false;
                }
            }
            return $data;
        }
        throw __ExceptionFactory::getInstance()->createException('ERR_CACHE_NOT_READABLE', array('cache_file' => $this->_file));
    }
    
    protected function _write($data)
    {
        if ($this->_options['hashed_directory_level'] > 0) {
            $hash = md5($this->_file_name);
            $root = $this->_options['cache_dir'];
            for ($i=0 ; $i<$this->_options['hashed_directory_level'] ; $i++) {
                $root = $root . 'code_cache_' . substr($hash, 0, $i + 1) . '/';
                if (!(@is_dir($root))) {
                    @mkdir($root, $this->_options['hashed_directory_umask']);
                }
            }
        }
        $fp = @fopen($this->_file, "wb");
        if ($fp) {
            if ($this->_options['file_locking']) @flock($fp, LOCK_EX);
            if ($this->_options['read_control']) {
                @fwrite($fp, $this->_hash($data, $this->_options['read_control_type']), 32);
            }
            $len = strlen($data);
            @fwrite($fp, $data, $len);
            if ($this->_options['file_locking']) @flock($fp, LOCK_UN);
            @fclose($fp);
            return true;
        }      
        throw __ExceptionFactory::getInstance()->createException('ERR_CACHE_NOT_WRITABLE', array('cache_file' => $this->_file));
    }
    
    protected function _cleanDir($dir, $group = false, $mode = 'ingroup')     
    {
        if ($this->_options['protect_content']) {
            $motif = ($group) ? 'code_cache_'.md5($group).'_' : 'code_cache_';
        } else {
            $motif = ($group) ? 'code_cache_'.$group.'_' : 'code_cache_';
        }
        if (!($dh = opendir($dir))) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CANT_OPEN_CACHE_DIR');
        }
        $result = true;
        while ($file = readdir($dh)) {
            if (($file != '.') && ($file != '..')) {
                if (substr($file, 0, 11)=='code_cache_') {
                    $file2 = $dir . $file;
                    if (is_file($file2)) {
                        switch (substr($mode, 0, 9)) {
                            case 'old':
                                // files older than lifeTime get deleted from cache
                                if (!empty($this->_default_ttl)) {
                                    if ((mktime() - @filemtime($file2)) > $this->_default_ttl) {
                                        $result = ($result and ($this->_unlink($file2)));
                                    }
                                }
                                break;
                            case 'notingrou':
                                if (!strpos($file2, $motif, 0)) {
                                    $result = ($result and ($this->_unlink($file2)));
                                }
                                break;
                            case 'callback_':
                                $func = substr($mode, 9, strlen($mode) - 9);
                                if ($func($file2, $group)) {
                                    $result = ($result and ($this->_unlink($file2)));
                                }
                                break;
                            case 'ingroup':
                            default:
                                if (strpos($file2, $motif, 0)) {
                                    $result = ($result and ($this->_unlink($file2)));
                                }
                                break;
                        }
                    }
                    if ((is_dir($file2)) and ($this->_options['hashed_directory_level']>0)) {
                        $result = ($result and ($this->_cleanDir($file2 . '/', $group, $mode)));
                    }
                }
            }
        }
        return $result;
    }

    protected function _unlink($file)
    {
        if (file_exists($file) && !@unlink($file)) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CANT_REMOVE_CACHE');
        }
        return true;        
    }

    
    
}