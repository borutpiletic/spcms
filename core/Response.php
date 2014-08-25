<?php
namespace spcms\core;

class Response
{
	/**
	 * Common used HTTP status codes
	 * @var array
	 */
	public static $httpStatusCodes = array(
		200 => '200 OK',
		301 => '301 Moved Permanently',
		403 => '403 Forbidden',
		404 => '404 Not found'
	);	
	
	
	/**
	 * Current HTTP status, set by Response::setHttpStatus
	 * @var int 
	 */
	private $_currentHttpStatus = null;


	/**
	 * Create a JSON HTTP response
	 * @param array $data
	 * @param bool $output
	 */
	public function json(array $data, $output = true)
	{
		$data = json_encode($data);
		
		if($output === true)
		{
			header('Content-type: application/json');
			print $data;
		}
		else
			return $data;
	}
	
	/**
	 * Set HTTP header status by code.
	 * @param string $code E.g.: 404, 402, etc..
	 * @return Response
	 */
	public function setHttpStatus($code)
	{
		if (array_key_exists($code, self::$httpStatusCodes) === true)
		{
			// Set internal HTTP status code
			$this->_currentHttpStatus = $code;
			header('HTTP/1.0 '. self::$httpStatusCodes[$code]);
			
			return $this;
		}
		else
			throw new \Exception("No HTTP status founded for provided code: {$code}");
	}
	
	/**
	 * Display error page and terminate script execution.
	 * @param int $code HTTP reponse code
	 * @param string $template Tempalte name.
	 */
	public function displayErrorPage($code = null, $template = 'theme')
	{
		// We make sure that HTTP response code is set
		if ($code === null && $this->_currentHttpStatus === null)
			throw new Exception('No HTTP status code provided!');
		
		// Make sure status $code is avaliable
		$code = ($code === null) ? $this->_currentHttpStatus : $code;		
		
		// Default core error page template
		$coreErrorPage = \SimplCMS::$app->basePath. '/core/modules/page/views/error-page.php';		
		
		// Check for custom response code template
		$customErrorPage = \SimplCMS::$app->theme->basePath. '/error-page.php';
		
		// Render custom error page or display error message and terminate script
		if (file_exists($customErrorPage) === true)
			require_once $customErrorPage;
		else 
			require_once $coreErrorPage;
		
		exit;
	}
}