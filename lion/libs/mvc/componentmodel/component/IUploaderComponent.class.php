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
 * @package    ComponentModel
 * 
 */

/**
 * Interface to components representing file uploaders.
 * 
 * File uploaders have 2 working modes: synchronous and asynchronous.
 * Synchronous mode allow to upload files on form submit events, while asynchronous mode allow to upload
 * files as soon as they are selected within the client component.
 * 
 * Lion ootb implementation of this interface is the {@link __FileBoxComponent}, a really good javascript-based uploader.
 *
 */
interface __IUploaderComponent extends __IValueHolder {
        
    /**
     * Uploader is ready to upload a new file
     */
    const UPLOAD_STATUS_READY     = 1;
    
    /*
     * Uploader is uploading a file
     */
    const UPLOAD_STATUS_UPLOADING = 2;
    
    /**
     * Uploader has uploaded a file successfully
     */
    const UPLOAD_STATUS_DONE      = 3;
    
    /**
     * Upload has been cancelled by the user
     */
    const UPLOAD_STATUS_CANCELLED = 4;
    
    /**
     * Generally due to an unknown error :)
     */
    const UPLOAD_STATUS_UNKNOWN   = 100;
    
    /**
     * An error has been produced and the uploader is unabled to upload a file
     */
    const UPLOAD_STATUS_ERROR     = 300;
    
    /**
     * Uploader will upload the file once the container form is submitted
     *
     */
    const UPLOAD_MODE_SYNCHRONOUS  = 1;
    
    /**
     * Uploader will upload the file as soon as the user select it (without waiting to submit the container form)
     *
     */
    const UPLOAD_MODE_ASYNCHRONOUS = 2;
    
    
    public function setSize($size);
    
    public function getSize();
    
    public function setCurrentSize($current_size);
    
    public function getCurrentSize();
    
    public function setRate($rate);
    
    public function getRate();
    
    public function setFilename($filename);
    
    public function getFilename();
    
    public function setType($type);
    
    public function getType();

    public function setTempFile($temp_file);
    
    public function getTempFile();
    
    public function setStatus($status);
    
    public function getStatus();
    
    public function save($dest_path);
    
    public function setUploadMode($upload_mode);
    
    public function getUploadMode();
    
    public function clearFile();
            
}
