<?php
class TiiDB extends TiiCore
{
	/**
	*  Holds the Engine object
	* 
	 * @var TiiDbDriver
	 */
	private $E;
	
	/**
	* put your comment there...
	* 
	* @var string
	*/
	public $engine;
	public $host;
	public $schema;
	public $port;
	public $username;
	public $password;

	private $num_of_rows = 0;

	public function __construct($args='', $engine=null)
	{
		$this->LoadFromArray(Tii::Config('database'));
		is_null($engine) || $this->engine = $engine;
		Tii::Import('base/interfaces.php');
		Tii::Import('base/drivers/db.'.strtolower($this->engine).'.php');
		
		$_engine = new ReflectionClass('TiiDbDriver_'.$this->engine);
		$this->E = $_engine->newInstance($args);
		$this->E->_Parent(&$this)->_Initialize();
		
        $this->Execute("SET NAMES utf8");
        $this->Execute('SET CHARACTER_SET utf8');
	}
	
	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return String
	 */
	public function Path($val = null){
		return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath());
	}
	
	public function GetConnection(){
		return $this->E->GetConnection();
	}

	/**
	 * Runs the given query on the database
	 * and returns the resultset as an array
	 *
	 * @param string $sql
	 * @return array
	 */
	public function Query($sql, $getFirstRowOnly=false, $fetch_type=null, $colum_index=null)
	{
		//error_log($sql);
		return $this->E->Query($sql,$getFirstRowOnly, $fetch_type, $colum_index);
	}

	/**
	 * @return PDO
	 */
	public function PDO()
	{
		if (Tii::$db_engine == 'PDO') return $this->E->GetConnection();
		return false;
	}

	public function GetColumns($tablename)
	{
		return $this->E->GetColumns($tablename);
	}

	public function Execute($sql)
	{
		return $this->E->Execute($sql);
	}

	public function GetLastInsertId()
	{
		return $this->E->GetLastInsertId();
	}

	public function BeginTransaction()
	{
		return $this->E->BeginTransaction();
	}

	public function Rollback()
	{
		return $this->E->Rollback();
	}

	public function Commit()
	{
		return $this->E->Commit();
	}

	public function NumOfRows()
	{
		return $this->num_of_rows;
	}

	public function NumOfRowsAffected()
	{
		return $this->E->NumOfRowsAffected();
	}

	public function PrepareListQuery(ListView &$settings){
		if (empty($settings->primary_id)) throw new Exception('Primary ID is not set.');

		#..first check if the row sorting feature is enabled.
		if ($settings->enable_row_sorting):
			#..check if there is any move command is passed in query string.
			if (TiiR::G('move') && TiiR::G('move_id')):
				self::BeginTransaction();
				try{
					$current_sortorder = TiiR::G('move_from');

					$sql='select '.$settings->row_sorting_field.' from '.$settings->table_name.' where true '.$settings->row_sorting_group_filter.' and sort_order '.(TiiR::G('move')=='up'?'<':'>').$current_sortorder.' order by '.$settings->row_sorting_field.(TiiR::G('move')=='up'?' desc':' asc').' limit 0,1';
					$base_sortorder = self::Query($sql,true);

					if (! empty($base_sortorder)):
						$new_sortorder = $base_sortorder['sort_order'];
					else:
						$new_sortorder = TiiR::G('move')=='up'?$current_sortorder:$current_sortorder+1;
					endif;

					$current_sortorder == 0 && $current_sortorder=1;

					$sql='update '.$settings->table_name.' set '.$settings->row_sorting_field.'='.$current_sortorder.' where true '.$settings->row_sorting_group_filter.' and sort_order='.$new_sortorder;
					//FUNC::Log($sql);
					self::Execute($sql);

					$sql='update '.$settings->table_name.' set '.$settings->row_sorting_field.'='.$new_sortorder.' where true '.$settings->row_sorting_group_filter.' and '.$settings->primary_id.'='.TiiR::G('move_id');
					//FUNC::Log($sql);
					self::Execute($sql);

					self::Commit();

					header('location:'.FUNC::RUP(array('move'=>'', 'move_id'=>'', 'move_from'=>'')));
				}catch(Exception $e){
					self::Rollback();
				}
			endif;
		endif;

		$where_search='';
		if ($settings->enable_search && !empty($settings->search_for)){
			$searchables=$settings->GetSearchables();

			//var_dump($searchables);

			if (is_array($searchables) && count($searchables)>0){
				#..try to retrieve the field types from the database
				$sql_temp='select `'.implode('`,`',$searchables).'` from '.$settings->table_name.' limit 0,0';
				//echo $sql_temp;
				$sth_columns=$this->PDO()->query($sql_temp);

				$where_search='false ';

				$columnCtr=0;
				foreach($searchables as $searchable){
					#..get the column meta info
					#..so that we can determine if this column is suitable for search with the key.
					#..if the search_key is string and the search field is integer, then
					#..we shouldn't include this field in the where clause.
					$meta=$sth_columns->getColumnMeta($columnCtr);

					$_include=false;

					//echo strtolower($meta['native_type']),',';

					switch(strtolower($meta['native_type'])){
						case 'integer':
						case 'long':
							if(is_numeric($settings->search_for)) {
								$_include=true;
								$_use_like=false;
							}
							break;
						case 'date':
							$_include=true;
							$_use_like=false;
							break;
						case 'string':
						case 'var_string':
							$_include=true;
							$_use_like=true;
							break;
					}

					#..check if the key to be included in the filter query.
					if ($_include)	$where_search.='or '.$searchable.
					(($_use_like)?(' like "%'.($settings->search_for).'%" '):(' = "'.$settings->search_for.'"'));

					#..increment column number.
					$columnCtr++;
				}
				$sth_columns->closeCursor();
			}

		}

		$_static_filter='';
		$_static_filter=$settings->GetStaticFilter();

		$_filter='';
		//echo $settings->enable_filter;
		if ($settings->enable_filter) $_filter=$settings->GetFilter();
		//echo $_filter;

		$field_list='`'.implode('`, `',array_merge($settings->GetFields(true),$settings->GetFieldsMore(),($settings->enable_row_sorting?array('sort_order'):array()))).'`';

		$sql='select SQL_CALC_FOUND_ROWS {[field_list]} from '.$settings->table_name;

		$where='';
		if (!empty($_static_filter))$where.=' and ('.$_static_filter.')';
		if (!empty($_filter))$where.=' and ('.$_filter.')';
		if (!empty($where_search))$where.=' and ('.$where_search.')';

		if(!empty($where)) $sql.=' where true'.$where;

		$order_by = array();
		$_static_sort_by = $settings->GetStaticSortBy();
		if(! empty($_static_sort_by)) $order_by[]=$_static_sort_by;

		if ($settings->enable_sort && !empty($settings->sort_by))
			$order_by[]=$settings->sort_by.' '.(strtolower($settings->sort_order)=='ascending'?'ASC':'DESC');
//			$sql.=' order by '.$settings->sort_by.' '.(strtolower($settings->sort_order)=='ascending'?'ASC':'DESC');
		elseif ($settings->enable_row_sorting)
			$order_by[]=$settings->row_sorting_field.' asc';
//			$sql.=' order by '.$settings->row_sorting_field.' asc';

		if (! empty($order_by)) $sql .= ' order by ' . implode(', ', $order_by);

		#..check if the page number requested is not greater than total pages.
		$this->query(str_replace('{[field_list]}',$settings->primary_id,$sql));
		$row=$this->query('SELECT FOUND_ROWS();');
		$settings->SetTotalRecords($row[0]['FOUND_ROWS()']);
		//error_log($settings->GetTotalRecords());
		if (($settings->num_of_pages=ceil($settings->GetTotalRecords()/$settings->records_per_page))<$settings->page_number)
		$settings->page_number=$settings->num_of_pages;

		#..complete the sql.
		$sql=str_replace('{[field_list]}',$field_list,$sql);

		$n=$settings->records_per_page;
		if($settings->page_number==0) $m=0;
		else $m=($settings->page_number-1)*$n;

		$sql.=' limit '.$m.','.$n;

		//echo $sql;
		//exit;
//		error_log($sql);

		return $sql;
	}

	public function HasError()
	{
		return $this->E->HasError();
	}

	public function GetError()
	{
		return $this->E->GetError();
	}


