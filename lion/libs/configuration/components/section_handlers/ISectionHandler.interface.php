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
 * @package    Configuration
 * 
 */

/**
 * This is the interface that should implement all classes in charge of resolution of configuration settings.
 * 
 * A {@link __ConfigurationSection} instance can have associated a class implementing the {@link __ISectionHandler}. 
 * When a section with a section handler associated is requested by the {@link __Configuration::getSection} method,
 * the section handler will read the content of the section in order to create other components that will be returned
 * by the getSection instead of the requested {@link __ConfigurationSection} instance.
 * If no section handlers are associated, the original {@link __ConfigurationSection} will be returned.
 * 
 * This mechanism is usefull to transform some configuration pieces in terms of application components just when needed.
 * 
 */
interface __ISectionHandler {
    
    /**
     * Implemented by all configuration section handlers in order to transform a {@link __ConfigurationSection} 
     * instance in tems of application components.
     * 
     * @param __ConfigurationSection $section The {@link __ConfigurationSection} instance to process
     * 
     * @return mixed The configuration section transformed into application components
     */
    public function &process(__ConfigurationSection &$section);
    
}