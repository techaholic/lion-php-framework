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
 * The slider is a component equipped with a small bar, also called a thumb, that slides along a visible line. 
 * To use the slider control, the user can drag the thumb in one of two directions.
 * 
 * A slider is configured with a set of values from a minimum to a maximum. Therefore, the user can make a selection included in that range.
 * 
 * Slider tag is <b>slider</b>
 * i.e.
 * <code>
 *  
 *   <comp:slider lowerLimit = "1"
 *                upperLimit = "50"
 *                     value = "25"
 *                     name="my_slider"/>
 * 
 * </code>
 * 
 * <b>lowerLimit</b> and <b>upperLimit</b> attributes define the numeric range of the slider. <b>value</b> defines the initial value.<br>
 * In this example, we have configured a simple slider defining a range between 1 and 50, with a default value of 25.<br>
 * <br>
 * We can also link a label to an slider control, in order to show the slider value. To do that, we just need to set the <b>inputReceiver</b> attribute:<br>
 * <code>
 * 
 *   <comp:slider lowerLimit = "1"
 *                upperLimit = "50"
 *                     value = "25"
 *             inputReceiver = "slider_label_2" 
 *                      name = "example2"/>
 * 
 *    Slider value: <comp:label name="slider_label_2" text="25"/>
 *
 * </code> 
 * 
 * By default, an slider show the numeric value without decimals. To allow decimals, we can set the <b>decimals</b> attribute to the number of decimals to show to<br>
 * <br>
 * In the other hand, an slider is rendered in an horizontal axis by default.<br>
 * However we can alter it by setting the <b>axis</b> attribute:<br>
 * <code>
 * 
 *   <comp:slider lowerLimit = "1"
 *                upperLimit = "50"
 *                     value = "25"
 *                      axis = "vertical"
 *                      name = "example2"/>
 * 
 * </code>
 * The axis attribute allowes <i>horizontal</i> (by default) and <i>vertical</i>.<br>
 * <br>
 * Finally, we can customize the slider look & feel by setting the <b>trackCssClass</b> and <b>handleImg</b> attributes:<br>
 *  - trackCssClass is the css class that corresponds with the slider tracking line.<br>
 *  - handleImg is the image that will be render as the handle (the piece that we move along the tracking line)<br>
 * 
 * An example of track css class could be like the following example:<br>
 * <code>
 * 
 *   .track {
 *       background: transparent 
 *                   url(track_background_image.png) 
 *                   repeat-x 
 *                   top left;
 *   }
 * 
 * </code>
 * 
 * Of course, we can change the dimensions of the slider by setting the <b>width</b> and <b>height</b> attribute, as well as the handle dimensions by setting the <b>handleWidth</b> and <b>handleHeight</b> attributes.<br>
 * <br>
 * <br>
 * <br>
 * See the slider component in action here: {@link http://www.lionframework.org/components/slider.html}
 */
class __SliderComponent extends __InputComponent {

    const AXIS_HORIZONTAL = "horizontal";
    const AXIS_VERTICAL   = "vertical";
    
    protected $_upper_limit = 1;
    protected $_lower_limit = 0;
    protected $_input_receiver = null;
    protected $_left_track_cssclass = null;
    protected $_right_track_cssclass = null;
    protected $_track_cssclass = null;
    protected $_handle_img = null;
    protected $_width = null;
    protected $_height = null;
    protected $_handle_width = "10px";
    protected $_handle_height = "15px";
    protected $_value = 0;
    protected $_decimals = 0;
    protected $_axis = self::AXIS_HORIZONTAL;

    /**
     * Set the axis in which the slider will be rendered in.
     * This property allow the following values:
     *  - horizontal ({@link __SliderComponent::AXIS_HORIZONTAL}): to render the tracking line in the horizontal axis
     *  - vertical ({@link __SliderComponent::AXIS_VERTICAL}): to render the tracking line in the vertical axis
     *
     * @param string $axis
     */
    public function setAxis($axis) {
        if($axis == self::AXIS_VERTICAL) {
            $this->_axis = $axis;
        }
        else {
            $this->_axis = self::AXIS_HORIZONTAL;
        }
    }
    
    /**
     * Get the axis in which the slider will be rendered in
     *
     * @return string
     */
    public function getAxis() {
        return $this->_axis;
    }
    
    /**
     * Set a width for current component
     *
     * @param integer $width
     */
    public function setWidth($width) {
        $this->_width = $width;
    }
    
    /**
     * Get the width for current component
     *
     * @return integer
     */
    public function getWidth() {
        if($this->_width !== null) {
            $return_value = $this->_width;
        }
        else {
            if($this->_axis == self::AXIS_HORIZONTAL) {
                $return_value = "200px";
            }
            else {
                $return_value = "9px";
            }
        }
        return $return_value;
    }
    
    /**
     * Set a height for current component
     *
     * @param integer $height
     */
    public function setHeight($height) {
        $this->_height = $height;
    }
    
    /**
     * Get the height for current component
     *
     * @return integer
     */
    public function getHeight() {
        if($this->_height !== null) {
            $return_value = $this->_height;
        }
        else {
            if($this->_axis == self::AXIS_HORIZONTAL) {
                $return_value = "9px";
            }
            else {
                $return_value = "100px";
            }
        }
        return $return_value;
    }
    
    /**
     * Set a width for the handle piece (the one the user moves)
     *
     * @param integer $handle_width
     */
    public function setHandleWidth($handle_width) {
        $this->_handle_width = $handle_width;
    }
    
    /**
     * Get the width of the handle piece
     *
     * @return integer
     */
    public function getHandleWidth() {
        return $this->_handle_width;
    }
    
    /**
     * Set a height for the handle piece (the one the user moves)
     *
     * @param integer $handle_height
     */
    public function setHandleHeight($handle_height) {
        $this->_handle_height = $handle_height;
    }
    
    /**
     * Get the height of the handle piece
     *
     * @return integer
     */
    public function getHandleHeight() {
        return $this->_handle_height;
    }
    
    /**
     * Set an image for the handle piece (the one the user moves)
     *
     * @param string $handle_img
     */
    public function setHandleImg($handle_img){
        $this->_handle_img = $handle_img;
    }
    
    /**
     * Get the image of the handle piece
     *
     * @return string
     */
    public function getHandleImg(){
        return $this->_handle_img;
    }
    
    /**
     * Get the css class applied to the left corner of the tracking line
     *
     * @return string
     */
    public function getLeftTrackCssClass(){
        return $this->_left_track_cssclass;
    }
    
    /**
     * Set a css class to be applied to the left corner of the tracking line
     *
     * @param string $left_track_cssclass
     */
    public function setLeftTrackCssClass($left_track_cssclass){
        $this->_left_track_cssclass = $left_track_cssclass;
    }
    
    /**
     * Get the css class applied to the right corner of the tracking line
     *
     * @return string
     */
    public function getRightTrackCssClass(){
        return $this->_right_track_cssclass;
    }
    
    /**
     * Set a css class to be applied to the right corner of the tracking line
     *     
     * @param string $right_track_cssclass
     */
    public function setRightTrackCssClass($right_track_cssclass){
        $this->_right_track_cssclass = $right_track_cssclass;
    }
    
    /**
     * Get the css class applied to the tracking line
     *
     * @return string
     */
    public function getTrackCssClass(){
        return $this->_track_cssclass;
    }
    
    /**
     * Set a css class to be applied to the tracking line
     *
     * @param string $track_cssclass
     */
    public function setTrackCssClass($track_cssclass){
        $this->_track_cssclass = $track_cssclass;
    }
    
    
    /**
     * Set the upper limit within the numeric range handled by the slider
     *
     * @param integer|float $upper_limit
     */
    public function setUpperLimit($upper_limit) {
        if(is_numeric($upper_limit)) {
            $this->_upper_limit = $upper_limit;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong value for upper limit: ' . $upper_limit);
        }
    }

    /**
     * Set the lower limit within the numeric range handled by the slider
     *
     * @param integer|float $lower_limit
     */
    public function setLowerLimit($lower_limit) {
        if(is_numeric($lower_limit)) {
            $this->_lower_limit = $lower_limit;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong value for lower limit: ' . $lower_limit);
        }
    }
    
    /**
     * Get the upper limit within the numeric range handled by the slider
     *
     * @return integer|float
     */
    public function getUpperLimit() {
        return $this->_upper_limit;
    }
    
    /**
     * Get the lower limit within the numeric range handled by the slider
     *
     * @return integer|float
     */
    public function getLowerLimit() {
        return $this->_lower_limit;
    }
    
    /**
     * Set the component id to be used to show the slider value
     * The component must be either a {@link __LabelComponent} or a {@link __InputBoxComponent}.
     *
     * @param string $input_receiver
     */
    public function setInputReceiver($input_receiver){
        $this->_input_receiver = $input_receiver;
    }
    
    /**
     * Get the component used to show the slider value
     *
     * @return __LabelComponent|__InputBoxComponent
     */
    public function &getInputReceiver(){
        $return_value = null;
        if(__ComponentHandlerManager::getInstance()->hasComponentHandler($this->_view_code)) {
            $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
            if($component_handler != null) {
                if($this->_input_receiver != null) {
                    if($component_handler->hasComponent($this->_input_receiver)) {
                        $return_value = $component_handler->getComponent($this->_input_receiver);
                        if(!$return_value instanceof __InputBoxComponent && !$return_value instanceof __LabelComponent ) {
                            throw __ExceptionFactory::getInstance()->createException('Wrong receiver component class. Only inputbox and labels are allowed, not ' . get_class($return_value));
                        }
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Input receiver not found: ' . $this->_input_receiver);
                    }
                }
            }
        }
        return $return_value;
    }
    
    /**
     * Set the value of the slider
     *
     * @param integer|float $value
     */
    public function setValue($value) {
        $return_value = parent::setValue($value);
        $this->_updateInputReceiver();
    }
    
    protected function _updateInputReceiver() {
        $input_receiver = $this->getInputReceiver();
        $value = round($this->getValue(), $this->getDecimals());
        if($input_receiver instanceof __InputBoxComponent ) {
            $input_receiver->setValue($value);
        }
        else if($input_receiver instanceof __LabelComponent ) {
            $input_receiver->setText($value);
        }
    }
    
    /**
     * Set the number of decimals to be shown within the input receiver (if any).
     * By default, the input receiver will round the value without decimals.
     *
     * @param integer $decimals
     */
    public function setDecimals($decimals) {
        $this->_decimals = (int) $decimals;
    }
    
    /**
     * Get the number of decimals to be shown within the input receiver
     *
     * @return integer
     */
    public function getDecimals() {
        return $this->_decimals;
    }
    
}
