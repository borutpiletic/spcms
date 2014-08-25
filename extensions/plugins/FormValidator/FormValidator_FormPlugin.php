<?php
/**
 * FormValidator_FormPlugin
 * 
 * This plugin adds core component core\Form validaton functionality.
 * When custom form is created use FormValidator_FormPlugin::setValidators to pass your form validation options.
 */
namespace spcms\extensions\plugins\FormValidator;

use spcms\core\Plugin;
use spcms\core\Form;

class FormValidator_FormPlugin extends Plugin
{
	private $implementedHooks = array(
		'beforeSubmit'
	);
	
	private static $validators = array(
		// Form name
		'Login' => array(
			// Fields to validate
			'username'	=> array('required'), // Validators
			'password'	=> array('required'),			
			
		)
	);
	
	/**
	 * Current form instance.
	 * @var Form
	 */
	private $form;

	/**
	 * Implementation of hook Form|captureFormRequestData|beforeSubmit
	 * @param \spcms\core\Form $form
	 * @param type $data
	 * @throws \Exception
	 */
	public function hookBeforeSubmit(Form $form, &$data)
	{
		// Create static reference for wider usage
		$this->form = $form;
		
		// Check if this form has any validation rules
		if (array_key_exists($this->form->getName(), self::$validators) === false)
			return;
		
		// First lets check if form has all validator elements
		if ($this->containsAllValidationFields( $this->form->getName() , $data) === true)
		{
			
			// Lets validate our fields
			//$this->form->setError('INVALID_USERNAME', 'Your username seem to be invalid');
		}
		else
			throw new \Exception(__CLASS__. ': valdiation fields does not match your form fields. Fields seem to be missing in your form.');
	}
	
	private function validate()
	{
		
	}
	
	private function validateLength()
	{
		
	}
	
	/**
	 * Check if form has all validation fields.
	 */
	private function containsAllValidationFields($formName, $data)
	{
		// Find out if any validation element is missing
		$missing = sizeof(self::$validators[$formName]);		
		foreach ($data as $fieldName => $value)
		{
			if (array_key_exists($fieldName, self::$validators[$formName]))
					$missing--;
		}
		return ( $missing > 0 ) ? false : true;
	}
	
	public static function outputErrors(Form $form, $templatePath = '')
	{
		$errors = $form->getErrors();
		
		$output = '<ul>';
		foreach ($errors as $type => $message)
		{
			$output .= "<li>{$type} ---> {$message}";
		}
		$output .= '</ul>';
		
		return $output;
	}
	
	/**
	 * Set custom form validators.
	 * @param array $validatorOptions
	 */
	public static function setValidationRules(array $validatorOptions)
	{
		self::$validators = array_merge(self::$validators, $validatorOptions);
	}
}