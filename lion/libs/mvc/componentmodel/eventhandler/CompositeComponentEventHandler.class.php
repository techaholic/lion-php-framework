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

class __CompositeComponentEventHandler extends __EventHandler implements __ICompositeComponentEventHandler {

    protected $_composite_component = null;
    
    public function setCompositeComponent(__ICompositeComponent &$composite_component) {
        $this->_composite_component =& $composite_component;
    }
    
    public function &getCompositeComponent() {
        return $this->_composite_component;
    }
    
    public function raiseEvent($event_name, $extra_info = array()) {
        if($this->_composite_component != null) {
            $view_code = $this->_composite_component->getViewCode();
            $event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
            if($event_handler != null) {
                $event = new __UIEvent($event_name, $extra_info, $this->_composite_component);
                $event_handler->handleEvent($event);
            }
        }
    }
    
    final public function setupProperties() {
        $composite_component = $this->getCompositeComponent();
        $composite_component->setupProperties($this);
    }
    
}
