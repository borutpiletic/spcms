<?php
/**
 * @class spcms\core\BaseClass
 * 
 * This is a SPCMS core application base class. All components exposing plugin hook support (events)
 * should extend this class in order to gain additional functionality.
 */
namespace spcms\core;

abstract class BaseClass
{
	/**
	 * List of plugins that are attached to this component.
	 * @var array
	 */
	private $_registeredPlugins = array();
	
	/**
	 * Notify all plugins about executed event in which they can hook to.
	 * @param string $hookName
	 * @param array $args Array of arguments passed to hook[PluginHookName]. Contains special
	 * element called '_info_' where component information is passed to plugin.
	 * - Additional arguments: notify() can take more than 1 argument.
	 */
	public final function notify($hookName, array &$args) 
	{
		// Register plugins only once
		if (empty($this->_registeredPlugins))
			$this->registerComponentPlugins();
		
		foreach ($this->_registeredPlugins as $plugin => $info)
		{
			// Check for implemented hook
			if(in_array($hookName, $info['hooks']))
			{
				//@TODO: switch based on the plugin type, maybe someday
				
				// Create plugin object
				$object = "\\spcms\\extensions\\plugins\\{$plugin}\\{$info['class']}";
				$object = new $object();
				
				// Populate hook with component info
				$args['_hookInfo_'] = array(
					'hookName'	=> $hookName					
				);
				
				$object->{'hook'.$hookName}($this, $args);
			}
		}	
	}
	
	/**
	 * Register and attached all discovered plugins.
	 * @return void
	 */
	private function registerComponentPlugins()
	{		
		// Attach all discovered plugins that might be extending
		// this component functionality.
		
		// Get extended class name without namespace
		$extendedClassName = get_class($this);
		$extendedClassName = explode('\\', $extendedClassName);
		$extendedClassName = end($extendedClassName);
		
		// @TODO: plugin discovery handler <---> Database
		// 
		// Load all plugins from DB register and check if they
		// belong to this component.
		$plugins = \SimplCMS::getRegisteredPlugins();
		
		foreach ($plugins as $plugin => $info)
		{
			//dump($info, false);
			
			// Find component corresponding plugin class
			if(strstr($info['class'] , "_{$extendedClassName}Plugin"))
			{
				// Register plugin hooks
				$this->_registeredPlugins[ $info['name'] ] = array(
					'class' => $info['class'],
					'name'	=> $info['name'],
					'hooks' => explode(':', $info['hooks'])
				);			
			}
		}
	}
	
	public function detach(Plugin $observer)
	{
		
	}
	
	protected function init(){}
}

