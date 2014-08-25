<?php
namespace spcms\core\modules\admin\helpers;

use spcms\core\Form;
use spcms\extensions\plugins\FormValidator\FormValidator_FormPlugin;

class FormsFactoryHelper
{
	/**
	 * Get login form
	 * @return \spcms\core\Form
	 */
	public function formLogin()
	{
		// Set form validation
		FormValidator_FormPlugin::setValidationRules(array(
			'Register' => array('123')
		));		
		// Init login form
		return new Form('Login', Form::METHOD_POST);		
	}
	
	public function formRegister()
	{
		// Set form validation
		FormValidator_FormPlugin::setValidationRules(array(
			'Register' => array(
				'email' => array(),
				'password' => array()
			)
		));		
		return new Form('Register', Form::METHOD_POST);
	}
}