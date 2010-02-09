<?php
abstract class TiiBase {
    private $id;
    
	

	public function __construct(){
	   $this->id = rand(10000,99999); //md5(microtime(true));
	}
    
    
    
    final public function ID(){
        return $this->id;
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
			isset($array[$var])
			&& $this->{$var} = $array[$var];
		}
        return $this;
	}
}