public function BuildFieldsValuesFromPost($tablename, $excluded_fields=null, $join_array=true)
{
	$columns = $this->GetColumns($tablename);
	$fields = array();
	$values = array();
	foreach($columns as $column):
		if (isset($_POST[$column])):
            if (!empty($excluded_fields) && in_array($column, $excluded_fields)) continue;

			$fields[]=$column;
			$values[]=(is_array($_POST[$column]) && $join_array)?implode(',',FUNC::DbEntry($_POST[$column])):FUNC::DbEntry($_POST[$column]);
		endif;
	endforeach;

	return array($fields, $values);
}

/**
 * @param unknown_type $tablename
 * @param array $fields
 * @param array $values
 * @return PDOStatement
 */
public function GetInsertStatement($tablename, Array &$fields, Array &$values)
{
	if (empty($tablename) || empty($fields) || empty($values)) trigger_error('Required variable cannot be empty. ['.$tablename.' : getInsert]', E_USER_ERROR);
	if(count($fields) !== count($values)) trigger_error('fields and values have to be of the same size.['.$tablename.' : getInsert]', E_USER_ERROR);

	$_fields = array();
	foreach($fields as $field) $_fields[]=':'.$field;

	$sql = 'insert into '.$tablename.'('.implode(',', $fields).') values ('.implode(', ',$_fields).')';
	$sth = $this->prepare($sql);
	for($i=0, $j=count($fields); $i<$j; $i++):
		$sth->bindValue($_fields[$i], $values[$i]);
	endfor;

	return $sth;
}

