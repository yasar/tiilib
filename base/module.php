<?php

class TiiModule extends TiiCore{
	/**
	 * @var TiiConfig
	 */
	protected $Config;

	protected $Template;

	protected $path;

	public function __construct(){
		$this->config = new TiiConfig();
		$this->LoadConfig();

		$this->Template = new TiiTemplate();

		$this->path = $this->GetCreatorsPath();
	}

	private function LoadConfig(){
		$config_file = $this->GetCreatorsPath() . '/config.json';
		if(file_exists($config_file)) $this->Config->LoadFromFile($config_file);
	}

	protected function SetTemplate($template_file){
		$this->Template->SetTemplate($template_file);
		return $this->Template;
	}

}
