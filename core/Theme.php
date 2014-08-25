<?php
namespace spcms\core;

class Theme 
{
    const TPL_ENGINE_PHPTAL = 'phptal';
	
	const TPL_ENGINE_PHP = 'php';
	
	public $name = 'default';
	
    public $templateFileName;
	
	public $pageTitle;
	
	/**
	 * Absolute theme URL path
	 * @var type 
	 */
	public $baseUrl;
	
	/**
	 * Absolute theme directory path
	 * @var type 
	 */
	public $basePath;	
	
	/**
	 * @var Page
	 */
	public $page;

	/**
     * Theme assets register
     * @var type 
     */
    private $assets = array();

    /**
     * Theme sections collection
     * @var array
     */
    private $sections = array();
	
	
	/**
	 * Theme template file headers
	 * @var array
	 */
	private $headers = array();
	
	/**
	 * Template engine used
	 * @var string Default: php
	 */
	public static $templateEngine = 'php';	
    
    public function __construct() 
    {
		$route = \SimplCMS::component('request')->getRoute();
		
		// Set theme name
		if($route['module'] === 'admin')
			$this->name = 'admin';
		else
			$this->name = \SimplCMS::app()->themeName;

		$this->baseUrl = \SimplCMS::$app->baseUrl. '/themes/'. $this->name;
		$this->basePath = \SimplCMS::$app->basePath. '/themes/'. $this->name;
		
		// Set theme default template file
		$this->templateFileName = $this->_resolveTemplateFileName('index');
    } 
	
	/**
	 * Set page model which will be avalibale in theme file
	 */
	public function setPage(Page $page)
	{
		$this->page = $page;
	}
    
    /**
     * Return appended value from exsisting section
     * @param type $sectionName Theme section name.
     * @param type $id Theme section element ID.
     * @return mixed string|null
     */
    public function getSection($sectionName, $id = null)
    {
        if(isset($this->sections[$sectionName]))
        {
            if ($id !== null && isset($this->sections[$sectionName][$id]))
                return $this->sections[$sectionName][$id];
            return $this->sections[$sectionName];
        }
        return null;
    }
    
    /**
     * Append value to theme section
     * @param type $value Value to be appended to the section
     * @param type $sectionName Unique section name
     * @param type $id Unique ID when more than one element is appended to the same section
     */
    public function appendToSection($value, $sectionName, $id = null)
    {
        if ($id !== null)
            $this->sections[$sectionName][$id] = $value;
        else
            $this->sections[$sectionName] = $value;
    }
    
    /**
     * Append script file to theme assets
     * @param string|array $filePath Path to the script file or array of paths.
     * @param string $relativePath If true (default), it will look for a file in the theme directory
     */
    public function addScriptFile($filePath, $relativePath = true)
    {
		// Add multiple files
		if(is_array($filePath))
		{
			if($relativePath === true) 
			{
				foreach ($filePath as $path)
				{
					$this->assets['scripts'][] = \SimplCMS::app()->baseUrl. "/themes/{$this->name}/{$path}";
				}
			}
			else
			{
				foreach ($filePath as $path)
				{
					$this->assets['scripts'][] = $path;    			
				}
			}
		} 
		else
		{
			if($relativePath === true)
				$this->assets['scripts'][] = \SimplCMS::app()->baseUrl. "/themes/{$this->name}/{$filePath}";
			else
				$this->assets['scripts'][] = $filePath;    
		}		
    }
    
    /**
     * Append stylesheet file to theme assets
     * @param string|array $filePath Path to the stylesheet file or array of paths.
     * @param string $relativePath If true (default), it will look for a file in the theme directory
     */
    public function addStylesheetFile($filePath, $relativePath = true)
    {
		// Add multiple files
		if(is_array($filePath))
		{
			if($relativePath === true) 
				foreach ($filePath as $path)
					$this->assets['stylesheets'][] = \SimplCMS::app()->baseUrl. "/themes/{$this->name}/{$path}";
			else
				foreach ($filePath as $path)				
					$this->assets['stylesheets'][] = $path;
		} 
		else
		{
			if($relativePath === true)
				$this->assets['stylesheets'][] = \SimplCMS::app()->baseUrl. "/themes/{$this->name}/{$filePath}";
			else
				$this->assets['stylesheets'][] = $filePath;    
		}
    }
    
    /**
     * Render theme script files or return collection
     * @param boolean $render Render script file collection
     * @return array|string
     */
    public function getScriptFiles($render = true)
    {
        if(!isset($this->assets['scripts']))
            return null;
        
        // Script file links ready to be rendered 
        if($render)
        {
            $output = null;
            foreach ($this->assets['scripts'] as $scriptPath)
                $output .= "<script src='{$scriptPath}'></script>\n";
			
            print $output;
        }
        else
            return $this->assets['scripts'];
    }
    
