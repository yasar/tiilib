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
    
	protected function GetCreatorsPath($level=1){
		$trace = debug_backtrace();
		return dirname($trace[$level]['file']);
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
