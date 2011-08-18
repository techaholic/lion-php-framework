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

class __MemoryStreamStorage implements __IStreamStorage {
    
    private $_stream_mode = 'r';
    
    /**
     * Returns an octal representation of access mode for an stream.
     * Mode are based on an octal representation of read/write/execution where
     * each digit from left to right represent the owner, the group and other respectively.
     *
     * @return integer
     */
    protected function _getStreamMode() {
        return $this->_stream_mode;
    }

    protected function _setStreamMode($stream_mode) {
        $format = 'text';
        if(is_string($stream_mode) && !is_numeric($stream_mode)) {
            $mode = 0;
            foreach($stream_mode as $single_mode) {
                switch (strtoupper($single_mode)) {
                    case 'R':
                        $mode |= 100;
                        break;
                    case 'W':
                    case 'A':
                        $mode |= 200;
                        break;
                    case 'X':
                        $mode |= 400;
                        break;
                    case 'T':
                        $format = 'text';
                        break;
                    case 'B':
                        $format = 'binary';
                        break;
                }
            }
            $this->_stream_mode   = (int) $mode;
            $this->_stream_format = $format;
        }
        else {
            $this->_stream_mode = (int) $stream_mode;
        }
    }
    
    public function stat() {    
        $time = time();
        if($this->_getStreamContent() != null) {
            $size = strlen($this->_getStreamContent());
        }
        else {
            $size = 0;
        }
        $uid  = getmyuid();
        $gid  = getmygid();
        $mode = octdec(100000 + $this->_getStreamMode());

        $keys = array(
            'dev'     => 0,
            'ino'     => 0,
            'mode'    => $mode,
            'nlink'   => 0,
            'uid'     => $uid,
            'gid'     => $gid,
            'rdev'    => 0,
            'size'    => $size,
            'atime'   => $time,
            'mtime'   => $time,
            'ctime'   => $time,
            'blksize' => 0,
            'blocks'  => 0
        );
        $return_value = array_merge(array_values($keys), $keys);
        return $return;
    }
    
}