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
 * @package    WebFlow
 * 
 */

class __FlowStateFactory {

    const ACTION_STATE = 1;
    const START_STATE = 2;
    const END_STATE = 3;
    const DECISION_STATE = 4;
    const SUBFLOW_STATE = 5;
    
    static public function createState($state_type) {
        $return_value = null;
        switch((int)$state_type) {
            case self::ACTION_STATE:
                $return_value = new __ActionFlowState();  
                break;
            case self::START_STATE:
                $return_value = new __StartFlowState();  
                break;
            case self::END_STATE:
                $return_value = new __EndFlowState();  
                break;
            case self::DECISION_STATE:
                $return_value = new __DecisionFlowState();  
                break;
            case self::SUBFLOW_STATE:
                $return_value = new __SubFlowState();  
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Unknow flow state type: ' . $state_type);
                break;
        }
        return $return_value;
    }
    
}
