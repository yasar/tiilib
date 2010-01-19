<?php

interface TDbDriver
{
	public function Query($sql, $getFirstRowOnly=false, $fetch_type=null, $colum_index=null);
	public function GetColumns($tablename);
	public function Execute($sql);
	public function GetLastInsertId();
	public function BeginTransaction();
	public function Commit();
	public function Rollback();
	public function GetConnection();
	public function HasError();
	public function GetError();
	public function NumOfRowsAffected();
}


interface TUI{ //User interface
	public function SetTemplateEngine(TTemplate $object);
	public function GetHTML();
}