/**
 * @param unknown_type $tablename
 * @param array $fields
 * @param array $values
 * @param unknown_type $where
 * @return PDOStatement
 */

public function GetUpdateStatement($tablename, Array $fields, Array $values, $where='')
{
	if (empty($tablename) || empty($fields) || empty($values)) trigger_error('Required variable cannot be empty. ['.$tablename.' : getUpdate]', E_USER_ERROR);
	if(count($fields) !== count($values)) trigger_error('fields and values have to be of the same size.['.$tablename.' : getUpdate]', E_USER_ERROR);

	$updates = array();
	$_fields = array();
	for($i=0, $j=count($fields); $i<$j; $i++):
		$updates[] = $fields[$i] . ' = '.($_fields[]=':'.$fields[$i]).'';
	endfor;

	$sql = 'update '.$tablename.' set '.implode(', ', $updates).' where true and '.$where;
	$sth = $this->prepare($sql);

	for($i=0, $j=count($fields); $i<$j; $i++):
		$sth->bindParam($_fields[$i], $values[$i]);
	endfor;

	return $sth;
}

public function BuildInsertQuery($tablename, Array &$fields, Array &$values)
{
	if (empty($tablename) || empty($fields) || empty($values)) trigger_error('Required variable cannot be empty. ['.$tablename.' : buildInsert]', E_USER_ERROR);
	if(count($fields) !== count($values)) trigger_error('fields and values have to be of the same size.['.$tablename.' : buildInsert]', E_USER_ERROR);

	return 'insert into '.$tablename.'('.implode(',', $fields).')
	values ("'.implode('", "',$values).'")';
}

public function BuildUpdateQuery($tablename, Array &$fields, Array &$values, $where='')
{
	if (empty($tablename) || empty($fields) || empty($values) || empty($where)) trigger_error('Required variable cannot be empty.['.$tablename.' : buildUpdate]', E_USER_ERROR);
	if(count($fields) !== count($values)) trigger_error('fields and values have to be of the same size.['.$tablename.' : buildUpdate]', E_USER_ERROR);

	$updates = array();
	for($i=0, $j=count($fields); $i<$j; $i++):
        $updates[] = $fields[$i] . '="'.(FUNC::DbEntry($values[$i])).'"';
	endfor;

	return 'update '.$tablename.' set '.implode(', ', $updates).' where true and '.$where;
}

public function BuildInsertOrUpdateWithDuplicateKeyQuery($tablename, Array &$fields, Array &$values)
{
    if (empty($tablename) || empty($fields) || empty($values)) trigger_error('Required variable cannot be empty. ['.$tablename.' : buildInsert]', E_USER_ERROR);
    if(count($fields) !== count($values)) trigger_error('fields and values have to be of the same size.['.$tablename.' : buildInsert]', E_USER_ERROR);

    $updates = array();
    for($i=0, $j=count($fields); $i<$j; $i++):
        $updates[] = $fields[$i] . '="'.($values[$i]).'"';
    endfor;

    $updates=implode(', ', $updates);

    return 'insert into '.$tablename.' set '.$updates.' on duplicate key update '.$updates;

}


}