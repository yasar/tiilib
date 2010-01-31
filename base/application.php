<?php 
class TiiApplication extends TiiCore {
    // holds the application name
	//private $name;

	// holds the application path
	//private $path;

    /**
     * @var TiiTemplate
     */
    private $Template;

	/**
	 * @var string
	 */
    protected $template_file;

    public function __construct() {
    	//prevent the following variables to be set from outside the class; 
		//$this->_var_permissions['deny_set']=array('Template');
    	
        //$this->name = pathinfo($this->GetCreatorsPath(), PATHINFO_BASENAME);
		

        // load the application config file
        //echo $this->Path();exit;
        Tii::LoadConfig($this->Path().'/config.json');

		$this->template_file = Tii::Config($this->Name() . '/template');
    }
    /*
    public function __set($var, $val){
    	parent::__set($var, $val);
    }
	*/
	
	/**
	 * @return TiiApplication|String
	 */ 	 		
    public function Name($val = null) {
    	return $this->GetOrSet(__FUNCTION__, $val, pathinfo($this->Path(), PATHINFO_BASENAME));
    }

	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return TiiApplication|String
	 */
	public function Path($val = null){
		return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath(2));
	}

	/**
	 *
	 * @return TiiApplication
	 */
    public function Init() {
        // check if the template_file is defined
        if ( empty($this->template_file))
            throw new Exception('Template file is not defined.');

        //create the template object;
        $this->Template = new TiiTemplate();

		//pass the template file into Template object
        $this->Template->SetTemplate($this->template_file);

        // get the default_method from application configuration
        $default_method = Tii::Config($this->Name() . '/default_method');

        // check if default_method is empty, then set the default
        if ( empty($default_method))
            $default_method = 'main';
		
        // check if default_method is created
        if (!method_exists($this, $default_method))
            throw new Exception(Tii::Out('Default method "%s()" is not created.', $default_method));

        // call the default method
        $this->{$default_method}();

		return $this;
    }

	/**
	 *
	 * @return TiiTemplate
	 */
	public function Template(){return $this->Template;}

}
