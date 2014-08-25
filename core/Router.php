<?php
/**
 * spcms\core\Router
 * 
 * This class handles all application requests and resolves them by calling
 * appropriate module, controller and action. It also supports building custom
 * routes (localized) which are resolved before default routing takes place.
 */
namespace spcms\core;

class Router extends BaseClass
{
	/**
	 * SEO friendly custom routes. Localized routes are also supported.
	 * @var array
	 */
	protected static $customRoutes = array();
	
	/**
	 * Protected route indicator.
	 * @var boolean
	 */
	private $_protectedRoute = false;
	
	/**
	 * Application route
	 * @var array
	 */
	private $_route = array();
	
	/**
	 * Router instance.
	 * @var Router
	 */
	private static $_instance = null;
	
	private function __construct() {}
	
	/**
	 * Instance singelton.
	 * @return Router
	 */
	public static function getInstance()
	{
		if (self::$_instance === null)
			return self::$_instance = new Router;
		else
			return self::$_instance;
	}

	/**
	 * Dispatch application request.
	 * @param array $route
	 */
    public function dispatch(array $route)
    {
		// Assign route reference and resolve requested route
		$this->_route = $route;
		$this->_protectedRoute = $this->_isRouteProtected();
        $this->resolveRoute();
    }
    
    /**
     * Resolve application request created by Request::getRoute. This method
	 * maps appropriate 'module->controller->action' from request route.
     * @return void
     * @throws \Exception
     */
    private function resolveRoute()
    {		
        $themePath = \SimplCMS::app()->basePath. '/themes/'. $this->getThemeName();
		
		// @TODO What to do with default route? Access checking?
        // Default route resolves to CMS frontpage using Page module.
        if(isset($this->_route['default']))
        {
			// Create default route controller instance
            $controller = new modules\page\controllers\IndexController($this->_route);
			
			// Authorization checking for protected routes
			if ($this->_protectedRoute === true && $controller->authorizedAccess() === true)			
				return $controller->{"indexAction"}();
				
			return $controller->{"indexAction"}();
        }

		// Custom application routes handling
		// Override routes with custom routes builder
		if (!empty(self::$customRoutes)) {
			$customRoute = $this->customRoutesBuilder($this->_route);
			
			if ($customRoute !== null)
				$this->_route = $customRoute;
		}
		
		// Dynamically build controller
		$controller = $this->buildControllerClassName($this->_route);            
		$controller = new $controller($this->_route);
		
		// Serving dynamic requests
		// If controller is static, all request will are routed to one action where they should be routed dynamically.
		if($controller->isStatic() === true && !method_exists($controller, "{$this->_route['action']}Action"))
		{
			// Authorization checking for protected routes
			if ($this->_protectedRoute === true && $controller->authorizedAccess() !== true)			
				\SimplCMS::$app->response->setHttpStatus(403)->displayErrorPage();				
			else
				return $controller->{"indexAction"}();
				
		}
		// Serving static requests
        // Resolve core module request
        else if (\SimplCMS::app()->isCoreModule($this->_route['module']))
        {
            if(method_exists($controller, "{$this->_route['action']}Action"))
			{
				// Authorization checking for protected routes
				if ($this->_protectedRoute === true && $controller->authorizedAccess() !== true)			
					\SimplCMS::$app->response->setHttpStatus (403)->displayErrorPage();					
                else
					return $controller->{"{$this->_route['action']}Action"}($this->_route);
			}
            else
			{
				// In development mode throw exception, else show 404 page
				if( \SimplCMS::$app->getMode() === Application::MODE_DEVELOPMENT )
					throw new \Exception("Controller ". get_class($controller). " doesn not have action called {$this->_route['action']}");
				else
					\SimplCMS::$app->response->setHttpStatus(404)->displayErrorPage();
			}
        }
		
		// TODO: Resolve extension module requests		
		throw new \Exception('Unable to resolve your request.');
	}
	
    /**
     * Build controller class name from route
     * @param array $route
     */
    private function buildControllerClassName(array $route)
    {
        $controllerName = ucfirst($route['controller']);        
        return __NAMESPACE__. "\modules\\{$route['module']}\controllers\\{$controllerName}Controller";
    }

    /**
     * Get active theme name.
     * @return string
     */
    private function getThemeName()
    {
        if(isset(\SimplCMS::app()->getConfig()->pageTheme))
            return \SimplCMS::app()->getConfig()->pageTheme;
        return 'default';
    }
	
	/**
	 * Add custom URL routes
	 * @param string $routes Routes configuration
	 */
	public static function addCustomRoutes(array $routes) 
	{
		if (self::$_instance === null)
			self::$customRoutes = $routes;
		else
			throw new Exception('Custom routes must be added before route dispatching!');
	}
	
	/**
	 * Parse and build custom routes. Matched routes will override default system routes.
	 * @param array $route
	 * @return array|null Custom route or NULL
	 */
	private function customRoutesBuilder(array $route)
	{
		$urlParams = \SimplCMS::$app->request->getParams();
		$newRoute = null;
		
		foreach (self::$customRoutes as $customRoute => $pattern)
		{
			// Parse localized route
			if(is_array($pattern))
			{
				// TODO: localized routes parsing
				continue;
			}
			
			$newRoute = $this->parseCustomUrlRoute($urlParams, $pattern, $customRoute);
			if ($newRoute !== null)
				break;
		}
		
		return $newRoute;
	}
	
	/**
	 * Custom URL routes parsing. Matched routes will override default system routes.
	 * @param array $urlParams Request URL params.
	 * @param string $pattern URL matching pattern.
	 * 
	 * @return array|null Custom route or NULL
	 */
	private function parseCustomUrlRoute(array $urlParams, $patterns, $customRoute)
	{
		// Get chunks from URL matching patterns
		$patterns = explode('/', $patterns);
//		
//		dump($patterns, false);
//		dump($urlParams);
		
		// Perform matching only if URL parameter count is the 
		// same as URL pattern segment count.
		if (sizeof($patterns) !== sizeof($urlParams))
			return;
		
		$matched = false;
		$newRoute = array();
		
		foreach ($patterns as $i => $chunk)
		{
			print "$urlParams[$i] = $chunk <br/>";
			
			// Match URL params against pattern chunks
			if ($urlParams[$i] === $chunk)
				continue;
			
			// Match URL params against params pattern chunks
			if($patterns[$i]{0} === ':')
			{
				// Add param into request params
				$newRoute['params'][substr($chunk,1)] = $urlParams[$i];
				continue;
			}
			
			
		}
		
		// Assign new route if matched
		if ($matched === true) {
			// Build new route
			list($newRoute['route']['module'], $newRoute['route']['controller'], $newRoute['route']['action']) = explode(':', $customRoute);
			
			// Assigne matched params
			if (!empty($newRoute['params'])) {				
				foreach ($newRoute['params'] as $paramName => $paramValue)
					$_GET[$paramName] = $paramValue;
			}

			// Alter system route with new route
			return \SimplCMS::$app->request->alterSystemRoute($newRoute['route']);
		}
		
		return null;
	}
	
	/**
	 * Determine if requested route is protected.
	 * @return boolean
	 */
	private function _isRouteProtected()
	{				
		if (strpos($this->_route['action'], '_') === 0 && strpos($this->_route['action'], 'Action') !== true)
			return true;
		return false;
	}
	
	protected function handleRouteAction(Controller $controller)
	{
		// Authorization checking for protected routes actions
		//if ($this->_protectedRoute === true && $controller->authorizedAccess() === true)
			
	}
}