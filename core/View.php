<?php
namespace spcms\core;

class View
{	
	/**
	 * Current action controller
	 * @var Controller
	 */
	protected $controller;
	
	/**
	 * View file name
	 * @var string 
	 */
	protected $name;

	/**
	 * Flash messages
	 * @var type 
	 */
	protected $messages = array();

	/**
	 * Variables assigned to the view file
	 * @var array
	 */
	protected $vars = array();
	
	protected $output = false;
	
	protected $clearCache = false;

	public function __construct(Controller $controller) 
	{
		$this->controller = $controller;
		$this->name = $controller->actionName;
	}
	
	/**
	 * Render view file
	 * @param boolean $output If true, view will be printed out. Else only returned as a string.
	 */
	public function getContents($output = false)
	{
		if($output === true)
			$this->output = true;
		
		// @TODO
		// Here is the place where in future template engine could be implemented.
		// For now, PHP will do.
		
		return $this->phpTemplateExecute();		
	}
	
    /**
     * Capture view file into output buffer
     * @param string $name 
     * @return string View output
     */
    private function captureViewOutput($name, array $vars = null)
    {
		$this->executeTemplateEngine( Theme::getTemplateEngineName() );
    }
	
	/**
	 * Render view using PHP template engine
	 * @return string
	 * @throws \Exception
	 */
	private function phpTemplateExecute()
	{
       $viewOutput = null;
        
        // Assign variables to the view file
        if(isset($this->vars) && is_array($this->vars))
            extract($this->vars);
        
        // Get core module view file
        if(\SimplCMS::app()->isCoreModule())
        {
			OutputBuffer::start();
            
                $viewFile = \SimplCMS::app()->basePath. "/core/modules/{$this->controller->moduleName}/views/{$this->name}.php";
				
				if(file_exists($viewFile) === false)
					throw new \Exception('View file "'. $this->name. '" not found by controller: '. $this->controller->controllerName);
				
				require_once $viewFile;

				$viewOutput = OutputBuffer::getContents();
            
            OutputBuffer::end();
        }
        // TODO: Get custom module view file
		
		return $viewOutput;
		
        
//		if($this->output === true)
//			print $viewOutput;		
//		else
//			return $viewOutput;
	}
	
	/**
	 * Render view using PHPTAL template
	 */
	private function phptalTemplateExecute()
	{
		require_once \SimplCMS::$app->basePath. '/core/libraries/PHPTAL-1.2.2/PHPTAL.php';
		
		$viewFile = \SimplCMS::$app->basePath. "/core/modules/{$this->controller->moduleName}/views/{$this->name}.xhtml";
		
		try {
			
			$template = new \PHPTAL($viewFile);
			
			// Clear PHPTAL cache
//			if($this->clearCache === true)
//				$template->cleanUpCache();			
			
			// View file variable assignment
			// assign View object reference
			$template->set('this', $this);
			
			// Assign custom vars
			if(!empty($this->vars))
			{
				foreach ($this->vars as $varName => $varValue)
					$template->{$varName} = $varValue;
			}
			
			if($this->output === true)
				print $template->execute();
			else
				return $template->execute();
			
		}
		catch (Exception $e)
		{
			print $e->getMessage();
		}
	}
	
	/**
	 * Set view file name
	 * @param string $viewName
	 */
	public function setName($viewName)
	{
		$this->name = $viewName;
	}
	
	/**
	 * Set view file vars
	 * @param array $vars
	 */
	public function setVars(array $vars)
	{
		$this->vars = $vars;
	}
	
	public function clearCache()
	{
		$this->clearCache = true;
	}
	
	/**
	 * This method should always be used for safe view output. Never use 'print' or 'echo' alone.
	 * @param string $string
	 */
	public function output($string, $return = false)
	{
		$string = htmlspecialchars($string, ENT_COMPAT | ENT_HTML5, 'UTF-8');
		
		if($return === false)
			print $string;
		else
			return $string;
	}
}
