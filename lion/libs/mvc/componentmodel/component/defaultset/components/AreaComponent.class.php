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
 * Area can be used to enclose one of more components.
 * <br>
 * Area components are usefull to show or hidden all the enclosed components
 * at the same time by just making the area visible or not.<br>
 * <br>
 * Area tag is "area"<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:area name="my_area">
 *    
 *     <comp:label name="your_name_label" text="Your name:"/>
 *     <comp:inputbox name="your_name"/>
 *     <br>
 *     <comp:label name="your_email" text="Your email:"/>
 *     
 *   </comp:area>
 * 
 * </code>
 * 
 * As in this example, we can show or hidden all the enclosed components by just making visible the "my_area" component:
 * 
 * <code>
 * 
 *   //hidden all the components enclosed in my_area:
 *   $my_area = $this->getComponent('my_area');
 *   $my_area->setVisible(false);
 * 
 * </code>
 * <br>
 * <br>
 * See the area component in action here: {@link http://www.lionframework.org/components/area.html}
 *
 */
class __AreaComponent extends __UIContainer implements __IPoolable {
        
}

