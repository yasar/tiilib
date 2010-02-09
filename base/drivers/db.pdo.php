<?php
class TiiDbDriver_PDO implements TiiDbDriver
{
	/**
	 * @var PDO
	 */
	private $conn;
	private $transaction_initiator;
	private $num_of_rows_affected;
	
	/**
	* Holds reference to TiiDB interface
	* used for accessing the configuration
	* 
	* @var TiiDB
	*/
	private $parent;
	
	public function __construct()
	{
		$this->transaction_initiator=null;
	}
	
	public function _Parent($parent=null){
		if(is_null($parent)) return $this->parent;
		$this->parent = $parent;
		return $this;
	}
	
	public function _Initialize(){
		$this->conn = new PDO(
							'mysql:host='.$this->parent->host.';
							port='.$this->parent->port.';
							dbname='.$this->parent->schema,
							$this->parent->username,
							$this->parent->password,
							array(
								PDO::ATTR_PERSISTENT => false,
							));
	}

	/**
	 * @return PDO
	 */
	public function GetConnection()
	{
		return $this->conn;
	}

	/**
	 * Returns true if any error occured
	 *
	 * @return boolean
	 */
	public function HasError()
	{
		return $this->conn->errorCode()=='00000'?false:true;
	}
	/**
	 * Runs the given query on the database
	 * and returns the resultset as an array
	 *
	 * @param string $sql
	 * @return array
	 */
	public function Query($sql, $getFirstRowOnly=false, $fetch_type=null, $column_index=null)
	{
		is_null($fetch_type) && $fetch_type = PDO::FETCH_ASSOC;
		$sth = $this->conn->query($sql);
		if ($sth === false):
			if ($this->conn->errorCode() !== '00000'):
				$e=$this->conn->errorInfo();
				//FUNC::Log($sql);
				//FUNC::Log($e[2]);
			endif;
			return false;
		endif;
		
		if ($getFirstRowOnly || $fetch_type == PDO::FETCH_OBJ){
			if ($fetch_type == PDO::FETCH_COLUMN) return $sth->fetch($fetch_type, $column_index);
			return $sth->fetch($fetch_type);
		}else{
			if ($fetch_type == PDO::FETCH_COLUMN) $rows = $sth->fetchAll($fetch_type,$column_index);
			else $rows = $sth->fetchAll($fetch_type);
			return $rows;
		}
		/*
		if ($fetch_type == PDO::FETCH_COLUMN) $rows = $sth->fetchAll($fetch_type,$column_index);
		else $rows = $sth->fetchAll($fetch_type);
		if ($getFirstRowOnly && isset($rows[0])) $rows=$rows[0];
		return $rows;
		*/
	}

	public function GetColumns($tablename)
	{
		$sql='describe '.$tablename;
		return $this->conn->query($sql)->fetchAll(PDO::FETCH_COLUMN);
	}

	public function Execute($sql)
	{
		//$this->conn->exec($sql);
		$sth=$this->conn->prepare($sql);
		$sth->execute();

		$this->num_of_rows_affected = $sth->rowCount();
	}

	public function GetLastInsertId()
	{
		return $this->conn->lastInsertId();
	}

	public function BeginTransaction()
	{
		return $this->conn->beginTransaction();
	}

	public function Rollback()
	{
		return $this->conn->rollBack();
	}

	public function Commit()
	{
		return $this->conn->commit();
	}

	public function GetError()
	{
		$arr=$this->conn->errorInfo();
		return implode('|',$arr);
	}

	public function NumOfRowsAffected()
	{
		return $this->num_of_rows_affected;
	}
}
