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

        $this->path = $this->GetCreatorsPath();

        $this->Template = new TiiTemplate();
    }

	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return TiiApplication|String
	 */
	public function Path($val = null){
		return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath());
	}
	
    protected function SetTemplate($template_file, $absolute_path = false)
    {
        $this->Template->SetTemplate($template_file, $absolute_path);
        return $this->Template;
    }
}
