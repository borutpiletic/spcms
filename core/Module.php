<?php
namespace spcms\core;

class Module
{
	const TYPE_CORE = 0;
	
	const TYPE_CUSTOM = 1;
	
	public $controller;
	
	public $model;
	
	public $helper;
	
	private $moduleName;
	
	private $moduleNs;

	public function __construct($moduleName, $type = self::TYPE_CORE) 
	{
		$this->moduleName = $moduleName;
		
		if($type === self::TYPE_CUSTOM)
			$this->moduleNs = '\spcms\extensions\modules\\';
		
		if($type === self::TYPE_CORE)
			$this->moduleNs = '\spcms\core\modules\\';
	}
	
	/**
	 * Return module controller
	 * @param string $controllerName Controller name without 'Controller' suffix.
	 * @return \spcms\core\controllerName
	 */
	public function getController($controllerName = 'index')
	{
		$controllerName = "{$this->moduleNs}{$this->moduleName}\controllers\\". ucfirst($controllerName). 'Controller';		
		return new $controllerName();
	}
	
	/**
	 * Return module helper
	 * @param string $helperName Helper name without 'Helper' suffix.
	 * @return \spcms\core\helperName
	 */
	public function getHelper($helperName)
	{
		
		$helperName = "{$this->moduleNs}{$this->moduleName}\helpers\\".  ucfirst($helperName). 'Helper';
		return new $helperName();
	}	
}
