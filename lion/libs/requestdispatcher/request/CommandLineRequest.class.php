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
 * @package    Request
 * 
 */

class __CommandLineRequest extends __Request {

    public function readClientRequest() {
        $parameters = $this->_parseArguments($_SERVER['argv']);
        $this->_requested_parameters[REQMETHOD_COMMAND_LINE] = array_change_key_case($parameters);
        $this->_requested_parameters[REQMETHOD_ALL] = array_change_key_case($parameters);
        $this->_request_method = REQMETHOD_COMMAND_LINE;
    }

    public function getFrontControllerClass() {
        return __CurrentContext::getInstance()->getPropertyContent('COMMAND_LINE_FRONT_CONTROLLER_CLASS');
    }

    public function &getFilterChain() {
        $return_value = null;
        return $return_value;
    }

    public function hasFilterChain() {
        return false;
    }

    protected function _parseArguments($argv) {
        $return_value = array();
        foreach ($argv as $arg) {
            if (preg_match('/\-\-([^=]+)\=(.*)/',$arg, $reg)) {
                $return_value[$reg[1]] = $reg[2];
            } elseif(preg_match('/\-([a-zA-Z0-9])/',$arg, $reg)) {
                $return_value[$reg[1]] = 'true';
            }

        }
        return $return_value;


    }

}