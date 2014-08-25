<?php
/**
 * SimplCMS application static wrapper class. All application components
 * should be called trough this component factory. Note that this class is global therefore
 * it is not namespaced.
 */
class SimplCMS
{
    /**
     * CMS core components register
     * @var array 
     */
    protected static $components = array(
        'app'           => 'Application',
        'page'          => 'Page',
        'log'           => 'RuntimeLog',
        'router'        => 'Router',
        'request'       => 'Request',
        'theme'         => 'Theme',
		'locale'		=> 'Locale',
		'db'			=> 'Database',
		'response'		=> 'Response',
		'session'		=> 'Session'
    );
    
    /**
     * Application
     * @var \spcms\core\Application
     */
    public static $app;
	
	/**
	 * Plugins registered by this application
	 * @var array 
	 */
	private static $pluginRegistry = array();
    
    /**
     * Loaded components collection
     * @var array
     */
    public static $loadedComponents = array();

    public static function app()
    {        
        // CMS app instance
        if(!isset(self::$app))      
            self::$app = new spcms\core\Application(__DIR__. '/../config.ini');

        return self::$app;
    }
    
    /**
     * CMS components loader
     * @param string $componentName Core component name (case-sensitive)
     * @param type $arguments
     * @return object
     * @throws Exception
     */
    public static function component($componentName, $arguments = null)
    { 
        // If arguments array is empty - ignore it
        $arguments = (!empty($arguments)) ? $arguments : null;
        
        // Check if core component is registered
        if(array_key_exists($componentName, self::$components))
        {
            // Check if core component might be loaded
            if(!in_array($componentName, self::$loadedComponents))
            {
                $componentName = '\\spcms\\core\\'. self::$components[$componentName];
                self::$loadedComponents[$componentName] = new $componentName($arguments);
            }
        }
        else
            trigger_error ("Call to undefined method ". __CLASS__. '::'. $componentName, E_USER_ERROR);
        
        if(self::$loadedComponents[$componentName] === null)
            throw new Exception($componentName. ' core component does not seem to exsist.');
        else
            return self::$loadedComponents[$componentName];        
    }
    
    /**
     * Register new CMS component
     * @param string $componentName Component class name
     * @param string $alias Component alias used for loading component
     */
    public static function registerComponent($componentName, $alias)
    {
        if(!isset(self::$components[$alias]))
            self::$components[$alias] = $componentName;
    }
    
    /**
     * Get array of registered components
     * @return array
     */
    public static function getRegisteredComponents()
    {
        return self::$components;
    }
    
    /**
     * List of loaded components that have been registered and used at least once
     * @return array
     */
    public static function getLoadedComponents()
    {
        return self::$loadedComponents;
    }
	
	public static function db()
	{
		return self::$app->getDbConnection();
	}
	
	/**
	 * Get registered plugins
	 * @param boolean $includeDisabled Get complete register or only enabled plugins
	 * @return array
	 * @throws Exception
	 */
	public static function getRegisteredPlugins($includeDisabled = false)
	{
		if (!empty(self::$pluginRegistry))
			return self::$pluginRegistry;		
		
		if ($includeDisabled === true)
			$sth = self::db()->prepare('SELECT * FROM sys_plugins');		
		else
			$sth = self::db()->prepare('SELECT * FROM sys_plugins WHERE active = 1');		
			
		if ($sth->execute() === true)
			return self::$pluginRegistry = $sth->fetchAll(PDO::FETCH_ASSOC);
		else 
			throw new Exception ('Error getting plugin registry');
	}
}
