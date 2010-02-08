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
	}

	public function LoginControl($params=null){
        switch(TiiR::P('action')){
            case 'login':
                if(false === $this->Module->IsAccountExist(TiiR::P('email',''))){
                    echo 1111;
                    $this->Errors[] = new TiiError('Account does not exist');
                }
                if($this->Errors->count() > 0){
                    echo $this->Errors->GetMessages();
                }
                break;
                
            default:
                return $this->Module->GetLoginForm(isset($params['login_form'])?$params['login_form']:null);
        }
	}

}