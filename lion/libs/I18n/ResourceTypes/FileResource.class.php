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
 * @package    I18n
 * 
 */


class __FileResource extends __ResourceBase {
    
    protected $_mime_type = null;
    protected $_file_name = null;
    protected $_file_extension = null;
    
    public function getFileMD5() {
        return md5($this->getValue());        
    }
    
    public function setFileName($file_name) {
        $this->_file_name = $file_name;
        $path_parts = pathinfo($this->_file_name);
        $this->setFileExtension($path_parts['extension']);
    }
    
    public function setFileExtension($file_extension) {
        $this->_file_extension = $file_extension;
        $mime_type = $this->_getMimeTypeForFileExtension($file_extension);
        $this->setMimeType($mime_type);
    }
    
    public function display() {
        $value = $this->getValue();
	    if(!empty($value)) {
    	    //Generate an unike ID for the resource:
            $resource_hash = $this->getFileMD5();
    
            //Now will check if the browser sent an ID for requested resource.
            //If he sent it, will check if they match with current resource:
            $headers = headers_list();            
            if (key_exists('If-None-Match', $headers) && ereg($resource_hash, $headers['If-None-Match'])) {
                header('HTTP/1.1 304 Not Modified');
            }
            else {
                header("ETag: \"{$resource_hash}\"");
                header("Cache-Control: max-age=3600, must-revalidate");
                header("Pragma: Public");
        	    header("Accept-Ranges: bytes");
                header("Content-Length: ".strlen($value));
                if($this->getMimeType() != null) {          	    
                    header("Content-Type: {$this->getMimeType()}");    	    
                }
                if($this->getFileName() != null) {
                    header("Content-Disposition: inline; filename=\"{$this->getFileName()}\";");                
                }
                echo $value;
            }
	    }
	    else {
	        /**
	         * @todo Add a default image resource for non found resources!
	         */
	    }
    }
    
    public function getFileName() {
        $return_value = null;
        if($this->_file_name != null) {
            $return_value = $this->_file_name;
        }
        else if($this->_key != null) {
            if($this->_extension != null) {
                $return_value = $this->_key . '.' . $this->_extension;
            }
            else {
                $return_value = $this->_key . '.txt';
            }
        }
        return $return_value;
    }

    public function getMimeType() {
        return $this->_mime_type;
    }
            
    public function setMimeType($mime_type) {
        $this->_mime_type = $mime_type;
    }

