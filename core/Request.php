<?php
/**
 * Request
 * This class is SimplCMS application component which handles all request 
 * made to the SimplCMS application.
 * 
 */
namespace spcms\core;

class Request
{
    /**
     * Route information
     * @var type array
     */
    private $route;
    
    public function __construct() 
    {
        $this->buildRoute();
    }

    /**
     * Return request GET query as string
     * @param type $method
     * @return string Route query
     */
    public function getQuery()
    {
        if(isset($_GET['r']))
            return $_GET['r'];
        else
            return null;
    }
    
    /**
     * Return GET param
     * @param string $paramName
     * @return mixed
     */
    public function getParam($paramName)
    {
        if(isset($_GET[$paramName]))
            return $_GET[$paramName];
        return null;
    }
    
    /**
     * Return GET param
     * @param string $paramName
     * @return mixed | null on empty
     */
    public function getPostParam($paramName)
    {
        if(isset($_POST[$paramName]))
            return $_POST[$paramName];
        return null;
    }    

    public function getParams()
    {
        if(isset(\SimplCMS::app()->getConfig()->urlRewrite) && \SimplCMS::app()->getConfig()->urlRewrite)
            return explode('/', $this->getQuery());
    }
	
	/**
	 * Extract JSON data from request body
	 * @param bool $asArray Return result as associative array or raw json object.
	 * @return mixed
	 */
	public function getJson($asArray = true)
	{
		$rawJson = file_get_contents('php://input');
		
		if($asArray === true)
			return json_decode($rawJson, true);
		
		return json_decode($rawJson);
	}

	/**
     * Check if request method is GET
     * @return boolean
     */
    public function isGet()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'GET') ? true : false;
    }
    
    /**
     * Check if request method is POST
     * @return boolean
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
    }

    /**
     * Build atomic route array from route query ($_GET['r']).
     * @return array
     */
    private function buildRoute()
    {
        $query = $this->getQuery();		
        $segmentCount = substr_count($query, '/');
        
        // Explode query into route segments
        $query = explode('/', $query);
        
        if($segmentCount > 1)
        {
            $this->route['module'] = $query[0];
            $this->route['controller'] = $query[1];
            $this->route['action'] = $query[2];
        }
        else if($segmentCount === 1)
        {
            $this->route['module'] = $query[0];
            $this->route['controller'] = 'index';
            $this->route['action'] = $query[1];
        }
        else 
        {            
            // Default route gets called using core Page module
            $this->route['module'] = 'page';
            $this->route['controller'] = 'index';
            $this->route['action'] = (empty($this->route['action']) && !empty($query[0])) ? $query[0] : 'index';
            
            // Set indicator for default CMS frontpage route
            if(empty($this->route['action']))
                $this->route['default'] = true;
        }
        
        return $this->route;
    }
    
    /**
     * Return request URL as atomic route array
     * @return array
     */
    public function getRoute()
    {
        if($this->route === null)
            return $this->buildRoute();
        
        return $this->route;
    }
	
	public function getModuleName()
	{
		return $this->route['module'];
	}
    
	public function getControllerName()
	{
		return $this->route['controller'];
	}
	
	public function getActionName()
	{
		return $this->route['action'];
	}
	
	public function redirect($path, $relative = true)
	{
		// @TODO: status codes
		// Redirect made inside application
		// $this->setStatusCode(301)
		// 
		// ;
		
		if($relative === true)
			header('Location: '. \SimplCMS::$app->baseUrl. "/{$path}");
		else
			header("Location: {$path}");
	}
	
	/**
	 * Set HTTP header status code
	 */
	public function setStatusCode($code)
	{
		if(array_key_exists($code, $this->httpStatusCodes))
			header('HTTP/1.0 '. $this->httpStatusCodes[$code]);
		else 
			throw new \Exception(__CLASS__. ": invalid HTTP status code ({$code})");
	}
	
	/**
	 * Alter system route. Used by Router::parseCustomUrlRoute(). Its purpose is to 
	 * override route created by routing system.
	 * NOTE: Alter this if your really know what you are doing.
	 * 
	 * @param array $newRoute New route that override the one provided by routing system.
	 * Must be in a form of array('module' => 'moduleName', 'controller' => 'controllerName', 'action' => 'actionName')
	 */
	public function alterSystemRoute(array $newRoute)
	{
		if (!empty($newRoute) && sizeof($newRoute) === 3)
			return $this->route = $newRoute;
		
		throw new \Exception('Alter system route failed: provided "newRoute" is empty or is missing chunk.');
	}

		/**
	 * Get HTTP REQUEST_URI header
	 * @param boolean $absolute
	 */
	public function getRequestUri($absolute = true)
	{
		if($absolute === true)
			return $_SERVER['REQUEST_URI'];
		
		return $_SERVER['REQUEST_URI'];
	}
	
	
}