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
 * @package    Model
 * 
 */

/**
 * This class contains both the model and view. 
 * Instances of this class are usually created by actions commands.
 * A ModelAndView class can contains the View class for rendering, or also a view name that
 * will be used to resolve the View technology.
 * 
 * @see __Action
 *
 */
class __ModelAndView {

    protected $_model     = null;
    protected $_view      = null;
    protected $_view_code = null;

    /**
     * The constructor method.
     * This constructor accept several combination of input parameters:
     * 
     * {@example ModelAndView.constructor.php}
     *
     */
    public function __construct() {
        //By default:
        $this->_model = new __ModelMap();
        $args     = func_get_args();
        $num_args = func_num_args();
        if($num_args > 0) {
            $argmatch = strtoupper(implode(",", array_map('gettype', $args)));
            // 'STRING,STRING,XXX' => 'STRING,STRING,OBJECT'
            if($num_args == 3 && strpos($argmatch, 'STRING,STRING,') === 0) {
                $argmatch = 'STRING,STRING,OBJECT';
            }
            // 'STRING,XXX' && XXX != MODELMAP => 'STRING,OBJECT'
            else if($num_args == 2 && strpos($argmatch, '__MODELMAP') === false) {
                $argmatch = 'STRING,OBJECT';
            }
            
            switch( $argmatch ) {
                case '__VIEW': 
                    $this->setView($args[0]);
                    break;
                    
                case 'STRING': 
                    $this->setViewCode($args[0]);
                    break;
        
                case '__VIEW,__MODELMAP': 
                    $this->setView($args[0]);
                    $this->setModel($args[1]);
                    break;    
                
                case 'STRING,__MODELMAP':
                    $this->setViewCode($args[0]);
                    $this->setModel($args[1]);
                    break;
                
                case '__VIEW,STRING,OBJECT':
                    $this->setView($args[0]);
                    $this->addObject($args[1], $args[2]);
                    break;
                    
                case 'STRING,STRING,OBJECT':
                    $this->setViewCode($args[0]);
                    $this->addObject($args[1], $args[2]);
                    break;
                
                case 'STRING,OBJECT':
                    $this->addObject($args[0], $args[1]);
                    break;
                    
                default:
                    //exception!
                    break;
            }
        }
    }

    public function __get($key) {
       return $this->getObject($key);
    }

    public function __set($key, $value) {
        $this->addObject($key, $value);
    }    
    
    public function setView(__View &$view) {
        $this->_view = &$view;
    }
    
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    public function setModel(&$model) {
        $this->_model =& $model;
    }
    
    public function addObject($key, $value) {
        $this->_model->add($value, $key);
    }
    
    public function getObject($key) {
        $return_value = null;
        if($this->_model->hasKey($key)) {
            $return_value = $this->_model->get($key);
        }
        return $return_value;
    }
    
    public function &getModel() {
        return $this->_model;
    }

    public function getViewCode() {
        return $this->_view_code;
    }
    
    public function &getView() {
        return $this->_view;
    }
    
}