    protected function _getMimeTypeForFileExtension($file_extension) {
        $return_value = 'text/plain'; //by default
        $mimetypes = array(
            'ez'      => 'application/andrew-inset',
            'hqx'     => 'application/mac-binhex40',
            'cpt'     => 'application/mac-compactpro',
            'doc'     => 'application/msword',
            'bin'     => 'application/octet-stream',
            'dms'     => 'application/octet-stream',
            'lha'     => 'application/octet-stream',
            'lzh'     => 'application/octet-stream',
            'exe'     => 'application/octet-stream',
            'class'   => 'application/octet-stream',
            'so'      => 'application/octet-stream',
            'dll'     => 'application/octet-stream',
            'oda'     => 'application/oda',
            'pdf'     => 'application/pdf',
            'ai'      => 'application/postscript',
            'eps'     => 'application/postscript',
            'ps'      => 'application/postscript',
            'smi'     => 'application/smil',
            'smil'    => 'application/smil',
            'mif'     => 'application/vnd.mif',
            'xls'     => 'application/vnd.ms-excel',
            'ppt'     => 'application/vnd.ms-powerpoint',
            'wbxml'   => 'application/vnd.wap.wbxml',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'bcpio'   => 'application/x-bcpio',
            'vcd'     => 'application/x-cdlink',
            'pgn'     => 'application/x-chess-pgn',
            'cpio'    => 'application/x-cpio',
            'csh'     => 'application/x-csh',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'dxr'     => 'application/x-director',
            'dvi'     => 'application/x-dvi',
            'spl'     => 'application/x-futuresplash',
            'gtar'    => 'application/x-gtar',
            'hdf'     => 'application/x-hdf',
            'js'      => 'application/x-javascript',
            'skp'     => 'application/x-koan',
            'skd'     => 'application/x-koan',
            'skt'     => 'application/x-koan',
            'skm'     => 'application/x-koan',
            'latex'   => 'application/x-latex',
            'nc'      => 'application/x-netcdf',
            'cdf'     => 'application/x-netcdf',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'swf'     => 'application/x-shockwave-flash',
            'sit'     => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texinfo' => 'application/x-texinfo',
            'texi'    => 'application/x-texinfo',
            't'       => 'application/x-troff',
            'tr'      => 'application/x-troff',
            'roff'    => 'application/x-troff',
            'man'     => 'application/x-troff-man',
            'me'      => 'application/x-troff-me',
            'ms'      => 'application/x-troff-ms',
            'ustar'   => 'application/x-ustar',
            'src'     => 'application/x-wais-source',
            'xhtml'   => 'application/xhtml+xml',
            'xht'     => 'application/xhtml+xml',
            'zip'     => 'application/zip',
            'au'      => 'audio/basic',
            'snd'     => 'audio/basic',
            'mid'     => 'audio/midi',
            'midi'    => 'audio/midi',
            'kar'     => 'audio/midi',
            'mpga'    => 'audio/mpeg',
            'mp2'     => 'audio/mpeg',
            'mp3'     => 'audio/mpeg',
            'aif'     => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'm3u'     => 'audio/x-mpegurl',
            'ram'     => 'audio/x-pn-realaudio',
            'rm'      => 'audio/x-pn-realaudio',
            'rpm'     => 'audio/x-pn-realaudio-plugin',
            'ra'      => 'audio/x-realaudio',
            'wav'     => 'audio/x-wav',
            'pdb'     => 'chemical/x-pdb',
            'xyz'     => 'chemical/x-xyz',
            'bmp'     => 'image/bmp',
            'gif'     => 'image/gif',
            'ief'     => 'image/ief',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'jpe'     => 'image/jpeg',
            'png'     => 'image/png',
            'tiff'    => 'image/tiff',
            'tif'     => 'image/tiff',
            'djvu'    => 'image/vnd.djvu',
            'djv'     => 'image/vnd.djvu',
            'wbmp'    => 'image/vnd.wap.wbmp',
            'ras'     => 'image/x-cmu-raster',
            'pnm'     => 'image/x-portable-anymap',
            'pbm'     => 'image/x-portable-bitmap',
            'pgm'     => 'image/x-portable-graymap',
            'ppm'     => 'image/x-portable-pixmap',
            'rgb'     => 'image/x-rgb',
            'xbm'     => 'image/x-xbitmap',
            'xpm'     => 'image/x-xpixmap',
            'xwd'     => 'image/x-xwindowdump',
            'igs'     => 'model/iges',
            'iges'    => 'model/iges',
            'msh'     => 'model/mesh',
            'mesh'    => 'model/mesh',
            'silo'    => 'model/mesh',
            'wrl'     => 'model/vrml',
            'vrml'    => 'model/vrml',
            'css'     => 'text/css',
            'html'    => 'text/html',
            'htm'     => 'text/html',
            'asc'     => 'text/plain',
            'txt'     => 'text/plain',
            'rtx'     => 'text/richtext',
            'rtf'     => 'text/rtf',
            'sgml'    => 'text/sgml',
            'sgm'     => 'text/sgml',
            'tsv'     => 'text/tab-separated-values',
            'wml'     => 'text/vnd.wap.wml',
            'wmls'    => 'text/vnd.wap.wmlscript',
            'etx'     => 'text/x-setext',
            'xsl'     => 'text/xml',
            'xml'     => 'text/xml',
            'mpeg'    => 'video/mpeg',
            'mpg'     => 'video/mpeg',
            'mpe'     => 'video/mpeg',
            'qt'      => 'video/quicktime',
            'mov'     => 'video/quicktime',
            'mxu'     => 'video/vnd.mpegurl',
            'avi'     => 'video/x-msvideo',
            'movie'   => 'video/x-sgi-movie',
            'ice'     => 'x-conference/x-cooltalk',
        );
        if(key_exists($file_extension, $mimetypes)) {
            $return_value = $mimetypes[$file_extension];
        }
        return $return_value;
        
    }        
    
}

