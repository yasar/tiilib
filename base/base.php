<?php
abstract class TiiBase {
    private $id;
    
	

	public function __construct(){
	   $this->id = rand(10000,99999); //md5(microtime(true));
	}
    
    
    
    final public function ID(){
        return $this->id;
    }

}
