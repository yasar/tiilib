<?php
interface TiiDbDriver
{
	public function _Initialize();
	public function _Parent($parent=null);
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


interface TiiUI{ //User interface
	public function SetTemplateEngine(TiiTemplate $object);
	public function GetHTML();
}

interface TiiDomDriver{
	public function FindNode($path);
	public function InnerHTML($html=null);
}