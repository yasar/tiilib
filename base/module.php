<?php

class TiiModule extends TiiCore{
	/**
	 * @var TiiConfig
	 */
	protected $Config;



	public function __construct(){
		parent::__contruct();

		$this->path = $this->GetCreatorsPath();

		$this->config = new TiiConfig();
		$this->LoadConfig();
	}

	private function LoadConfig(){
		$config_file = $this->path . '/config.json';
		if(file_exists($config_file)) $this->Config->LoadFromFile($config_file);
	}
}
