<?php
namespace spcms\core\modules\menu\models;

use spcms\core\Model;

class MenuItemModel extends Model 
{
	protected static $tableName = 'mod_menu_item';
	
	/**
	 * Update menu item order
	 * @param array $menuItem Menu item array with following keys: itemId, weight
	 */
	public static function updateMenuItemOrder(array $menuItem)
	{
		$sql = 'UPDATE '. self::$tableName. ' SET weight = :weight WHERE id = :itemId';

		$statement = \SimplCMS::$app->getDbConnection()->prepare($sql);
		$statement->bindValue(':weight', $menuItem['weight']);
		$statement->bindValue(':itemId', $menuItem['id']);

		if ($statement->execute() === false)
			throw new Exception('Menu item update failed!');
	}
}