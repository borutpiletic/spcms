<?php
namespace spcms\core\modules\user\models;

use spcms\core\BaseClass;
use spcms\core\Plugin;

class User extends BaseClass
{
	private $id = 10;
	
	protected $tableName = 'mod_user';
	
	public function init() 
	{		
		//$this->notify('onLoad', array('tableName' => $this->tableName) );
		$this->notify('onCreate', array('tableName' => $this->tableName) );
	}
}



