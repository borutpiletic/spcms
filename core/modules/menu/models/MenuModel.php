<?php
namespace spcms\core\modules\menu\models;

use spcms\core\Model;
// Build menu with: http://mjsarfatti.com/sandbox/nestedSortable/


class MenuModel extends Model
{
	protected static $tableName = 'mod_menu';

	/**
	 * Return menu hierarchy structure as array.
	 * @param string $menuName
	 * @return array
	 */
	public function getStructure($menuName)
	{
		return self::buildMenuStructure( $this->getMenuItems($menuName) );
	}

	/**
	 * Build menu structure hierarchy.
	 * @param array $menuItems
	 * @param array $structure
	 * @return array
	 */
	public static function buildMenuStructure(array $menuItems)
	{	
		$structure = array();
		
		foreach ($menuItems as $item)
		{
			// Skip disabled menu items
			if($item['disabled'] == 1)
				continue;
			
			// Root level menu item
			if($item['parent'] == 0)
			{				
				// Override item only if it has no childrens
				if(empty($structure[$item['id']]))
					$structure[$item['id']] = $item;
				
				continue;
			}
			
			// Check if item parent exsists
			// If not, create parent and append child to it
			if(!isset($structure[$item['parent']]))
			{
				$structure[$item['parent']] = $menuItems[$item['parent']];
				$structure[$item['parent']]['children'][$item['id']] = $item;
			}
			else
			{	
				// Parent already exsists
				// Append child items
				$structure[$item['parent']]['children'][$item['id']] = $item;
			}
			
			// If item is also parent, append it to his parent
			if(array_key_exists($item['id'], $structure))
			{
				$structure[$item['parent']]['children'][$item['id']] = $structure[$item['id']];
				unset($structure[$item['id']]);
			}
		}
		
		return $structure;
	}
	
	/**
	 * Return menu ID for provided menu name
	 * @param string $menuName Machine-readble menu name
	 */
	public static function getMenuId($menuName)
	{
		$sql = "SELECT id FROM mod_menu WHERE sysMenuName = '{$menuName}' LIMIT 1";
		$query = \SimplCMS::$app->getDbConnection()->query($sql);
		return $query->fetchColumn();
	}

	/**
	 * Return menu items array
	 * @param int $menuId
	 * @return array
	 */
	public function getMenuItems($menuName)
	{
		$menuId = self::getMenuId($menuName);
		
		if(empty($menuId))
			throw new \Exception("Menu with name: '{$menuName}' could not be found!");
			
		// Join menu items with pages
		$sql  = "SELECT page.title, menuItem.name, menuItem.url AS url, menuItem.disabled, menuItem.parent, menuItem.weight, menuItem.pageId, menuItem.id FROM mod_menu AS menu ";
		$sql .= "JOIN mod_menu_item AS menuItem ON menu.id = menuItem.menuId ";
		$sql .= "JOIN mod_page AS page ON page.id = menuItem.pageId ";
		$sql .= "WHERE menuItem.menuId = {$menuId} ";
		$sql .= "ORDER BY menuItem.parent DESC ";
		
		$query = \SimplCMS::db()->query($sql);
		
		$menuItems = array();
		
		while ($item = $query->fetch(\PDO::FETCH_ASSOC))
			$menuItems[$item['id']] = $item;
		
		return $menuItems;
	}	
	
	/**
	 * Sorting callback method used by uasort function for sorting
	 * menu items by weight. Sort array element by [weight] key.
	 * 
	 * @param array $a1
	 * @param array $a2
	 * @return int
	 */
	public static function _sortByWeight($a1, $a2)
	{
		if($a1['weight'] === $a2['weight'])
			return 0;		
		
		return ($a1['weight'] < $a2['weight']) ? -1 : 1;
	}
	
}
