<?php
/**
 * spcms\core\Controller
 * 
 * Main application controller with IController interface.
 */
namespace spcms\core;

interface IController 
{
    public function indexAction();
}

abstract class Controller implements IController
{	
	/**
     * Theme object
     * @var Theme
     */
    public $theme;
	
	/**
	 * @var array
	 */
    public $route;
	
	/**
	 * View object
	 * @var View
	 */
	public $view;
	
	/**
	 * Default action
	 * @var string
	 */
	public $defaultAction = 'index';

	/**
	 * Page object
	 * @var Page
	 */
    public $page;
	
	/**
	 * Route module name
	 * @var string
	 */
    public $moduleName;
	
	/**
	 * Route action name
	 * @var string
	 */
    public $actionName;
	
	/**
	 * Route controller name
	 * @var string
	 */
    public $controllerName;
    
    /**
     * Act as static controller. If controller is static, all requests are
     * served trough indexAction.
     * @var boolean
     */
    protected $isStatic = false;

    public function __construct($route = null) 
    {   
        // Set basic controller vars
		if($route !== null)
		{
			$this->route = $route;
			$this->moduleName = $route['module'];
			$this->actionName = $route['action'];
			$this->controllerName = $route['controller'];
		}
        
		// Handle all controller init tasks (create view, theme, etc..)
        $this->init();
		
		// Controller on load action
        $this->onLoad();
    }
    
    /**
     * Default controller action
     */
    public function indexAction() 
    {
        print 'Hello from default SimplCMS default controller indexAction. This action should be overriden.';
    }
    
    /**
     * Render controllers view file
     * @param string $viewName
     * @param array $vars Variables passed to the view file
     */
    protected function render($viewName, array $vars = array())
    {	
		// Register before render event
		$this->beforeRender();
		
		$this->view->setName($viewName);
		$this->view->setVars($vars);
        
        // Capture view file contents
		$content = $this->view->getContents();
		
		// Render theme template file with injected view contents
		$this->theme->render( $content );
        
		// Register after render event
        $this->afterRender();
    }
    
    private function init()
    {	
		// Create view reference
		$this->view = new View($this);
		// Crate theme reference
		$this->theme = \SimplCMS::$app->theme;
    }

	/**
     * Check if controller is static
     * @return boolean
     */
    public function isStatic()
    {
        return $this->isStatic;
    }
	
	/**
	 * Stop the controller action with HTTP OK 200 response and kill the script.
	 * @param int $httpStatusCode
	 * @param bool $exit Terminate script
	 */
	protected function exitAction($httpStatusCode = 200, $terminate = false)
	{
		if(is_int($httpStatusCode))
			\SimplCMS::$app->request->setStatusCode($httpStatusCode);
		
		if($terminate === true)
			exit;
		
		return true;
	}

	/**
     * Standard error page.
	 * 
     */
    public function errorPageAction($httpStatusCode)
    {
        
		
		
        exit;
    }

	/**
     * Called right before page view rendering tooks place
     */
    public function beforeRender()
	{
		// Enable page gzip compression
		//OutputBuffer::enableGzipCompression();
	}
    
    /**
     * Called right before page view rendering tooks place
     */
    public function afterRender()
    {
        // Clean view output buffer
        OutputBuffer::end(true);
    }
    
    /**
     * Called right before controller starts loading
     */
    public function onLoad(){}
	
	/**
	 * Return custom module helper class
	 * @param string $helperName Helper class name, without Helper suffx
	 */
	public function getHelper($helperName)
	{
		if (\SimplCMS::$app->isCoreModule() === true)
			return \SimplCMS::$app->getCoreModule($this->route['module'])->getHelper($helperName);
		else
			return \SimplCMS::$app->getModule($this->route['module'])->getHelper($helperName);
	}
	
	/**
	 * TODO: render error page by controller
	 * Render error page.
	 * @param int $code HTTP error code
	 * Defaults to theme: theme-folder-root/error.php.
	 */
	protected function renderErrorPage($code)
	{
		\spcms\core\Response::setHttpStatus($code);
	}
	
	/**
	 * This method is controller alias for: Request::redirect()
	 * @param string $path
	 * @param string $relative
	 */
	public function redirect($path, $relative = true)
	{
		\SimplCMS::$app->request->redirect($path, $relative);
	}
	
	/**
	 * Checking if user is atuhorized to access protected route/action.
	 * Return boolean true or display 403 Forbidden page.
	 * @return mixed
	 */
	public function authorizedAccess() 
	{
		return true;
	}
}
