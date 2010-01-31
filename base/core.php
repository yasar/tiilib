<?php
/**
 * @classDescription		The core class for the framework
 */
class TiiCore extends TiiBase{
	/**
	 * Holds all the class member assignments..
	 * 
	 * @var Array
	 */
	private $variables=array();

	/**
	 * Holds the class variables list with their permissions
	 * @var Array
	 */
	//protected $_var_permissions=array();

	public function __construct(){
		parent::__construct();
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
		//error_log('__set');
		//if(in_array($var, $this->_var_permissions['deny_set'])) return;
		//if (in_array($var, get_class_vars())) $this->{$var} = $val;
		//else 
		$this->variables[$var] = $val;
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
	
	/**
	 * LoadFromArray()
	 * 
	 * Will load the class properties from an associative array
	 * 
	 * @param object $array
	 * @return {TCore}
	 */
	protected function LoadFromArray(Array $array){
		$vars = array_keys(get_class_vars(get_class($this)));
		foreach($vars as $var){
			isset($arr[$var])
			&& $this->{$var} = $arr[$var];
		}
        return $this;
	}
}
