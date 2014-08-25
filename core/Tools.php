<?php
/**
 * Tools is a special class used for misc operations such as: check application 
 * enviroment compatibility (dependencies, settings, etd...)
 */
namespace spcms\core;

class Tools 
{
	/**
	 * This method checks the proper environment compatibility for
	 * SimplCMS application.
	 */
	public static function enviromentCheck()
	{
		$environment = array();
		
		// Check for output buffering
		$environment['output_buffering'] = ini_get('output_buffering');
		
		print $environment['output_buffering']; exit;

		if($environment['output_buffering'] === '')
			$environment['output_buffering'] = 'undefined';
		
		return $environment;
	}
	
	public static function discoverPlugins()
	{
		// - code for parsing plugin.ini
		// - discover plugin hooks
		// - register plugin
	}
}