    /**
     * Render theme external stylesheet files or return collection
     * @param boolean $render Render stylesheet file collection
     * @return string|array
     */
    public function getStylesheetFiles($render = true)
    {
        if(!isset($this->assets['stylesheets']))
            return null;
        
        // Script file links ready to be rendered 
        if($render)
        {
            $output = null;
            foreach ($this->assets['stylesheets'] as $stylesheetPath)
            {
                $output .= "<link rel='stylesheet' type='text/css' href='{$stylesheetPath}'>\n";
            }
            print $output;
        }
        else
            return $this->assets['stylesheets'];
    }
	
	/**
	 * Template engine used by custom theme. Default is PHPTAL.
	 * @return string
	 */
	public static function getTemplateEngineName()
	{
		// TODO: Force admin module to use PHPTAL ???		
		return \SimplCMS::$app->getConfig()->templateEngine;
	}
	
	/**
	 * Set theme template file name
	 * @param string $templateFileName Template file name without file extension
	 */
	public function setTemplate($templateName)
	{
		$this->templateFileName = $this->_resolveTemplateFilename($templateName);
	}
	
	/**
	 * Resolve theme template filename based on the themeEngine
	 * @return string
	 */
	private function _resolveTemplateFileName($templateName)
	{
		$templateFileName = '';
		
		switch(self::getTemplateEngineName())
		{
			case self::TPL_ENGINE_PHPTAL:				
				$templateFileName = "{$templateName}.xhtml";
			break;
			case self::TPL_ENGINE_PHP:				
				$templateFileName = "{$templateName}.php";
			break;		
		}
		
		return $templateFileName;
	}
	
	/**
	 * Render theme template file
	 * @param string $content View content
	 * @throws Exception
	 */
	public function render($content = null)
	{
		$this->_executeTemplateFile($content);
	}
	
	public function setModuleClientScript($scriptName, $moduleName = null)
	{
		$route = \SimplCMS::$app->request->getRoute();
	}

	private function _executeTemplateFile($content = null)
	{
		// Include main template file
		$templateFile = \SimplCMS::$app->basePath. "/themes/{$this->name}/{$this->templateFileName}";
		
        if(file_exists($templateFile) === false)
			throw new \Exception("Theme template file '{$this->templateFileName}' not found using theme: '{$this->name}'");		
		
		// 
		// @TODO
		// Here is the place, where in future some template engine could be implemented.
		// 
		require_once $templateFile;
	}
	
	/**
	 * Include template file. Main template can consist of many template files: header, footer, etc...
	 * This method is a wrapper for conventional template file include.
	 * @param string $templateFileName File extension .php must me omited.
	 * @throws \Exception
	 */
	public function includeTemplateFile($templateFileName)
	{
		$tplFile = "{$this->basePath}/{$templateFileName}.php";
		
		if(file_exists($tplFile) === false)
			throw new \Exception("Theme: no template file named '{$templateFileName}' found in theme '{$this->name}'");
		
		require_once $tplFile;
	}
	
	/**
	 * Get theme HTML HEAD
	 * @return string
	 * @todo Create set HTML header method for adding cutsom headers!!!
	 */
	public function getHtmlHeaders()
	{
		$config = \SimplCMS::$app->getConfig();
		
		// Start building HTML headers
		$headers = '';
		$headers .= "<meta charset='{$config->charset}' />\n";
		$headers .= "\t\t<base href='{$config->baseUrl}' />\n";
		$headers .= "\t\t<meta name='generator' content='SimplCMS 0.2' />\n";
		
		// Add app client script config
		$headers .= $this->setAppScriptConfig($config);
		
		return $headers;
	}
	
	/**
	 * Application config accessible via client scripts (Javascript).
	 * @param stdClass $config SimplCMS app config object
	 * @return string Configuration added to HTML HEAD
	 */
	private function setAppScriptConfig(\stdClass $config)
	{
		// @todo: Make it pluggable
		
		// Add app script config
		$csConfig  = '<script type="text/javascript">';
		$csConfig .= 'var SimplCMS = {};';
		$csConfig .= "SimplCMS.config = { "
				. "baseUrl: '$config->baseUrl', "
				. "charset: '{$config->charset}', "
				. "themeName: '{$config->themeName}', "
				. "themeUrl: '{$config->themeUrl}/{$config->themeName}' };";
		$csConfig .= '</script>';
		
		return $csConfig;
	}
}