<?php
namespace spcms\extensions\plugins\LoginHandler;

use spcms\core\Model;
use spcms\core\Plugin;

class LoginHandler_FormPlugin extends Plugin 
{
	/**
	 * List of hooks that this plugin is implementing.
	 * @var array
	 */
	private static $implementedHooks = array(
		'processData',
		'login',
		//'beforeSubmit'
	);

	protected function init()
	{
		
	}
	
	public function hookBeforeSubmit($object, &$args)
	{
		//dump($object,false);
		print 123;
	}

	/**
	 * Main plugin hook.
	 * @param type $object
	 * @param null $data
	 */
	public function hookProcessData($object, &$args) 
	{
		
		
		
		
		
		// do some cool stuff
		print '<br><br>Plugin hook called: ' . __FUNCTION__. '<br><br>';
	}
}

