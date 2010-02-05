<?php

class TiiModule extends TiiCore{
	/**
	 * @var TiiConfig
	 */
	protected $Config;
    
    //protected $Controller;



	public function __construct(){
		parent::__construct();

		$this->path = $this->GetCreatorsPath();

		$this->Config = new TiiConfig();
		$this->LoadConfig();
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

	private function LoadConfig(){
		$config_file = $this->path . '/config.json';
		if(file_exists($config_file)) $this->Config->LoadFromFile($config_file);
	}
    
    public function Controller(TiiController $val=null){
        return $this->GetOrSet(__FUNCTION__, $val);
    }
}
