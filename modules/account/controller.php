<?php
class TiiModuleController_Account extends TiiController{
    /**
     * @var TiiModule_Account
     */
    private $Module;
    
    /**
    * put your comment there...
    * 
    * @var TiiModel_Account
    */
    private $Model;
    
	public function __construct(){
		parent::__construct();
        Tii::Import('modules/account/module.php');
        $this->Module = new TiiModule_Account();
        $this->Module->Controller(& $this);
	}

	public function LoginControl($params=null){
        switch(TiiR::P('action')){
            case 'login':
                if(false === $this->Module->IsAccountExist(TiiR::P('email'))){
                    $this->Errors[] = new TiiError('Account does not exist');
                }
                else {
					 $account = $this->Module->Authenticate(TiiR::P('email'),TiiR::P('password'));
					 if ($account === false){
						 $this->Errors[] = new TiiError('Email address or password is invalid');
					 }else{
					 	 $this->Model = new TiiModel_Account();
						 Tii::Session(TII_SK_ACTIVE_ACCOUNT, $this->Model->LoadFromArray($account));
					 }
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