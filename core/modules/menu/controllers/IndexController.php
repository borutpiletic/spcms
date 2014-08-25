<?php
namespace spcms\core\modules\menu\controllers;

use spcms\core\modules\menu;

class IndexController extends \spcms\core\Controller
{
	public function indexAction() 
	{
		
	}
	
	/**
	 * Update action via AJAX from jqtree (on move event)
	 */
	public function updateStructureAction()
	{
		if ( \SimplCMS::$app->request->isPost() === true)
		{
			// Get JSON params for menuItem
			$menuItem = array(
				'id'	 => \SimplCMS::$app->request->getPostParam('menuItemId'),
				'parent' => \SimplCMS::$app->request->getPostParam('menuItemParentId'),
				'weight' => \SimplCMS::$app->request->getPostParam('weight')
			);
			
			// Update menu item parent & order
			$updateMenuItem = menu\models\MenuItemModel::orm()->find_one($menuItem['id']);
			$updateMenuItem->parent = $menuItem['parent'];
			
			if ($updateMenuItem->save() == true) 
			{
				menu\models\MenuItemModel::updateMenuItemOrder($menuItem);
				print 1;
			}
		}
		else		
			exit(0);
	}
}
