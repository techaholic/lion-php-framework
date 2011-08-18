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
 * This class represents a file uploader component.
 * 
 * A filebox has 2 working modes:<br>
 *  - Synchronous mode: The filebox uploads the file just when the container form is submitted to<br>
 *  - Asynchronous mode: The filebox uploads the file as soon as it has been selected by the user<br>
 * <br>
 * The mode can be set by setting the <b>uploadMode</b> attribute. By default a filebox is synchronous.<br>
 * <br>
 * In asynchronous mode, as soon as a filebox finish uploading the file it shows a checkbox checked with the name of the already uploaded file.<br>
 * The user can unchek the checkbox, cancelling (reseting) the uploaded file<br>
 * <br>
 * In synchronous mode, the filebox works as an HTML file element.<br>
 * <br>
 * Note: If APC extension is present, a filebox can show a progressbar showing the the upload progress (only in asynchronous mode)<br>
 * <br>
 * FileBox tag is <b>filebox</b><br>
 * 
 * i.e.
 * <code>
 * 
 *   <comp:filebox name = "client_picture" 
 *           uploadMode = "asynchronous" 
 *         showProgress = "yes"/>
 * 
 * </code>
 * 
 * A upload component can show the upload progress by setting the <b>showProgress</b> property to true.<br>
 * 
 * While a filebox component is uploading a file to the server in asynchronous mode, the container form will wait until the component has uploaded the file.
 * We can also show a progress bar to the submit button by setting the container form into the <b>waitingComponents</b> attribute.<br>
 * 
 * i.e. 
 * 
 * <code>
 *   <comp:form name="client_form">
 * 
 *     ...
 *     <comp:filebox name = "client_picture" 
 *             uploadMode = "asynchronous" 
 *           showProgress = "yes" 
 *      waitingComponents = "client_form"/>
 *     ...
 * 
 *     <comp:commandbutton type = "submit" 
 *                         name = "submit_client_form" 
 *                      caption = "Submit"/>
 * 
 *   </comp:form>   
 * 
 * 
 * </code>
 * 
 * waitingComponents property accepts a comma-separated list of components that will wait until the filebox is uploading a file.
 * 
 * We can restrict type/size of files to be uploading by adding a validation rule to restrict both parameters<br>
 * i.e.
 * <code>
 *
 *  <comp:filebox name = "client_picture" 
 *          uploadMode = "asynchronous" 
 *        showProgress = "yes" 
 *   waitingComponents = "client_form"/>
 * 
 *  <comp:validationrule component = "client_picture" 
 *              validateOnlyOnBlur = "true" 
 *                     maxFileSize = "5242880" 
 *               allowedExtensions = "jpg,gif,png"/>
 * 
 * </code>
 * 
 * Once we have upload a file by ussing this component, we can save the file by ussing the <b>save</b> method, i.e.
 * <code>
 * 
 *   public function client_form_submit(__UIEvent &$event) {
 * 
 *       //save the picture to the harddrive:
 *       $client_picture = $this->getComponent('client_picture');
 *       $client_picture->save('destination_path_and_filename.jpg');
 * 
 *   } 
 * 
 * </code>
 * 
 * In asynchronous mode, a filebox raises the <b>uploaded</b> event once a file has been uploaded successfully to the server.<br>
 * We can also save the picture as soon as it's uploaded to the server by handling this event.
 * 
 * i.e.
 * <code>
 * 
 *   public function client_picture_uploaded(__UIEvent &$event) {
 * 
 *       //save the picture to the harddrive once it has been uploaded:
 *       $client_picture = $this->getComponent('client_picture');
 *       $client_picture->save('destination_path_and_filename.jpg');
 * 
 *   } 
 * 
 * </code>
 *   
 * We do not recommend to use the uploaded event with a filebox inside a form, but handle it within the submit event.<br>
 * The uploaded event could be usefull for filebox outside form components.<br>
 * <br>
 * <br>
 * <br>
 * See the filebox component in action here: {@link http://www.lionframework.org/components/filebox.html}
 *  
 * @see __ItemListComponent, __ListBoxComponent
 * 
 */
class __FileBoxComponent  extends __InputComponent implements __IUploaderComponent {
    
    protected $_size = null;
    protected $_current_size = null;
    protected $_rate = null;
    protected $_filename = null;
    protected $_temp_file = null;
    protected $_status = __IUploaderComponent::UPLOAD_STATUS_READY;
    protected $_upload_mode = __IUploaderComponent::UPLOAD_MODE_SYNCHRONOUS;
    protected $_icon  = null;
    protected $_type = null;
    
    /**
     * Reset the filebox value
     *
     */
    public function reset() {
        $this->clearFile();
        $this->resetValidation();
    }

    /**
     * Alias of {@link __FileBoxComponent::reset()} method
     *
     */
    public function clearFile() {
        $this->_size = null;
        $this->_current_size = null;
        $this->_rate = null;
        $this->_filename = null;
        $this->_temp_file = null;
        $this->_status = __IUploaderComponent::UPLOAD_STATUS_READY;
        $this->setValue(null);
    }
    
    /**
     * To be used internally by the framework, set the uploaded file size
     *
     * @param integer $size The size in bytes
     */
    public function setSize($size) {
        $this->_size = $size;
    }
    
    /**
     * Get the uploaded file size in bytes
     *
     * @return integer The file size in bytes
     */
    public function getSize() {
        return $this->_size;
    }
    
    /**
     * To be used internally by the framework, set the in-progress uploaded file portion size
     *
     * @param integer $current_size The in-progress uploaded file portion size in bytes
     */
    public function setCurrentSize($current_size) {
        $this->_current_size = $current_size;
    }
    
    /**
     * Get the in-progress uploaded portion size in bytes
     *
     * @return integer The uploaded file portion size in bytes
     */
    public function getCurrentSize() {
        return $this->_current_size;
    }
    
    /**
     * To be used internally by the framework, set the current upload rate
     *
     * @param integer $rate
     */
    public function setRate($rate) {
        $this->_rate = $rate;
    }
    
    /**
     * Get the upload rate
     *
     * @return integer The upload rate in bytes
     */
    public function getRate() {
        return $this->_rate;
    }
    
    /**
     * Set the filename represented by the current filebox. 
     * The filename is the value of a filebox from the point of view of a {@link __IValueHolder}.
     *
     * @param string $filename
     */
    public function setFilename($filename) {
        $this->setValue($filename);
    }
    
    public function setType($type) {
        $this->_type = $type;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    /**
     * Alias of setFilename
     *
     * @param string $value
     */
    public function setValue($value) {
        $this->_filename = $value;
        parent::setValue($value);
    }
    
    /**
     * Get the filename represented by the current filebox
     *
     * @return string
     */
    public function getFilename() {
        return $this->_filename;
    }

    /**
     * To be used internally by the framework, to set the name of the temporal file where the uploaded file is stored temporally
     *
     * @param string $temp_file
     */
    public function setTempFile($temp_file) {
        $this->_temp_file = $temp_file;
    }
    
    /**
     * Gets the name of the file where the uploaded file is stored as temporally
     *
     * @return string
     */
    public function getTempFile() {
        return $this->_temp_file;
    }
    
    /**
     * Set a value representing the status of the current filebox component.
     * 
     * Status are defined within the {@link __IUploaderComponent}, being the most important:<br>
     * <br>
     *  - {@link __IUploaderComponent::UPLOAD_STATUS_READY} (1): Filebox is ready to upload a new file<br>
     *  - {@link __IUploaderComponent::UPLOAD_STATUS_UPLOADING} (2): Filebox is uploading a file<br>
     *  - {@link __IUploaderComponent::UPLOAD_STATUS_DONE} (3): Filebox has uploaded a file successfully<br>
     *  - {@link __IUploaderComponent::UPLOAD_STATUS_CANCELLED} (4): Upload has been cancelled by the user<br>
     *  - {@link __IUploaderComponent::UPLOAD_STATUS_UNKNOWN} (100): Generally due to an error.
     *  - {@link __IUploaderComponent::UPLOAD_STATUS_ERROR} (300): An error has been produced and the filebox is unabled to upload a file<br>
     * 
     * @param integer $status
     * 
     * @see __IUploaderComponent
     */
    public function setStatus($status) {
        $this->_status = $status;
    }
    
    /**
     * Get a value representing the status of the current filebox
     *
     * @return integer
     */
    public function getStatus() {
        return $this->_status;
    }
    
    /**
     * Saves the uploaded file in the given path.
     *
     * @param string $dest_path The path to save the file to
     */
    public function save($dest_path) {
        if($this->getStatus() == __IUploaderComponent::UPLOAD_STATUS_DONE) {
            if(is_dir($dest_path)) {
                $filename = $this->getFilename();
            }
            else {
                $filename  = basename($dest_path);
                $dest_path = dirname($dest_path);
            }
            if(is_dir($dest_path) && is_writable($dest_path)) {
                copy($this->_temp_file, $dest_path . DIRECTORY_SEPARATOR . $filename);
                $this->setFilename($filename);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Can not save file: The specified directory does not exists or is not writable: ' . $dest_path);
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Can not save file: The file was not uploaded successfully to the server');
        }
        
    }
    
    public function getHandledEvents() {
        return array('uploading', 'cancelupload');
    }     
    
    public function handleEvent(__UIEvent &$event) {
        $event_name = strtoupper($event->getEventName());
        switch($event_name) {
            case 'UPLOADING':
                //free the session to allow more incoming requests:
                //@todo change the session handler to synchronize sessions between requests
                __SessionManager::getInstance()->endSession();
                $this->setStatus(__IUploaderComponent::UPLOAD_STATUS_UPLOADING);
                $this->setProgress(0);
                while($this->getStatus() == __IUploaderComponent::UPLOAD_STATUS_UPLOADING) {
                    $this->updateStatus();
                    sleep(1);
                }
                break;                
            case 'CANCELUPLOAD':
                $this->reset();
                $this->setStatus(__IUploaderComponent::UPLOAD_STATUS_CANCELLED);
                break;
        }
    }
    
    public function isEventHandled($event_name) {
        $return_value = false;
        $event_name = strtoupper($event_name);
        if($event_name == 'UPLOADING' || $event_name == 'CANCELUPLOAD') {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Set a value representing the upload mode, which it can be:
     *  
     *  - {@link __IUploaderComponent::UPLOAD_MODE_SYNCHRONOUS} (1): (by default) The file is uploaded when the form is submitted
     *  - {@link __IUploaderComponent::UPLOAD_MODE_ASYNCHRONOUS} (1): The file is uploaded as soon as it's selected by the user
     * 
     *
     * @param integer $upload_mode
     */
    public function setUploadMode($upload_mode) {
        if(!is_numeric($upload_mode)) {
            if(strtoupper($upload_mode) == 'ASYNCHRONOUS') {
                $this->_upload_mode = __IUploaderComponent::UPLOAD_MODE_ASYNCHRONOUS;
            }
            else {
                $this->_upload_mode = __IUploaderComponent::UPLOAD_MODE_SYNCHRONOUS;
            }
        }
        else if($upload_mode == __IUploaderComponent::UPLOAD_MODE_SYNCHRONOUS ||
                $upload_mode == __IUploaderComponent::UPLOAD_MODE_ASYNCHRONOUS) {
            $this->_upload_mode = $upload_mode; 
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unknow upload mode: ' . $upload_mode);
        }
    }
    
    /**
     * Get a value representing the upload mode
     *
     * @return integer
     */
    public function getUploadMode() {
        return $this->_upload_mode;
    }
        
    /**
     * To be used internally by the framework, and just in case the APC extension is present.
     * 
     * This method updates the status of the current component by asking to APC about the upload progress
     *
     */
    public function updateStatus() {
        if(function_exists('apc_fetch')) {
            $apc_key = 'upload_' . $this->getId();
            $upload_status = apc_fetch($apc_key);
            if($upload_status != false) {
	            if(key_exists('cancel_upload', $upload_status) && $upload_status['cancel_upload']) {
	                $this->setStatus(__IUploaderComponent::UPLOAD_STATUS_CANCELLED);
	                apc_delete ( $apc_key );
	            }
	            else {
	                if ($upload_status['done']) {
	                    $progress = 100;
	                    $this->setStatus(__IUploaderComponent::UPLOAD_STATUS_DONE);
                        apc_delete ( $apc_key );
	                }
	                else {
    	                if ($upload_status['total'] == 0) {
    	                    $progress = 0;
    	                }
    	                else {
    	                    $progress = (int) ($upload_status['current'] / $upload_status['total'] * 100);
    	                }
	                }
                    $this->setProgress($progress);
	            }
            }
        }
        else {
            $this->setStatus(__IUploaderComponent::UPLOAD_STATUS_UNKNOWN);
        }
    }
    
    /**
     * Set the url to an image to be shown closet to the filebox in case the status is UPLOAD_STATUS_DONE.
     * The image is usually an image representing the uploaded file (i.e. an icon corresponding to the file type, or a thumbnail)
     *
     * @param string $icon
     */
    public function setIcon($icon) {
        $this->_icon = $icon;
    }
    
    /**
     * Get the url of the image to be shown in case the status is UPLOAD_STATUS_DONE
     *
     * @return string
     */
    public function getIcon() {
        return $this->_icon;
    }
    
    
}
