<?php

class TiiDOM implements TiiDomDriver{
	/**
	*  Holds the Engine object
	* 
	 * @var TiiDomDriver
	 */
	private $E;
	
	/**
	* put your comment there...
	* 
	* @var string
	*/
	public $engine;
    
    public function __construct($args='', $engine='Tii'){
		//$this->LoadFromArray(Tii::Config('database'));
		is_null($engine) || $this->engine = $engine;
		Tii::Import('base/interfaces.php');
		Tii::Import('base/drivers/dom.'.strtolower($this->engine).'.php');
		
		$_engine = new ReflectionClass('TiiDomDriver_'.$this->engine);
		$this->E = $_engine->newInstance($args);
		//$this->E->_Parent(&$this)->_Initialize();
    }
    
    public function LoadHTML($html){
        $this->E->LoadHTML($this);
        return $this;
    }
    
    public function LoadFile($filename){
        $this->E->LoadFile($filename);
        return $this;
    }

	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return String
	 */
	//public function Path($val = null){
	//	return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath());
	//}
    
    public function FindNode($path, $idx=0){
        return $this->E->FindNode($path, $idx);
    }
    
    public function InnerHTML($html=null){
        return $this->E->InnerHTML($html);
    }
    
    public function Append($html){
        return $this->E->Append($html);
    }
    
    public function Callback($func=null){
        
    }
    
    //public function Find()
}