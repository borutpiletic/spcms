<?php
namespace spcms\core;

class Database
{
	/**
	 * @var \PDO
	 */
	protected $connection;
	
	protected $errors = array();
	
	/**
	 * Last executed PDO statement
	 * @var \PDOStatement
	 */
	protected $lastStatement;
	
	/**
	 * Init database
	 * @param array $config
	 * @return \PDO
	 */
	public function __construct(array $config = array()) 
	{
		if(empty($config))
			return $this->connect();
		
		// Set default charset - UTF8
		if (!isset($config['charset']))
			$config['charset'] = 'utf8';
		
		// Create custom connection. Useful when connecting to multiple databases.		
		$this->connection = new \PDO("{$config['type']}:host={$config['host']};dbname={$config['name']};charset={$config['charset']};", $config['user'], $config['pass']);
		
		// turn off silent mode
		$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		
		return $this->connection;
	}
	
	/**
	 * Execute query
	 * @param string $sql
	 * @return \PDOStatement
	 */
	public function query($sql)
	{		
		$this->lastStatement = $this->connection->query($sql);
		
		if($this->lastStatement !== false)
			return $this->lastStatement;
		
		$errorInfo = $this->connection->errorInfo();
		$this->errors[] = $errorInfo;
		
		throw new \Exception('Unable to run your query: '. $errorInfo[2]);
	}

	/**
	 * Return query result as array of records
	 * @param int $fetchStyle
	 * @return array
	 * @throws \Exception
	 */
	public function fetchArray($fetchStyle = \PDO::FETCH_ASSOC) 
	{	
		if($this->lastStatement !== null)		
			return $this->lastStatement->fetchAll($fetchStyle);
		
		throw new \Exception('No statement found executed by Database::query method.');
	}
	
	/**
	 * Fetch single row
	 * @param int $fetchStyle
	 * @return mixed
	 * @throws \Exception
	 */
	public function fetchRow($fetchStyle = \PDO::FETCH_ASSOC)
	{
		if($this->lastStatement !== null)		
			return $this->lastStatement->fetch($fetchStyle);
		
		throw new \Exception('No statement found executed by Database::query method.');		
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	public function getConnection()
	{
		if($this->connection !== null)
			return $this->connection;
		
		return $this->connect();
	}
	
	
	/**
	 * Connect to database and return connection PDO
	 * @return \PDO
	 */
	protected function connect()
	{
		$config = \SimplCMS::$app->getConfig();

		try
		{
			// Set default chartset
			if (!isset($config->dbCharset))
				$config->dbCharset = 'utf8';
			
			$this->connection = new \PDO("{$config->dbType}:host={$config->dbHost};dbname={$config->dbName};charset={$config->dbCharset}", $config->dbUser, $config->dbPass);
		}
		catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			$this->errors[] = $this->connection->errorInfo();
		}
		
		return $this->connection;
	}

		/**
	 * Simple SQL statement generator
	 * @param string $tableName
	 * @param string|array $select
	 * @param array $condition
	 * @return string
	 */
	public function buildSQL($tableName, $select = '*', array $condition = array(), array $order = array())
	{
		// Select
		$sql = 'SELECT ';
		
		if(is_array($select) && !empty($select))
			$sql .= implode($select, ', ');
		
		// From
		$sql .= " FROM {$tableName} ";
		
		// Where
		$sql .= ' WHERE ';
		
		$conditionSize = sizeof($condition)-1;
		$conditionAttr = array_keys($condition);
		
		foreach ($conditionAttr as $i => $attr)
		{
			$sql .= "{$attr} = '{$attr}' ";
			
			if($conditionSize > $i) 
				$sql .= ' AND ';
		}
		
		return $sql;
	}
	
	/**
	 * Build PDO prepared statement
	 * @return \PDOStatement
	 */
	public function buildStatement($sql, array $bindValues = array())
	{
		$statement = $this->connection->prepare($sql);
		
		if(!empty($bindValues))
		{
			foreach ($bindValues as $attr => $value)
				$statement->bindValue($attr, $value);
		}
		
		return $statement;
	}
}