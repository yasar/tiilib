<?php

/**
 * TiiController
 * 
 * @package tiilib
 * @author yasar@live.ca
 * @copyright 2010
 * @version $Id$
 * @access public
 */
class TiiController extends TiiCore
{
    /**
     * @var TiiTemplate
     */
    protected $Template;
    
    
    /**
    * @var TiiErrors
    */
    protected $Errors;
    
    public function __construct()
    {
        parent::__construct();

        $this->Template = new TiiTemplate();
        
        $this->Errors = new TiiErrors();
    }

	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return String
	 */
	public function Path($val = null){
		return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath());
	}
	
    public function Template($template_file=null, $absolute_path = false)
    {
        if(! is_null($template_file)) $this->Template->SetTemplate($template_file, $absolute_path);
        return $this->Template;
    }
    
    /**
    * put your comment there...
    * 
    * @param TiiError $error
    * @returns TiiController
    */
    /*
    protected function AddError(TiiError $error){
        $this->errors[]=$error;
        return $this;
    }
    
    protected function HasError(){
        return count($this->errors) > 0;
    }
    
    /**
    * Clear the stored errors previously generated
    * 
    * @returns TiiController
    */
    /*
    protected function ClearErrors(){
        $this->errors = array();
        return $this;
    } 
    */
}
