<?php
class TiiModule_Account extends TiiModule{
	public function __construct(){
		parent::__construct();
	}

	public function GetLoginForm($params = null){
        $default = array(
            'holder'            => array(
                'id'            =>'LoginForm_Holder'
            ),
            'buttons_holder'    => array(
                'id'            => 'Buttons_Holder'
            ),
            'buttons'           => array(
                'submit'        => array(
                    'tag'       => 'button',
                    'type'      => 'submit',
                    'innertext' => 'Let me in!'
                )
            ),
            'fields'            => array(
                'login'         => array(
                    'tag'   => 'input',
                    'type'  => 'text',
                    'id'    => 'email',
                    'name'  => 'email',
                    'label' => 'Email'
                ),
                'password'  => array(
                    'tag'   => 'input',
                    'type'  => 'password',
                    'id'    => 'password',
                    'name'  => 'password',
                    'label' => 'Password'
                )
            )
        );
        
        
		if(! is_null($params) && ! empty($params) && is_array($params) ) {
            Tii::Import('helper/array.php');
            $params = TiiA::ExtendDeeper($default, $params);
        }else $params =& $default;
        
        Tii::App()->Template()->AddScript($this->Path().'/templ/login.js');
        Tii::App()->Template()->AddStyle($this->Path().'/templ/login.css');
        
		return $this->Controller()
            ->Template($this->path.'/templ/login.html', $absolute_path=true)
            ->ProcessModuleTemplate($params)
            ->GetHTML()
            ;
	}

    public function IsAccountExist($email){
        $sql="select count(*) as total from account where email='$email'";
        $row=$this->DB->Query($sql,true);
        return intval($row['total']) > 0;
    }

    public function Authenticate($email,$password){
		$row=$this->DB->Query("select * from account where email='$email' and password='$password'");
		if(empty($row)) return false;
		return $row;
    }
}

