<?php
/**
 * Plugin
 * 
 * Plugin is SimpleCMS implementatino of event system built using Observer design pattern.
 * 
 * This class is used for creating custom CMS plugins. All events
 * must begin with prefix "hook" to which component event they want to hook.
 * 
 * Example:
 * User (subject) notifies observers (plugins) about "onLogin" event.
 * Your plugin (observer) should respond to this event by implementing public method
 * called "hookOnLogin" which will get user object passed to it and optional additional arguments array.
 * Arguments are defined by individual extendable component and may vary.
 * 
 */
namespace spcms\core;

abstract class Plugin
{	
	/**
	 * List of hooks this plugin implements, e.g.: array('hook1', 'hook2')
	 * @var array
	 */
	private static $implementedHooks;

	public final function __construct() 
	{
		// Execute plugin
		$this->init();
	}
	
	/**
	 * Initialize plugin settings.
	 * This method should be overriden. 
	 * @return void
	 */
	protected function init(){}
	
	/**
	 * Get hooks implemented by this plugin.
	 * @return array Array of implemented hooks.
	 */
	public static function getImplementedHooks()
	{
		return static::$implementedHooks;
	}
}