<?php
class TiiModuleController_Account extends TiiController{
    /**
     * @var TiiModule_Account
     */
    private $Module;
    
	public function __construct(){
		parent::__construct();
        Tii::Import('modules/account/module.php');
        $this->Module = new TiiModule_Account();
        $this->Module->Controller(& $this);
        //var_dump($this->Module->Controller());
	}

	public function LoginControl($params=null){
		return $this->Module->GetLoginForm(isset($params['login_form'])?$params['login_form']:null);
	}

}