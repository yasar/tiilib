<?php

class Tii_Mdl_Login extends TiiModule{
	public function __construct(){
		parent::__construct();
	}

	public function LoginControl(){
		return $this->Template->SetTemplate($this->path.'/templ/login.html')->GetHtml();
	}
}
