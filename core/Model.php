<?php
/**
 * Model class represents application model with Active Record capabilies,  
 * which features are provided by IdiORM library.
 */
namespace spcms\core;

abstract class Model extends BaseClass
{	
	/**
	 * List of attributes that should be loaded by Model::prepareModel.
	 * @var type 
	 */
	protected $attributes = array();

	/**
	 * Table name on which this model will operate
	 * @var string
	 */
	protected static $tableName;
	
	/**
	 * Construct model
	 * @param string $tableName
	 * @param boolean $lazyLoad If true, model will be automatically loaded.
	 */
	public function __construct($tableName = null, $lazyLoad = true)
	{
		//parent::__construct();
		
		if (isset($tableName))
			$this->tableName = $tableName;
		
		// Lazy load model
		if($lazyLoad === true && !empty($this->tableName)) 
			$this->prepareModel();
	}
	
	private static function initORMModel()
	{		
		// Start IdiORM
		require_once \SimplCMS::$app->basePath. '/core/libraries/php/idiorm/idiorm_1.4.0.php';
		$config = \SimplCMS::$app->getConfig();
		
		\ORM::configure("mysql:host={$config->dbHost};dbname={$config->dbName}");
		\ORM::configure('username', $config->dbUser);
		\ORM::configure('password', $config->dbPass);
	}
	
	/**
	 * Retrun ORM model instance
	 * @return \ORM
	 */
	public static function orm()
	{
		self::initORMModel();
		
		return \ORM::for_table(static::$tableName);
	}
	
	/**
	 * Populate model with attibutes defined in Model::$attributes.
	 */
	protected function prepareModel()
	{
		// Assign model attributes		
		$model = $this->orm()->find_one(3);
		
		foreach ($this->attributes as $attr)
		{
			if (isset($model->{$attr}))
				$this->{$attr} = $model->{$attr};
		}
	}
}
