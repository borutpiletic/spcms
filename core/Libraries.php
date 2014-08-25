<?php
/**
 * Libraries
 * 
 * This class provides unified functionality for handling
 * client script (Javascript and CSS) libraries in SimpleCMS.
 * 
 * Library usally comes in a package containinfg multiple files. Idea behind 
 * this class is to create a package which can included with a single line of code.
 */
namespace spcms\core;

class Libraries
{
	/**
	 * Libraries register. Contains core and custom client script
	 * files.
	 * @var type 
	 */
	protected $libraries = array();
	
	public function __construct() 
	{
		// Initialize SimplCMS core libraries
		$corePath = \SimplCMS::$app->baseUrl. '/core/libraries';
		
		// Add core stylesheet
		$this->libraries['core']['css'] = array(
			'bootstrap3' => array(
				$corePath. '/css/bootstrap-3.1.1-cosmo/css/bootstrap.min.css',				
			),
			'fancytree' => $corePath. '/js/fancytree/dist/skin-win8/ui.fancytree.min.css',
		);	
		
		// Add core Javascript
		$this->libraries['core']['js'] = array(
			'jquery' => $corePath. '/js/jquery-1.10.1.min.js',
			'jquery_ui' => $corePath . '/js/jquery-ui/jquery-ui.min.js',
			'bootstrap3' => $corePath. '/css/bootstrap-3.1.1-cosmo/js/bootstrap.min.js',
			'fancytree' => array(
				$corePath. '/js/fancytree/dist/jquery.fancytree.min.js',
				$corePath. '/js/fancytree/src/jquery.fancytree.dnd.js',
			),
			'cookie' => $corePath. '/js/jquery.cookie.min.js',
		);
	}
	
	/**
	 * Add core script to current active theme
	 * @param string $name e.g.: jquery
	 * @return string|false
	 */
	public function getCoreScriptPath($name)
	{	
		if($this->isRegistered($name, 'js', true) === true)
		{
			return $this->libraries['core']['js'][$name];
		}
		return false;
	}
	
	/**
	 * Check if library was registered
	 * @param string $name
	 * @param boolean $core Set to true if you are looking for core library
	 * @return boolean
	 */
	private function isRegistered($name, $type, $core = false)
	{	
		if($core === true) 
		{
			if(array_key_exists($name, $this->libraries['core'][$type]))
				return true;
		}
		else
		{
			if(array_key_exists($name, $this->libraries['custom'][$type]))
				return true;
		}		
		
		throw new \Exception("Core client library '{$name}' not found");		
	}
	
	/**
	 * Register client script library to theme assets.
	 * @param string $name
	 * @param string $type js|css
	 * @param boolean $core 
	 */
	public function importCoreLibrary($name, $type)
	{		
		// Add stylsheet to theme assets
		if($type === 'css')
		{
			if( $this->isRegistered($name, $type, true) === true)				
				\SimplCMS::$app->theme->addStylesheetFile($this->libraries['core'][$type][$name], false);

		}
		// Add Javascript to theme assets
		if($type === 'js')
		{
			if( $this->isRegistered($name, $type, true) === true)
				\SimplCMS::$app->theme->addScriptFile($this->libraries['core'][$type][$name] , false);

		}			
	}

	/**
	 * Register custom client script library to theme assets.
	 * @param string $name
	 * @param string $type js|css
	 * @param boolean $core 
	 */
	public function importCustomLibrary($name, $type)
	{
		// Add stylsheet to theme assets
		if($type === 'css')
		{
			if( $this->isRegistered($name, $type, false) === true)				
				\SimplCMS::$app->theme->addStylesheetFile($this->libraries['custom'][$type][$name], false);

		}
		// Add Javascript to theme assets
		if($type === 'js')
		{
			if( $this->isRegistered($name, $type, false) === true)
				\SimplCMS::$app->theme->addScriptFile($this->libraries['custom'][$type][$name] , false);

		}			
	}
	
	/**
	 * Register custom client script library.
	 * @param array $files Library structure must be in following format:
	 * array( 
	 *	'library_name' => array('file1', 'file2'), // if library must consists of multiple files
	 *  'library_name2' => 'onlyonefile...'  // if library consists of only 1 file
	 * )
	 * @param string $type js|css
	 * @param boolean $relativePath Default: true. If path is relative, library must be placed in extensions/libraries/[js|css]/[library_name]/[file_name]
	 * @return void
	 */
	public function addCustomLibrary(array $files, $type, $relativePath = true)
	{
		if($relativePath === true)
		{
			foreach ($files as $library_name => $library_file)
			{
				if(is_array($library_file))
				{
					foreach ($library_file as $file)
					{
						$this->libraries['custom'][$type][$library_name][] = \SimplCMS::$app->baseUrl. "/extensions/libraries/{$type}/{$library_name}/". $file;
					}
				}
				else
				{
					$this->libraries['custom'][$type][$library_name] = \SimplCMS::$app->baseUrl. "/extensions/libraries/{$type}/{$library_name}/{$library_file}";
				}
			}
		}
		$this->libraries['custom'][$type] = $files;		
	}
}
