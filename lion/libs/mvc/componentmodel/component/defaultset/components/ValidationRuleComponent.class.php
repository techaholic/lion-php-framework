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
 * ValidationRule is a component designed to validate user inputs within other components.
 * ValidationRule is the out of the box {@link __IValidator} implementation, offering enough services to cover the most typical user input validations.<br>
 * Note that ValidationRule generates client-side javascript code to perform validations without calling to the server in most of the cases, but it also perform a double validation in the server side once a form is submitted.<br>
 *<br>
 * A ValidationRule tag is <b>validationrule</b>.<br>
 * i.e.
 * <code>
 * 
 *   <comp:inputbox name="first_name"/>
 * 
 *   <comp:validationrule component = "first_name" 
 *               validateOnlyOnBlur = "true" 
 *                        mandatory = "true" 
 *                        maxLength = "45"/>
 * 
 * </code>
 * <br>
 * A validation rule references a value holder component to be validated by setting the <b>component</b> attribute with the name of the component to validate to (as shown within the example above).<br>
 * <br><br>
 * <b>Validating lenght:</b><br>
 * <br>
 * We can restrict the minimum and maximum lenght that we allow to input in a component by setting the <b>minLenght</b> and <b>maxLenght</b> attributes:
 * <code>
 * 
 *   <comp:inputbox name="first_name"/>
 * 
 *   <comp:validationrule component = "first_name" 
 *                        minLenght = "1" 
 *                        maxLength = "45"/>
 * 
 * </code>
 * 
 * In our example, we are restricting the lenght of the first name to be between 1 and 45 characters.<br>
 * <br>
 * We can also restrict the lenght to a fixed number of characters by setting the <b>validLength</b> attribute:
 * <code>
 * 
 *   <comp:inputbox name="zipcode"/>
 * 
 *   <comp:validationrule component = "zipcode" 
 *                      validLenght = "5"/>
 * 
 * </code> 
 * <br><br>
 * <b>Validating format:</b><br>
 * <br>
 * We can restrict the format in order to match with a regular expression by setting the <b>pattern</b> attribute:
 * 
 * <code>
 * 
 *   <comp:inputbox name="zipcode"/>
 * 
 *   <comp:validationrule component = "zipcode" 
 *                          pattern = "^\d+$" 
 *                      validLenght = "5"/>
 *  
 * </code>
 * <br><br>
 * <b>Validating mandatory fields:</b><br>
 * <br>
 * We can make a field mandatory by just setting the <b>mandatory</b> attribute as "true". By default, a field is not mandatory.
 * 
 * <code>
 * 
 *   <comp:inputbox name="age"/>
 * 
 *   <comp:validationrule component = "age" 
 *                        mandatory = "true"
 *                          pattern = "^\d+$"/>
 *  
 * </code>
 * 
 * <br><br>
 * <b>Validating acceptance:</b><br>
 * <br>
 * We can also use the <b>acceptance</b> attribute to let the user check a checkbox:
 * <code>
 * 
 *   <comp:checkbox name="accept_toc" caption="I accept terms and conditions"/>
 * 
 *   <comp:validationrule component = "accept_toc" 
 *                       acceptance = "true"/>
 * 
 * </code>
 * <br><br>
 * <b>Validating matching fields:</b><br>
 * <br>
 * We can also use the <b>matchComponent</b> attribute to validate that the value of a component match the value of another component.<br>
 * This feature is really usefull to let the user introduce twice the same value (i.e. a password to avoid mistakes).<br>
 * i.e.
 * <code>
 * 
 *   <comp:inputbox type="password" name="password"/>
 *   <comp:inputbox type="password" name="repeat_password"/>
 *   
 *   <comp:validationrule component = "repeat_password"  
 *                   matchComponent = "password"/>
 * 
 * </code>
 * In our example, the validation rule associated to repeat_password inputbox will check that match with the value input within the password inputbox.
 * 
 * <br><br>
 * <b>Validating numeric values:</b><br>
 * <br>
 * We can also validate numeric values within a range by setting the <b>minimumNumber</b> and <b>maximumNumber</b>:
 * <code>
 * 
 *   <comp:inputbox name="year"/>
 * 
 *   <comp:validationrule component = "year" 
 *                    minimumNumber = "1975"
 *                    maximumNumber = "2100"/>
 * 
 * </code> 
 * <br>
 * This rule will also validate that the user input is a numeric value.<br>
 * The validation rule also has the <b>specificNumber</b> attribute to restrict the user input to an specific number.
 * <br><br>
 * <b>Validating files:</b><br>
 * <br>
 * Uploader components like the {@link __FileBoxComponent} have other kind of parameters to be validated, such the file type, size, ...<br>
 * The validation rule provides specific attribute for those kind of components:<br>
 * <br>
 * We can validate the file extension by setting the <b>allowedExtensions</b> with a comma-separate list of values (i.e. "jpg,gif,png" to allow just jpg, gif or png file extenssions):
 * <code>
 * 
 *   <comp:filebox name="my_image"/>
 * 
 *   <comp:validationrule component = "my_image" 
 *                allowedExtensions = "jpg,gif,png"/>
 * 
 * </code>
 * <br>
 * We can also restrict the maximum size for a file to upload to by setting the <b>maxFileSize</b> attribute to the maximum number of bytes that we allow to be uploaded to.<br>
 * i.e.
 * <code>
 * 
 *   <comp:filebox name="my_image"/>
 * 
 *   <comp:validationrule component = "my_image" 
 *                allowedExtensions = "jpg,gif,png"
 *                      maxFileSize = "5242880"/>
 * 
 * </code>
 * <br><br>
 * <b>Validate on blur:</b><br>
 * <br>
 * One of the most valuable features of the ValidationRule component is the validation on blur capability, which allow the component to be validated as soon as it loses the focus.<br>
 * This capability is brought by the {@link http://www.livevalidation.com LiveValidation} javascript library, in which the ValidationRule component generates code to work with.<br>
 * We can make it possible by just setting the <b>validateOnlyOnBlur</b> attribute to true<br>
 * i.e.
 * <code>
 * 
 *   <comp:inputbox name="zipcode"/>
 * 
 *   <comp:validationrule component = "zipcode" 
 *                          pattern = "^\d+$" 
 *                      validLenght = "5"
 *               validateOnlyOnBlur = "true"/>
 * 
 * </code>
 * <br><br>
 * <b>Custom server-side validations:</b><br>
 * <br>
 * We can also define our own custom validations within the server-side event handler. To do that, we just need to handle the validate event of the component to be validated to.<br>
 * i.e.:
 * <code>
 * 
 *   <comp:inputbox name="email"/>
 * 
 *   <comp:validationrule component = "email" 
 *                           format = "email" 
 *               validateOnlyOnBlur = "true"/>
 * 
 * </code>
 * <br>
 * In this example, we have set a validation rule associated to an email input box.<br>
 * Now image that we need to also check if the email already exists in our database in order to alert the user to choose another email. We can do it by catching the validate event in our event handler.<br>
 * i.e.
 * <code>
 * 
 * class OurEventHandler extends __EventHandler {
 * 
 *     ...
 * 
 *     public function email_validate(__UIEvent &$event) {
 *         $email = $event->getComponent()->getValue();
 *         $already_exists = __ModelProxy::getInstance()
 *                           ->checkIfEmailExists($email);
 *         if($already_exists) {
 *             throw new __ValidationException(
 *                   'The email you have entered already 
 *                    exists in our database. Please choose 
 *                    another one');
 *         }
 *     }
 * 
 *     ...
 * 
 * }
 * 
 * </code>
 * 
 * Is as easy as to define a method as <component_to_validate> + "_validate" to handle the validate event (see the {@tutorial View/Components/View.EventManagementSystem.pkg} tutorial about event handlers). This method will be called as soon as the component is validated, which means that if the component is validated on blur, an ajax call will be performed in order to validate it within the server-side.<br>
 * A validation method must raise a <b>__ValidationException</b> in order to notify a validation error. This special exception is interpreted by the framework in order to show the error message to the user<br>
 * <br>
 * You can also handle validation events associated to forms if you need to validate aspects that does not depend on a single component
 * <br><br>
 * <b>Placing error messages:</b><br>
 * <br>
 * We can customize where to show validation messages. By default, validation messages are shown closet to the component.<br>
 * To define where to show validation messages, we can set the <b>reportAfterElement</b> to define the HTML element in which we're going to show messages after, or we can also set <b>reportAfterComponent</b> to define the component in which we're going to show the message after.<br>
 * i.e.
 * <code>
 * 
 *   <div id="reportErrorsHere" />
 *   <comp:inputbox name="zipcode"/>
 * 
 *   <comp:validationrule component = "zipcode" 
 *                      validLenght = "5"
 *               reportAfterElement = "reportErrorsHere"/>
 * 
 * </code>
 * <br>
 * <br>
 * See the text validation rule component in action here: {@link http://www.lionframework.org/components/validationrule.html}
 *  
 */
class __ValidationRuleComponent extends __UIComponent implements __IPoolable, __IValidator {

	protected $_min_length   = null;
	protected $_max_length   = null;
	protected $_valid_length = null;
	protected $_pattern      = null;
	protected $_format       = null;
	protected $_mandatory    = false;
	protected $_validate_only_on_blur   = false;
	protected $_validate_only_on_submit = true;
	protected $_error_message = null;
	protected $_validation_result = null;
	protected $_wait = 2000;
	protected $_report_after_element = null;
	protected $_acceptance = false;
	protected $_allowed_extensions = null;
	protected $_only_integer = false;
	protected $_specific_number = null;
	protected $_maximum_number = null;
	protected $_minimum_number = null;
	protected $_max_file_size = null;
	protected $_on_invalid = null;

	const FORMAT_EMAIL = '^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$';
	
	/**
	 * Name of component to validate to
	 *
	 * @var string
	 */
	protected $_component = null;
	
	/**
	 * Index of component to validate to (if applicable)
	 *
	 * @var mixed
	 */
	protected $_component_index = null;

	protected $_match_component = null;

	protected $_match_component_index = null;

	protected $_report_after_component = null;

	protected $_report_after_component_index = null;
	
	/**
	 * Set the component name that current validation rule is regarding to
	 *
	 * @param string $component The component name
	 */
	public function setComponent($component) {
		$this->_component = $component;
	}
	
	/**
	 * Retrieves the component name that current validation rule is regarding to
	 *
	 * @return string
	 */
	public function getComponent() {
		return $this->_component;
	}
	
	/**
	 * Set the name of a component to validate if the associated component's value match with the given component's value
	 *
	 * @param string $match_component The component name
	 */
	public function setMatchComponent($match_component) {
	    $this->_match_component = $match_component;
	}
	
	/**
	 * Set the method to execute if the validation fails
	 * 
	 * @param $on_invalid
	 */
	public function setOnInvalid($on_invalid) {
	    $this->_on_invalid = $on_invalid;
	}
	
	/**
	 * Gets the method to be execute if the validation fails (null is by default)
	 * 
	 * @return string
	 */
	public function getOnInvalid() {
	    return $this->_on_invalid;
	}
	
	/**
	 * Get the name of a component to validate if the associated component's value match with the given compoennt's value
	 *
	 * @return string
	 */
	public function getMatchComponent() {
	    return $this->_match_component;
	}

	/**
	 * Set the name of a component to show validation messages after
	 *
	 * @param string $report_after_component
	 */
	public function setReportAfterComponent($report_after_component) {
	    $this->_report_after_component = $report_after_component;
	}
	
	/**
	 * Get the name of the component to show validation messages after
	 *
	 * @return string
	 */
	public function getReportAfterComponent() {
	    return $this->_report_after_component;
	}
	
	/**
	 * Set the mame of an HTML element to show validation messages after
	 *
	 * @param string $element_id
	 */
	public function setReportAfterElement($element_id) {
	    $this->_report_after_element = $element_id;
	}
	
	/**
	 * Get the name of an HTML element ot show validation message after
	 *
	 * @return string
	 */
	public function getReportAfterElement() {
	    return $this->_report_after_element;
	}
	
	/**
	 * Get the component to be validated by the current validator
	 *
	 * @return __IComponent
	 */
	public function getComponentToValidate() {
	    $return_value = null;
	    $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
	    if($component_handler != null) {
	        if($component_handler->hasComponent($this->_component, $this->_component_index)) {
    	        $return_value = $component_handler->getComponent($this->_component, $this->_component_index);
	        }
	        else {
	            throw __ExceptionFactory::getInstance()->createException('Component to validate not found: ' . $this->_component);
	        }
	    }
	    return $return_value;
	}

    /**
     * Get the component to validate if the associated component's value match with the given compoennt's value
     *
     * @return __IComponent
     */
	public function getComponentToMatch() {
	    $return_value = null;
	    if($this->_match_component != null) {
    	    $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
    	    if($component_handler != null) {
    	        if($component_handler->hasComponent($this->_match_component, $this->_match_component_index)) {
    	           $return_value = $component_handler->getComponent($this->_match_component, $this->_match_component_index);
    	        }
    	        else {
    	            throw __ExceptionFactory::getInstance()->createException('Component to match not found: ' . $this->_match_component);
    	        }
    	    }
	    }
	    return $return_value;
	}

	public function getComponentForErrorReporting() {
	    $return_value = null;
	    if($this->_report_after_component != null) {
    	    $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
    	    if($component_handler != null) {
    	        if($component_handler->hasComponent($this->_report_after_component, $this->_report_after_component_index)) {
    	           $return_value = $component_handler->getComponent($this->_report_after_component, $this->_report_after_component_index);
    	        }
    	        else {
    	            throw __ExceptionFactory::getInstance()->createException('Component to use for error reporting not found: ' . $this->_report_after_component);
    	        }
    	    }
	    }
	    return $return_value;
	}
	
	/**
	 * Set the maximum size to allow in {@link __IUploader} components (in bytes)
	 *
	 * @param integer $max_file_size
	 */
	public function setMaxFileSize($max_file_size) {
	    if(is_numeric($max_file_size)) {
	        $this->_max_file_size = $max_file_size;
	    }
	    else {
	        throw __ExceptionFactory::getInstance()->createException('Wrong argument for maximum file size: ' . $max_file_size);
	    }
	}
	
	/**
	 * Get the maximum size to allow in {@link __IUploader} component (in bytes)
	 *
	 * @return integer
	 */
	public function getMaxFileSize() {
	    return $this->_max_file_size;
	}
	
	
	/**
	 * Set the index of the component to validate to (if applicable)
	 *
	 * @param mixed $component_index
	 */
	public function setComponentIndex($component_index) {
	    $this->_component_index = $component_index;
	}
	
	/**
	 * Get the index of the component to validate to (if applicable)
	 *
	 * @return mixed
	 */
	public function getComponentIndex() {
	    return $this->_component_index;
	}
	
	/**
	 * Set the index of the component to validate if associated component's value match with (if applicable)
	 *
	 * @param mixed $match_component_index
	 */
	public function setMatchComponentIndex($match_component_index) {
	    $this->_match_component_index = $match_component_index;
	}
	
	/**
	 * Get the index of the component to validate if associated component's value match with (if applicable)
	 *
	 * @return mixed
	 */
	public function getMatchComponentIndex() {
	    return $this->_match_component_index;
	}

	/**
	 * Set the index of the component to report errors after (if applicable)
	 *
	 * @param mixed $report_after_component_index
	 */
	public function setReportAfterComponentIndex($report_after_component_index) {
	    $this->_report_after_component_index = $report_after_component_index;
	}
	
	/**
	 * Get the index of the component to report errors after (if applicable)
	 *
	 * @return mixed
	 */
	public function getReportAfterComponentIndex() {
	    return $this->_report_after_component_index;
	}
	
	
	/**
	 * Set the minimum length allowed to the related component
	 *
	 * @param integer $min_length The minimum length
	 */
	public function setMinLength($min_length) {
		$this->_min_length = $min_length;
		$this->_valid_length = null;
	}
	
	/**
	 * Get the minimum length allowed to the related component
	 *
	 * @return integer
	 */
	public function getMinLength() {
		return $this->_min_length;
	}
	
	/**
	 * Set the maximum length allowed to the related component
	 *
	 * @param integer $max_length The maximum length
	 */
	public function setMaxLength($max_length) {
		$this->_max_length = $max_length;
		$this->_valid_length = null;
	}
	
	/**
	 * Get the maximum length allowed to the related component
	 *
	 * @return integer
	 */
	public function getMaxLength() {
		return $this->_max_length;
	}
	
	/**
	 * Set the valid length allowed to the related component
	 *
	 * @param integer $valid_length The valid length
	 */
	public function setValidLength($valid_length) {
	    $this->_valid_length = $valid_length;
	    $this->_min_length = null;
	    $this->_max_length = null;
	}
	
	/**
	 * Get the valid length allowed to the related component
	 *
	 * @return integer
	 */
	public function getValidLength() {
	    return $this->_valid_length;
	}
		
	/**
	 * Set the valid format allowed to the related component by specifying a regular expression
	 *
	 * @deprecated Use setPattern instead of
	 * 
	 * @param string $valid_format A regular expression representing the valid format
	 */
	public function setValidFormat($valid_format) {
	    $this->setPattern($valid_format);
	}
	
	/**
	 * Get the valid format allowed to the related component
	 *
	 * @deprecated Use getPattern instead of
	 * 
	 * @return string A regular expression representing the valid format
	 */
	public function getValidFormat() {
		return $this->getPattern();
	}

	/**
	 * Sets allowed extensions for input files
	 *
	 * @param string|array $allowed_extensions A string of comma-separate list of extensions, or just an array
	 */
	public function setAllowedExtensions($allowed_extensions) {
	    if(is_string($allowed_extensions)) {
	        $allowed_extensions = preg_split('/,/', $allowed_extensions);
	    }
	    else if(!is_array($allowed_extensions)) {
	        throw __ExceptionFactory::getInstance()->createException('Wrong input type for allowed extensions: ' . $allowed_extensions);
	    }
	    $this->_allowed_extensions = $allowed_extensions;
	}
	
	/**
	 * Gets an array of allowed extensions for input files
	 *
	 * @return array
	 */
	public function getAllowedExtensions() {
	    return $this->_allowed_extensions;
	}

	/**
	 * Set if the associated component must allow just numeric values
	 *
	 * @param bool $only_integer
	 */
	public function setOnlyInteger($only_integer) {
	    $this->_only_integer = (bool) $only_integer;
	}
	
	/**
	 * Get if the assocaited component must allow just numeric values
	 *
	 * @return bool
	 */
	public function getOnlyInteger() {
	    return $this->_only_integer;
	}
	
	/**
	 * Set an specific number to validate if the associated component match to
	 *
	 * @param integer $specific_number
	 */
	public function setSpecificNumber($specific_number) {
	    if(is_numeric($specific_number)) {
	        $this->_specific_number = $specific_number;
	    }
	    else {
	        throw __ExceptionFactory::getInstance()->createException('Wrong value for specific number: ' . $specific_number);
	    }
	}
	
	/**
	 * Get an specific nubmer to validate if the associated component match to
	 *
	 * @return integer
	 */
	public function getSpecificNumber() {
	    return $this->_specific_number;
	}
	
	public function setMaximumNumber($maximum_number) {
	    if(is_numeric($maximum_number)) {
            $this->_maximum_number = $maximum_number;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong value for maximum number: ' . $maximum_number);
        }	    
	}
	
	/**
	 * Get the maximum number allowed within the associated component to validate to.
	 * This property will also restrict to numeric values to the associated component.
	 *
	 * @return integer
	 */
	public function getMaximumNumber() {
	    return $this->_maximum_number;
	}
	
	/**
	 * Set the minimum number allowed within the associated component to validate to.
	 * LThis property will also restrict to numeric values to the associated component.
	 *
	 * @param integer $minimum_number
	 */
    public function setMinimumNumber($minimum_number) {
        if(is_numeric($minimum_number)) {
            $this->_minimum_number = $minimum_number;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong value for minimum number: ' . $minimum_number);
        }       
    }
    
    /**
     * Get the minimum number allowed within the associated component to validate to.
     * This property will also restrict to numeric values to the associated component.
     *
     * @return integer
     */
    public function getMinimumNumber() {
        return $this->_minimum_number;
    }	
	
	/**
	 * Set a regular expression to match for valid values
	 *
	 * @param string $pattern
	 */
	public function setPattern($pattern) {
	    $this->_pattern = $pattern;
	}
	
	/**
	 * Gets the regular expression to match for valid values
	 *
	 * @return string
	 */
	public function getPattern() {
	    return $this->_pattern;
	}

	/**
	 * Sets an identifier representing the valid format to validate as
	 * i.e. 'email' to validate emails values
	 * 
	 * Current accepted formats are:<br>
	 *  - 'email'<br>
	 *
	 * @param string $format
	 */
	public function setFormat($format) {
	    $constant_name = 'self::FORMAT_' . strtoupper($format);
	    if(defined($constant_name)) {
	        $this->_pattern = constant($constant_name);
    	    $this->_format  = $format; 
	    }
	    else {
	        throw __ExceptionFactory::getInstance()->createException('Wrong format: ' . $format);
	    }
	}
	
	/**
	 * Gets the identifier representing the valid format to validate as
	 *
	 * @param string $format
	 */
	public function getFormat() {
	    return $this->_format;
	}

	/**
	 * Set if the related component's value is mandatory
	 *
	 * @param bool $mandatory
	 */
	public function setMandatory($mandatory) {
		$this->_mandatory = $this->_toBool($mandatory);
	}
	
	/**
	 * Get if the related component's value is mandatory
	 *
	 * @return bool
	 */
	public function getMandatory() {
		return $this->_mandatory;
	}
	
	/**
	 * Set if an associated checkbox (or boolean component) must be checked to be valid.
	 *
	 * @param boolean $acceptance
	 */
	public function setAcceptance($acceptance) {
	    $this->_acceptance = $this->_toBool($acceptance);
	}
	
	/**
	 * Get if an associated checkbox (or boolean-domain component) must be checked to be valid.
	 *
	 * @return unknown
	 */
	public function getAcceptance() {
	    return $this->_acceptance;
	}
	
	/**
	 * Set if the associated component must be validated as soon as it lose the focus
	 *
	 * @param bool $validate_only_on_blur
	 */
	public function setValidateOnlyOnBlur($validate_only_on_blur) {
	    $this->_validate_only_on_blur = $this->_toBool($validate_only_on_blur);
	}
	
	/**
	 * Get if the associated component must be validated as soon as it lose the focus
	 *
	 * @return bool
	 */
	public function getValidateOnlyOnBlur() {
	    return $this->_validate_only_on_blur;
	}
	
	/**
	 * Set if the associated component must be validated just when the contained form is submitted.
	 *
	 * @param bool $validate_only_on_submit
	 */
	public function setValidateOnlyOnSubmit($validate_only_on_submit) {
	    $this->_validate_only_on_submit = $this->_toBool($validate_only_on_submit);
	}
	
	/**
	 * Get if the associated component must be validated just when the contained form is submitted.
	 *
	 * @return bool
	 */
	public function getValidateOnlyOnSubmit() {
	    return $this->_validate_only_on_submit;
	}
	
	public function setWait($wait) {
	    $this->_wait = (int) $wait;
	}
	
	public function getWait() {
	    return $this->_wait;
	}
	
	/**
	 * Perform the component to perform the validation
	 *
	 * @return bool true if the validation has success, false if any error raised
	 */
	public function validate() {
	    $component_to_validate = $this->getComponentToValidate();
        $return_value = $this->_doValidation($component_to_validate);
	    return $return_value;
	}
	
    protected function _getPrintableSize($size) {
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $retstring = '%01.2f %s';
        $lastsizestring = end($sizes);
        foreach ($sizes as $sizestring) {
                if ($size < 1024) { break; }
                if ($sizestring != $lastsizestring) { $size /= 1024; }
        }
        if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
        return sprintf($retstring, $size, $sizestring);
    } 	
	
	protected function _doValidation(__IComponent &$component) {
	    $this->_validation_result = true;
        if( $component instanceof __IValueHolder && $component->getEnabled() && $component->getVisible()) {
    	    $value = $component->getValue();
    	    $trimmed_value = trim($value);
    	    $component_to_match = $this->getComponentToMatch();

    	    //check file size (if applicable):
    	    $error_file_size = false;
    	    $max_file_size = $this->getMaxFileSize();
    	    if($max_file_size !== null ) {
    	        if($component instanceof __IUploaderComponent) {
        	        $size = $component->getSize();
        	        if($size !== null && $max_file_size < $size) {
        	            $error_file_size = true;
        	        }
    	        }
    	        else {
    	            throw __ExceptionFactory::getInstance()->createException('Can not validate file size on a non uploader component (not implementing the __IUploaderComponent): ' . get_class($component));
    	        }
    	    }
    	    
    	    if($error_file_size) {
                $component->setStatus(__IUploaderComponent::UPLOAD_STATUS_ERROR);
    	        $exceded_size = $size - $max_file_size;
                $this->setErrorMessage( 'Maximum file size (' . $this->_getPrintableSize($max_file_size) . ') exceded by ' . $this->_getPrintableSize($exceded_size) );
                $this->_validation_result = false;
    	    }
    	    //validate a matches another component value
        	else if($component_to_match instanceof __IValueHolder && trim($component_to_match->getValue()) !== $trimmed_value) {
        	    print "[" . trim($component_to_match->getValue()) . "] - [" . $trimmed_value . ']';
	            $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_FIELD_MUST_MATCH')->setParameters(array($component->getAlias(), $component_to_match->getAlias()))->getValue() );
        	    $this->_validation_result = false;
        	}
            //validate if mandatory
    	    else if(strlen($trimmed_value) == 0 && $this->getMandatory()) {
                $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_REQUIRED_FIELD')->setParameters(array($component->getAlias()))->getValue() );
        	    $this->_validation_result = false;
    	    }
    	    //validate valid length
	        else if($this->getValidLength() != null && strlen($value) != $this->getValidLength()) {
	            $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_INVALID_LENGTH')->setParameters(array($component->getAlias(), $this->getValidLength()))->getValue() );
        	    $this->_validation_result = false;
	        }
	        //validate minimum length
	        else if($this->getMinLength() != null && strlen($value) < $this->getMinLength()) {
	            $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_TOO_SHORT_VALUE')->setParameters(array($component->getAlias(), $this->getMinLength()))->getValue() );
        	    $this->_validation_result = false;
        	}
        	//validate maximum length
            else if($this->getMaxLength() != null && strlen($value) > $this->getMaxLength()) {
	            $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_TOO_LONG_VALUE')->setParameters(array($component->getAlias(), $this->getMaxLength()))->getValue() );
        	    $this->_validation_result = false;
	        }
	        //validate format
        	else if(!empty($value) && $this->getPattern() != null && !preg_match("/" . $this->getPattern() . "/i", $value)) {
                $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_INVALID_VALUE')->setParameters(array($component->getAlias()))->getValue() );
        	    $this->_validation_result = false;
        	}
        	else if($this->getAcceptance() && !$value) {
        	    $this->setErrorMessage( __ResourceManager::getInstance()->getResource('ERR_MUST_BE_ACCEPTED'));
        	    $this->_validation_result = false;
        	}
            else if(!empty($value) && $this->getOnlyInteger() && !is_numeric($value)) {
                $this->setErrorMessage('Must be integer');
                $this->_validation_result = false;
            }
            else if(!empty($value) && $this->getSpecificNumber() !== null && $value != $this->getSpecificNumber()) {
                $this->setErrorMessage('Must be ' . $this->getSpecificNumber());
                $this->_validation_result = false;
            }
            else if(!empty($value) && $this->getMinimumNumber() !== null && $value < $this->getMinimumNumber()) {
                $this->setErrorMessage('Must not be less than ' . $this->getMinimumNumber());
                $this->_validation_result = false;
            }
            else if(!empty($value) && $this->getMaximumNumber() !== null && $value > $this->getMaximumNumber()) {
                $this->setErrorMessage('Must not be more than ' . $this->getMaximumNumber());
                $this->_validation_result = false;
            }
            else if(!empty($value) && $this->getAllowedExtensions() != null && !preg_match('/\.(' . join('|', $this->getAllowedExtensions()) . ')$/i', $value)) {
                $component->setStatus(__IUploaderComponent::UPLOAD_STATUS_ERROR);
                $this->setErrorMessage('Invalid file type. Expected extensions: ' . join(', ', $this->getAllowedExtensions()));
                $this->_validation_result = false;
            }
            
            //validate against server side
            else {
                $event_handler = __EventHandlerManager::getInstance()->getEventHandler($this->_view_code);
                if($event_handler->isEventHandled('validate', $this->_component)) {
                    $ui_event = new __UIEvent('validate', array('validationRule' => $this->getId()), $component);
                    if($event_handler->handleEvent($ui_event) === false) {
                        $this->_validation_result = false;
                    }
                }
            }
        }
        if($this->_validation_result == true) {
            $this->_error_message = null;
        }
        return $this->_validation_result;
	}
	
	/**
	 * Get the last validation result (a boolean indicating if the component was validated successfully or if any error was raised)
	 *
	 * @return bool
	 */
	public function getValidationResult() {
	    return $this->_validation_result;
	}

	public function setErrorMessage($error_message) {
	    $this->_error_message = $error_message;
	}
	
	public function getErrorMessage() {
	    return $this->_error_message;
	}
	
	/**
	 * Reset the last validation result, as if the component was never validated.
	 * 
	 */
	public function resetValidation() {
	    $this->_error_message = null;
	    $this->_validation_result = null;
	}
	
}
