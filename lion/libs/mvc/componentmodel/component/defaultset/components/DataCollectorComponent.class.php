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
 * Data collector is a component designed to collect data while rendering the UI.
 * It's usefull to set information rendered to the view in order to be retrieved from the event handler.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:datacollector name="invoice_info">
 *     <comp-property name="invoice_id">{$invoice_id}</comp-property>
 *     <comp-property name="client_id">{$client_id}</comp-property>
 *   </comp:datacollector>
 * 
 * </code>
 * 
 * Once we have stored information from the view to the data collector, we can retrieve it from the event handler.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   public function beforeRender() {
 *       $invoice_data_collector = $this->getComponent('invoice_info');
 *       //get the invoice id as well as the client id:
 *       $invoice_id = $invoice_data_collector->invoice_id;
 *       $client_id  = $invoice_data_collector->client_id;
 *   }
 * 
 * </code>
 * 
 * The data collector is not poolable, which means that can be retrieved only in create and beforeRender events.<br>
 * <br>
 *
 */
class __DataCollectorComponent extends __UIComponent {

    
}

