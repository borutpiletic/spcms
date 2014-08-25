<?php
/**
 * This helper class is taking functionality of MenuModel class
 * to more specific level: building JSON tree structure which will be used by facyTree
 * @see https://github.com/mar10/fancytree/wiki
 */
namespace spcms\core\modules\menu\helpers;

use spcms\core\modules\menu;

class fancyTreeHelper 
{
	/**
	 * Menu name
	 * @var string 
	 */
	private $menuName;
	
	/**
	 * @param string $menuName
	 */
	public function __construct($menuName)
	{
		$this->menuName = $menuName;
	}
	
	/**
	 * Return menu items prepared for jqtree structure
	 * @param string $menuName
	 * @return array
	 */
	public function getJsonMenuItems()
	{
		$menuId = menu\models\MenuModel::getMenuId($this->menuName);
		
		if(empty($menuId))
			throw new \Exception("Menu with name: '{$this->menuName}' could not be found!");
			
		// Join menu items with pages
		$sql  = "SELECT page.title, menuItem.name AS label, menuItem.disabled, menuItem.parent, menuItem.weight, menuItem.pageId, menuItem.id FROM mod_menu AS menu ";
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
	 * Get menu hierarchy as JSON structure used by jqTree.
	 * @return string JSON structure
	 */
	public function getJsonStructure()
	{
		// Build raw menu structure
		$structure = menu\models\MenuModel::buildMenuStructure( $this->getJsonMenuItems($this->menuName) );

		// Fixit into Javascript form
		$structure = self::buildJsonTreeStructure($structure);
		
		return (!empty($structure)) ? json_encode($structure) : json_decode( array() );
	}
	
	/**
	 * Build PHP array structure into proper Javascript JSON structure, 
	 * making arrays 0 indexed.
	 * 
	 * @param array $menuStructure Menu strcuture from menu\models\MenuModel::buildMenuStructure
	 * @return array
	 */
	private static function buildJsonTreeStructure($menuStructure, $sort = true) 
	{
		// Sort by weight
		if($sort === true)
			uasort($menuStructure, array('spcms\core\modules\menu\models\MenuModel','_sortByWeight'));		
		
		//dump($menuStructure); exit;
		
		$jsTreeStructure = array();
		
		foreach ($menuStructure as $itemKey => $itemObj) 
		{
			$item = array();
			
			foreach ($itemObj as $key => $value) 
			{
				if ($key === 'children')
					$item[$key] = self::buildJsonTreeStructure($value);
				else
					$item[$key] = $value;				
			}
			
			$jsTreeStructure[] = $item;
		}
		
		return $jsTreeStructure;
	}
	
	/**
	 * Build HTML system menu tree structure created by Menu::getStructure
	 * @param array $menuStructure
	 */
	private static function buildHtmlMenu(array $menuStructure, $sort = true)
	{
		$output = '';
		
		// Tree level depth counter
		static $level;

		// Sort by weight
		if($sort === true)
			uasort($menuStructure, array('spcms\core\modules\menu\models\MenuModel','_sortByWeight'));
		
		foreach ($menuStructure as $item)
		{
			// Reset level counter if item is root
			if($item['parent'] == 0)
				$level = 0;

			$output .= "<li class='level-{$level}'><a href='{$item['pageId']}'>{$item['name']}</a>\n";
			
			if(isset($item['children']) && is_array($item['children']))
			{
				$level++;
				$output .= "<ul class='level-{$level}'>\n";
				$output .= self::buildHtmlMenu($item['children']);
				$output .= "</ul>\n";
			}
		}
		
		return $output;
	}
	
	/**
	 * Return structure menu HTML list.
	 * 
	 * @param string $menuName
	 * @param array $attributes HTML list attributes
	 * @return string HTML list structure
	 */
	public static function htmlList($menuStructure, array $attributes = array())
	{
		$menu = self::getMenuStructure($menuName);
		
		// Add special level counter class
		$attributes['class'][] = 'level-0';
		$attributes = \spcms\core\HtmlRender::buildAttributes($attributes);		
		
		$output	 = "<ul {$attributes}>";
		$output .= self::buildHtmlMenu($menu);
		$output .= '</ul>';
		
		return $output;
	}	
	
}