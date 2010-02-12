<?php

class TiiModule extends TiiCore{
	/**
	 * @var TiiConfig
	 */
	private $_Config=null;
    
    private $_Controller=null;

    private $_DB;
    
    protected $path;
    


	public function __construct(){
		parent::__construct();

		$this->path = $this->GetCreatorsPath();

		$this->LoadConfig();
		
		$this->_DB =& Tii::DB();
		
		Tii::App()->Module(& $this);
	}

	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return String
	 */
	public function x__Path($val = null){
		return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath());
	}
	
	public function Path(){return $this->path;}
	
	/**
	* put your comment there...
	* @return TiiDB
	*/
	public function DB(){return $this->_DB;}

	private function LoadConfig(){
		$config_file = $this->path._.'config.json';
		if(file_exists($config_file)) {
			$this->_Config = new TiiConfig();
			$this->_Config->LoadFromFile($config_file);
		}
	}
	
	protected function Config(){
		
	}
    
    public function Controller(TiiController $val=null){
        return $this->GetOrSet('_'.__FUNCTION__, $val);
    }
}
