<?php
class TiiModuleController_Login extends TiiController{
    /**
     * @var TiiModule_Account
     */
    private $Module;
    
    /**
    * put your comment there...
    * 
    * @var TiiModel_Account
    */
    private $Account;
    
    
	public function __construct($params){
		parent::__construct($params);
        Tii::Import('modules/account/module.php');
        Tii::Import('modules/account/models/account.php');
        
        $this->Account = new TiiModel_Account();
        $this->Module = new TiiModule_Account();
        $this->Module->Controller(& $this);
        $this->Template->Controller(& $this);
	}
	
	public function Main($params=null){
		switch(TiiHlpRequest::Post('action')){
			case 'login':
				if ($this->Login()) return true;
				Tii::App()->Template()->Errors->Import($this->Errors);
				break;
		}
		
		return $this->GetLoginForm();
	}
	
	public function GetLoginForm(){
		//return $this->Module->GetLoginForm(isset($params['login_form'])?$params['login_form']:null);
		return $this->Module->GetLoginForm();
	}
	
	public function Login(){
        if(false === $this->Module->IsAccountExist(TiiR::P('email'))){
            $this->Errors[] = new TiiError('Account does not exist');
        }
        else {
			 $account = $this->Module->Authenticate(TiiR::P('email'),TiiR::P('password'));
			 if ($account === false){
				 $this->Errors[] = new TiiError('Email address or password is invalid');
			 }else{
				 $this->Account = new TiiModel_Account();
				 Tii::Session(TII_SK_ACTIVE_ACCOUNT, $this->Account->LoadFromArray($account));
			 }
        }
        if($this->Errors->count() > 0) return false;
        return true;
	}

}