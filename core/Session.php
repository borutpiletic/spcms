<?php
namespace spcms\core;

class Session
{
	private $storage;
	
	private $sessionHash;
	
	const CORE_NAMESPACE = 'SIMPLCMS_';

	public function __construct() 
	{
		if(!session_start())
			throw new \Exception('Session could not be started');
		
		// Create CMS internal session hash
		$this->sessionHash = Session::CORE_NAMESPACE. session_id();
		
		if(!isset($_SESSION[$this->sessionHash]))
			$_SESSION[$this->sessionHash] = new \stdClass;
		
		$this->storage = $_SESSION[$this->sessionHash];
	}
	
	/**
	 * Set session variable
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$this->storage->{$name} = $value;
	}

	/**
	 * Get session variable
	 * @param string $name
	 * @return mixed, null on empty.
	 */
	public function get($name)
	{
		if(isset($this->storage->{$name}))
			return $this->storage->{$name};
		else
			return null;
	}	
	
	/**
	 * Destroy CMS internal session or global PHP session.
	 * @param boolean $global If true, global PHP session will be destroyed.
	 */
	public function destroy($global = false)
	{
		if($global === true)
		{
			$_SESSION = array();
			return session_destroy();
		}
		else
			unset($_SESSION[$this->sessionHash]);
	}
}