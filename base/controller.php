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

    public function __construct()
    {
        parent::__construct();

        $this->Template = new TiiTemplate();
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
}
