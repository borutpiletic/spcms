<?php
/**
 * User class represents base application user.
 */
namespace spcms\core;

class User extends BaseClass
{
	/**
	 * Current logged-in user id. Default: 0
	 * @var type 
	 */
	public $id = 1;
	
	/**
	 * User role ID
	 * @var string
	 */
	protected $roleId = 4;
	
	/**
	 * User role name
	 * @var string
	 */
	protected $role = 10;
	
	public function __construct() 
	{
		// Set logged-in user id
		if (($userId = \SimplCMS::$app->session->get('userId')) !== null)
			$this->id = $userId;
	}
	
	/**
	 * Preform user authentication based on Authentication interface.
	 * @param \spcms\core\Authentication $auth
	 */
	public function authenticate(Authentication $auth)
	{
		
	}
	
	/**
	 * Preform RBAC checking for permissions.
	 * @param string $moduleName
	 * @param string $action
	 * @return boolean
	 */
	public function hasAccess($moduleName, $action)
	{
		if ($this->id !== 0)
		{
			// Do RBAC checking
			$sql = 'SELECT roleId FROM mod_user_rbac ';
			$sql .= 'WHERE module = :module ';
			$sql .= 'AND action = :action ';
			$sql .= 'AND roleId = :roleId ';
			
			$query = \SimplCMS::$app->getDbConnection()->prepare($sql);			
			$query->bindParam(':module', $moduleName);
			$query->bindParam(':action', $action);
			$query->bindParam(':roleId', $this->roleId, \PDO::PARAM_INT);
			
			if ($query->execute()) 
			{
				if ($this->roleId == $query->fetchColumn())
					return true;
				return false;
			}	
			
			throw new Exception('Permission checking for some reason failed!');
		}
		return false;
	}
	
	/**
	 * TODO: Check current user access for current action being executed. Dynamically 
	 * by route.
	 */
	public function hasActionAccess()
	{
		
	}
	
	/**
	 * Get all defined user roles.
	 * @return array
	 */
	public function getRoles()
	{
		$query = \SimplCMS::$app->getDbConnection()->query('SELECT * FROM mod_user_roles ORDER BY id');
		
		if ($query !== false)
		{
			return $query->fetchAll(\PDO::FETCH_ASSOC);
		}
		return array();
	}
}
