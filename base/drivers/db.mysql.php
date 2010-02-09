<?php
class TiiDbDriver_MYSQL implements TiiDbDriver
{
	private $conn;
	private $transaction_initiator;
	private $num_of_rows_affected;

	public function __construct()
	{
		$this->conn = mysql_connect (Tii::$db_hostname, Tii::$db_username, Tii::$db_password);
		mysql_select_db (Tii::$db_schema);
	}

	public function Query($sql, $getFirstRowOnly=false, $fetch_type=null, $column_index=null)
	{
        $rs = mysql_query($sql);

        if ($rs===false) throw new Exception('Error in query: '.$sql);

        if(class_exists('PDO') && $fetch_type == PDO::FETCH_COLUMN && $column_index !== null) {
            while($rows[] = mysql_fetch_row($rs));
        }
		else while($rows[] = mysql_fetch_assoc($rs));

        $rows=array_splice($rows, 0, -1);

        if(class_exists('PDO') && $fetch_type == PDO::FETCH_COLUMN && $column_index !== null){
            foreach($rows as $row) {
                $_rows[]=$row[$column_index];
                if ($getFirstRowOnly) break;
            }
            $rows =& $_rows;
        }

        if ($getFirstRowOnly && isset($rows[0])) $rows=$rows[0];

		return $rows;
	}

	public function Execute($sql)
	{
		mysql_query($sql);
	}

	public function GetColumns($tablename){
		$sql='describe ' . $tablename;
		$rows = $this->Query($sql);
		if (mysql_errno()!==0) return false;

		$columns = array();
		foreach ($rows as $row) $columns[] = $row['Field'];
		return $columns;
	}

	public function GetLastInsertId()
	{
	    return mysql_insert_id();
	}

	public function BeginTransaction()
	{
	}

	public function Rollback()
	{
	}

	public function Commit()
	{
	}

	public function GetConnection()
	{
		return $this->conn;
	}

	public function HasError()
	{
		return mysql_errno()!==0?true:false;
	}

	public function GetError()
	{
		return mysql_error();
	}

	public function NumOfRowsAffected()
	{


	}
}
