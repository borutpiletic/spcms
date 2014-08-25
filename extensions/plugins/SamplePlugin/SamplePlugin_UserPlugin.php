<?php
namespace spcms\extensions\plugins\SamplePlugin;

use spcms\core\Plugin;

class SamplePlugin_UserPlugin extends Plugin 
{
	/**
	 * List of hooks that this plugin is implementing
	 * @var array
	 */
	protected $implementedHooks = array();

	protected function init()
	{
		// Initialize plugin settings
		
		// Tell which hooks will this plugin implement.
	}

	public function hookOnLoad($object, $args)
	{
		print 'sample "onLoadHook" called';
	}
	
	public function implementedHooks()
	{
		
	}
}

