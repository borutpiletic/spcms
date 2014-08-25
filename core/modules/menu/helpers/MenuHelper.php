<?php
namespace spcms\core\modules\menu\helpers;

use spcms\core\modules\menu;

class MenuHelper
{
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
	public static function htmlList($menuName, array $attributes = array())
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
	
	/**
	 * Get hierarchical menu structure
	 * 
	 * @param string $menuName
	 * @return array
	 */
	public static function getMenuStructure($menuName)
	{
		$menu = new menu\models\MenuModel;
		return $menu->getStructure($menuName);
	}
	
	/**
	 * Get all menus (warning: also returns system menus).
	 * @return array
	 */
	public static function getMenus()
	{
		$sql = "SELECT * FROM mod_menu";
		$query = \SimplCMS::$app->getDbConnection()->query($sql);
		return $query->fetchAll( \PDO::FETCH_ASSOC );		
	}	
}
