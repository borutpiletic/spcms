<?php
/**
 * SimplCMS main application class. This class should never be used alone, but rather 
 * trough SimplCMS factory class.
 */

namespace spcms\core;

class Application
{
    const MODE_DEVELOPMENT = 'DEVELOPMENT';
    
    const MODE_PRODUCTION = 'PRODUCTION';
    
    private static $config;
    
    private static $mode;
    
    private static $route;
    
    public $baseUrl;
    
    public $basePath;
	
    public $themeName;
	
	/**
	 * Session handler
	 * @var Session
	 */
	public $session = null;
	
	/**
	 * Application language
	 * @var string
	 */
	public $language = 'en';

	/**
	 * Application locale (default: en_US)
	 * @var Locale
	 */
	public $locale;
	
	/**
	 * @var Request
	 */
	public $request;
	
	/**
	 * @var Response
	 */
	public $response;
	
	/**
	 * @var Libraries
	 */
	public $libraries;
	
	/**
	 * @var Theme
	 */
	public $theme;
	
	/**
	 * @var User
	 */
	public $user;

	/**
	 * CMS database connection
	 * @var Database
	 */
	protected $db;

	/**
     * Application modules register
     * @var type array
     */
    private $modulesRegister = array(
        'core' => array('page', 'mailer', 'menu', 'admin', 'gallery', 'user', 'cart')
    );
    
    /**
     * Return SimplCMS core modules
     * @return array
     */
    public function getCoreModules()
    {
        return $this->modulesRegister['core'];
    }
    
    /**
     * Check if request is for a core module
     * @param string $moduleName
     * @return boolean
     */
    public function isCoreModule($moduleName = null)
    {
		if($moduleName === null)
			$moduleName = \SimplCMS::$app->request->getModuleName();
		
        return (in_array($moduleName, $this->modulesRegister['core'])) ? true : false;
    }
	
	/**
	 * Return CMS core module component
	 * @param string $name
	 * @return \spcms\core\Module
	 */
	public function getCoreModule($name)
	{
		return new \spcms\core\Module($name);
	}
	
	/**
	 * Return custom (ext.) module component
	 * @param string $name
	 * @return \spcms\core\Module
	 */
	public function getModule($name)
	{
		return new \spcms\core\Module($name, Module::TYPE_CUSTOM);
	}

	/**
     * Set main application params
     * @param string $filename String (filepath) to INI file
     * @param int $mode Application mode (default: development)
     */
    public function __construct($filename)
    { 		
        // Parse app configuration
        self::parseConfig($filename);
    }
    
    public function run()
    {
		// Set app mode
		self::$mode = self::$config['mode'];		
		
		// Application mode specific settings
        switch(self::$mode)
        {
            // Put application in strict development mode
            case self::MODE_DEVELOPMENT:
				
				ini_set('display_startup_errors', 1);
				ini_set('display_errors', 1);				
                error_reporting(E_ALL | E_STRICT);
				
            break;
        
            case self::MODE_PRODUCTION:
				
            break;
        }
		
		// Set commonly used configurations
        $this->baseUrl = self::$config['baseUrl'];
        $this->basePath = self::$config['basePath'];
        $this->themeName = self::$config['themeName'];
		
        // Init application
        $this->init();
    }

    /**
     * Parse configuration file
     * @param array $config
     */
    private static function parseConfig($filename)
    {
        self::$config = parse_ini_file($filename, true);
        self::$config = array_merge(self::$config[ self::$config['APPLICATION']['mode'] ], self::$config['APPLICATION']);
    }
    
    /**
     * Get SimplCMS application configuration
     * @return object
     */
    public static function getConfig()
    {
        return (object) self::$config;
    }
    
    /**
     * Run application init tasks before it can be used
     */
    protected function init()
    {		
		// Load app resource dependencies
		$this->loadAppResources();		
		
		// Begin runtime logging
		$this->startRuntimeLogger();
		
		// Run application bootstrap
		if (isset(self::$config['bootstrap']) && self::$config['bootstrap'] == 1)
			$this->bootstrap();
		
		// Init session handler
		$this->startAppSession();
		
		// Init application locale
		$this->locale = new Locale;
		
		// Init application user
		$this->user = new User;
		
		// Create application request comp
		$this->request = \SimplCMS::component('request');
		
		// Create applicatoin response comp
		$this->response = \SimplCMS::component('response');
		
		// Load client libraries handler
		$this->libraries = new Libraries();		
		
		// Create application theme layer
		$this->theme = \SimplCMS::component('theme');
		
		// All needed components are loaded and application bootstrap is ready, 
		// lets dispatch our request.
        Router::getInstance()->dispatch( $this->request->getRoute() );
    }

	/**
     * Build URL according to app internals.
     * @param string $path
     * @param array $query
     * @param boolean $absolute
     * @param string $hashtag Hashname wihtout the leading #
     * @return string
     */
    public function buildUrl($path, $query = null, $absolute = true, $hashtag = null, $encoding = '')
    {        
        $url = '';
        
        // Build absolute URL
        if($absolute === true)
            $url = \SimplCMS::app()->baseUrl;
        
        // URL rewriting enabled
        if(\SimplCMS::app()->getConfig()->urlRewrite)
        {
            // Append urlRewriteSuffix to URL path
            if($urlSuffix = \SimplCMS::app()->getConfig()->urlRewriteSuffix)
                $path .= ".{$urlSuffix}";            
            
            // Build clean URL query
            if($query !== null)
            {
                $query = str_replace('=', '/', http_build_query($query, null, '/'));
                $url .= "/{$path}/{$query}";
            }
            else
                $url .= "/{$path}";
        }
        else
        {
            // Build standard GET query
            if($query !== null)
            {
                $query = http_build_query($query);
                $url .= "/?r={$path}&{$query}";
            }
            else
                $url .= "/?r={$path}";
        }
        
        // Append hastag
        if($hashtag !== null)
            $url .= "#{$hashtag}";
            
        return $url;
    }
    
	/**
	 * Load application resources used by SimplCMS
	 * @return void
	 */
    private function loadAppResources()
	{	
		// Load CMS helper functions
		require_once 'helper.functions.php';
	}
	
	/**
	 * Register HTTP session handler
	 */
	private function startAppSession()
	{
		$this->session = new Session;	
	}
	
	/**
	 * Start runtime logger handler
	 */
	protected function startRuntimeLogger()
	{		
		// Start runtime logger
		new RuntimeLog();		
	}
	
	/**
	 * Get CMS database connection
	 * @return \PDO
	 */
	public function getDbConnection()
	{
		// Create new DB connection
		if (!isset($this->db))
			$this->db = new Database;
		
		return $this->db->getConnection();
	}
	
	/**
	 * Application mode: DEVELOPMENT or PRODUCTION
	 * @return string
	 */
	public function getMode()
	{
		return self::$mode;
	}
	
	/**
	 * Run custom bootstrap class, if avaliable.
	 */
	private function bootstrap()
	{
		if(file_exists("{$this->basePath}/extensions/Bootstrap.php"))
			\spcms\extensions\Bootstrap::load();
	}
	
	/**
	 * Return path based on type
	 * @param string $pathType
	 */
	public function getPathByType($pathType)
	{
		
	}
}

