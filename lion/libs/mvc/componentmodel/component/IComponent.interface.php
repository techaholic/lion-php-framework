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

interface __IComponent {

    public function getId();
    
    public function setViewCode($view_code);
    
    public function getViewCode();
    
    public function setContainer(__IContainer &$container);
    
    public function &getContainer();
    
    public function setName($name);

    public function getName();
    
    public function setIndex($index);
    
    public function getIndex();
    
    public function setAlias($alias);
    
    public function getAlias();
    
    public function setDisabled($disabled);
    
    public function getDisabled();

    public function setVisible($enabled);
    
    public function getVisible();
    
    public function hasProperty($property_name);
    
    public function getProperty($property_name);
    
    public function setProperty($property_name, $property_value);

    public function validate();
    
    public function setPersist($persist);
    
    public function getPersist();
    
    public function isEventHandled($event_name);
    
    public function getHandledEvents();
    
    public function handleEvent(__UIEvent &$event);
    
    public function setProgress($progress);
    
    public function getProgress();
    
    public function handleCallback(__IRequest &$request);
    
    public function __toString();
    
}