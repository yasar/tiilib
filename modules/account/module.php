<?php
class TiiModule_Account extends TiiModule{
	public function __construct(){
		parent::__construct();
	}

}

class TiiModule_Controller_Account extends TiiController{

	public function __construct(){
		parent::__construct();
	}

	public function LoginControl(){
		return $this->SetTemplate($this->path.'/templ/login.html', true)->GetHtml();
	}

}