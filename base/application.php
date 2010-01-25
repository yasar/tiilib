<?php 
class TiiApplication extends TiiCore {
    // holds the application name
	private $_name;
	
	// holds the application path
	private $_path;
    
    /**
     * @var TiiTemplate
     */
    private $_Template;
    
    protected $template_file;
    
    public function __construct() {
    	//ob_start();
        $this->_name = pathinfo($this->GetCreatorsPath(), PATHINFO_BASENAME);
		$this->_path = $this->GetCreatorsPath();
		
        // load the application config file
        Tii::LoadConfig($this->_path.'/config.json');
        
		$this->template_file = Tii::Config($this->Name() . '/template');
    }
	
	public function __destruct(){
//		$buffer = ob_get_clean();
//		
//		$this->_Template->SetContent('page_content', $buffer);
	}
    
    public function Name($str = null) {
        if (!is_null($str))
            $this->_name = $str;
        return $this->_name;
    }
	
	/**
	 * Returns the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 * 
	 * @return string
	 */
	public function Path(){return $this->_path;}
    
	/**
	 * 
	 * @return TiiApplication
	 */
    public function Application() {
        //create the template object;
        $this->_Template = new TiiTemplate();
        
        // check if the template_file is defined
        if ( empty($this->template_file))
            throw new Exception('Template file is not defined.');
        
		//pass the template file into Template object
        $this->_Template->SetTemplate($this->template_file);
        
        // get the default_method from application configuration
        $default_method = Tii::Config($this->_name . '/default_method');
        
        // check if default_method is empty, then set the default
        if ( empty($default_method))
            $default_method = 'main';
            
        // check if default_method is created
        if (!method_exists($this, $default_method))
            throw new Exception(Tii::Out('Default method "%s()" is not created.', $default_method));
            
        // call the default method
        $this-> {
            $default_method
        } ();
		
		return $this;
    }
	
	/**
	 * 
	 * @return TiiTemplate
	 */
	protected function Template(){return $this->_Template;}
	
	/**
	 * 
	 * @return string
	 */
	public function GetHtml(){
		return $this->Template()->GetHTML();
	}
    
}
