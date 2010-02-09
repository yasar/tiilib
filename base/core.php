<?php
/**
 * @classDescription		The core class for the framework
 */
abstract class TiiCore extends TiiBase{
	/**
	 * Holds all the class member assignments..
	 * 
	 * @var Array
	 */
	private $variables=array();

    //protected $path;
    abstract public function Path();
    
	public function __construct(){
		parent::__construct();
	}

	protected function GetCreatorsPath($level=1){
		$trace = debug_backtrace();
		$arr = explode('/',dirname($trace[$level]['file']));
		$arr = array_reverse($arr);
		return array_shift($arr);
	}
    
	/**
	 * Magic function, Setter()
	 * 
	 * Will set the variable as an associated key in the variables array 
	 * 
	 * @param object $var
	 * @param object $val
	 * @return 
	 */
	public function __set($var, $val){
        if(in_array($var, get_class_vars(__CLASS__))) $this->{$var} = $val;
		else $this->variables[$var] = $val;
	}

	/**
	 * Magic function, Getter()
	 * 
	 * Will check if the requested variable is set in the variables array,
	 * if set, will return it,
	 * otherwise will return NULL
	 * 
	 * @param object $var
	 * @return 
	 */
	public function __get($var){
		return
			isset($this->{$var})
			? $this->variables[$var]
			: null;
		//return in_array($var, get_class_vars(__CLASS__)) ? $this->{$var} : isset($this->variables[$var]) ? $this->variables[$var] : null;
	}
	
	/**
	 * Magic function, IsSet()
	 * 
	 * Will look for the variable as a key in the associative array.
	 * If found, will return TRUE
	 * otherwise will return FALSE
	 *  
	 * @param object $var
	 * @return 
	 */
	public function __isset($var){
		return key_exists($var, $this->variables);
	}
	
	/**
	 * GetOrSet()
	 * Will look for the parameters.
	 * If value is provided then will set the variable and the return the object itself
	 * otherwise will return the variable 
	 * Typical usage is in the extending class to define a method to set/get a property
	 * USAGE:
	 * public function SqlQuery($val=null){return $this->GetOrSet(__FUNCTION__,$val);}
	 * 
	 * @param string $var Method name to set/get the property
	 * @param string optional $val the value to be set for the property
	 * @param string optional $default the value to be set for the property when get is invoked but property has never been set before
	 * @return TCore
	 */
	protected function GetOrSet($var,$val=null,$default=null){
		if (is_null($val)) {
			if(! is_null($default) && ! isset($this->{$var})) $this->{$var} = $default;
			return $this->{$var};	
		}
		$this->{$var} = $val;
		return $this;

	}
	
}
