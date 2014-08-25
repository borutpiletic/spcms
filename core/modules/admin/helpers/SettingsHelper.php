<?php
namespace spcms\core\modules\admin\helpers;

use spcms\core\BaseClass;

class SettingsHelper extends BaseClass
{
	/**
	 * Select all module names which define RBAC.
	 * @return array
	 */
	public function getRbacModules()
	{
		$query = \SimplCMS::$app->getDbConnection()->query('SELECT DISTINCT module AS name FROM mod_user_rbac');
		
		if ($query !== false)
			return $query->fetchAll(\PDO::FETCH_ASSOC);

		return array();		
	}
	
	/**
	 * Select all module names which define RBAC.
	 * @return array
	 */
	public function getRbacModuleActions($moduleName)
	{
		$sql = 'SELECT DISTINCT action AS name FROM mod_user_rbac';
		$sql .= ' WHERE module = :moduleName';
		
		$query = \SimplCMS::$app->getDbConnection()->prepare($sql);
		$query->bindParam(':moduleName', $moduleName);
		$query->execute();
		
		if ($query !== false)
		{
			return $query->fetchAll(\PDO::FETCH_ASSOC);
		}
		return array();		
	}
	
	/**
	 * Check RBAC permission against Role ID. 
	 * Return TRUE if has permission or FALSE otherwise.
	 * @param string $moduleName
	 * @param string  $action
	 * @param int $roleId
	 * @return boolean
	 */
	public function getRbacPermission($moduleName, $action, $roleId)
	{
		// Do RBAC checking
		$sql = 'SELECT roleId FROM mod_user_rbac ';
		$sql .= 'WHERE module = :module ';
		$sql .= 'AND action = :action ';
		$sql .= 'AND roleId = :roleId ';
		$sql .= 'LIMIT 1';

		$query = \SimplCMS::$app->getDbConnection()->prepare($sql);			
		$query->bindParam(':module', $moduleName);
		$query->bindParam(':action', $action);
		$query->bindParam(':roleId', $roleId, \PDO::PARAM_INT);

		if ($query->execute()) 
		{
			if ($roleId == $query->fetchColumn())
				return true;
			return false;
		}	

		throw new Exception('Permission checking for some reason failed!');
	}
}
 

