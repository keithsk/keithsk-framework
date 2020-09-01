<?php

namespace Keithsk;

use PDO;

class Database
{
	private $dbh;
	private $error;
	private $stmt;

	private $db_host;
	private $db_username;
	private $db_password;
	private $db_name;

	public function __construct()
	{
		// Get and set db config
		$dbConfig = get_config('database');
		
		if(is_array($dbConfig)) {
			foreach($dbConfig as $configKey => $configVal) {
				$this->$configKey = $configVal;
			}
		}

		// Set DSN
		$dsn = 'mysql:host=' . $this->db_host . ';dbname=' . $this->db_name;

		// Set options
		$options = array(
			PDO::ATTR_PERSISTENT => true, 
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		// Create a new PDO instance
		try { 
			$this->dbh = new PDO($dsn, $this->db_username, $this->db_password, $options);
		}
		// Catch the errors using exceptions handler
		catch(PDOException $e){
			$this->error = $e->getMessage();
		}
	}

	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}

	public function bind($param, $value, $type = null){
		if(is_null($type)){
			switch(true){
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;

				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;

				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;

				default : 
					$type = PDO::PARAM_STR;
			}
		}

		$this->stmt->bindValue($param, $value, $type);
	}


	public function execute(){
		return $this->stmt->execute();
	}

	public function execute2($params = []){
		return $this->stmt->execute($params);
	}

	public function resultSet(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_OBJ);
	}

	public function resultSetArray(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function singleArray(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount(){
		return $this->stmt->rowCount();
	}

	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}

	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}

	public function endTransaction(){
		return $this->bbh->commit();
	}

	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}

}