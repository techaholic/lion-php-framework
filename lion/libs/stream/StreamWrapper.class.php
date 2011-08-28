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

abstract class __StreamWrapper {

    protected $_stream_storage = null;
        
    public function stream_open($path, $mode, $options, $opened_dir) {
        $this->_stream_storage =& $this->_createStreamStorage($path);
        if ($this->_stream_storage instanceof __StreamStorage) {
            $this->_stream_storage->open($mode);
        }
        else {
            throw new __StreamException("Unable to create an stream storage for path: '" . $path . "'");
        }
        return TRUE;
    }

    public function stream_read($length)
    {
        return $this->_stream_storage->read($length);
    }

    public function stream_write($data, $length = null)
    {
        return $this->_stream_storage->write($data, $length);
    }
    
    public function stream_close() {
        return $this->_stream_storage->close();
    }

    public function stream_tell()
    {
        return $this->_stream_storage->tell();
    }
    
    public function stream_flush() {
        return $this->_stream_storage->flush();
    }

    public function stream_eof()
    {
        return $this->_stream_storage->eof();
    }

    public function stream_seek($offset, $whence = null)
    {
        return $this->_stream_storage->seek($offset, $whence);
    }
    
    public function stream_lock($operation) {
        return $this->_stream_storage->lock($operation);
    }

    public function stream_stat() {
        $this->_stream_storage->stat();
    }

    public function url_stat($path, $options) {
        $return_value = null;
        $stream_storage =& $this->_createStreamStorage($path);
        if( $stream_storage instanceof __StreamStorage ) {
            $return_value = $stream_storage->url_stat();
        }
        else {
            throw new __StreamException("Unable to create an stream storage for path: '" . $path . "'");
        }
        return $return_value;
    }

    abstract protected function &_createStreamStorage($path);

}
?>