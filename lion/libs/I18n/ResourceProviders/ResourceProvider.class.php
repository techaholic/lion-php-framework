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


/**
 * This is the base class for all resource providers classes used by the LION framework.
 * This contains some template methods such as LoadResource/s, SaveResource/s. 
 * Only methods for connect/disconnect from the storage media, and load/store operations are specialiced in derived classes by
 * implementing some abstract methods of __ResourceProviderBase class.
 *
 */
abstract class __ResourceProvider {
    
    /**
     * This variable stores the resource type to be returned by this resource provider. By default it returns MessageResource resources
     *
     * @var string
     */
    protected $_resource_class = null;
        
    /**
     * Posible values are:                                                                                         
     * action:  Resources are loaded in case a new action is requested.
     *          When the request execution finishes, resources are unloaded from memory
     * session: Resources are loaded once and persist along the session.
     */
    private $_persistence_level = PERSISTENCE_LEVEL_SESSION;
    
    /**
     * A descriptive text regarding resources loaded by the current instance.
     *
     * @var string
     */
    protected $_description = null;

    protected $_highlight_resources = false;
    
    public function __construct() {
        $this->_resource_class = __ContextManager::getInstance()->getCurrentContext()->getPropertyContent('DEFAULT_RESOURCE_CLASS');
        $this->_highlight_resources = __ApplicationContext::getInstance()->getPropertyContent('HIGHLIGHT_I18N_RESOURCES');
    }
        
    final public function setDescription($description) {
        $this->_description = $description;
    }
    
    final public function getDescription() {
        return $this->_description;
    }    
                
    public function setResourceType($resource_class) {
        $this->_resource_class = $resource_class;
    }
    
    public function getResourceType() {
        return $this->_resource_class;
    }
        
    public function setPersistenceLevel($persistence_level) {
        $this->_persistence_level = $persistence_level;
    }
    
    public function getPersistenceLevel() {
        return $this->_persistence_level;
    }
    
    protected function _createResource($key, $value) {
        $resource_class = $this->_resource_class;
        if($this->_highlight_resources) {
            $value = '<FONT style="BACKGROUND-COLOR: yellow">' . $value . '</FONT>';
        }
        return new $resource_class($key, $value);
    }
        
    /**
     * This method will load language specific resources from the concrete storage media
     *
     * @param string The language iso code to load resources from
     */
    abstract public function loadResources($language_iso_code, __ActionIdentity $action_identity = null);
    
}