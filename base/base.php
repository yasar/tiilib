<?php
class TiiBase {
    private $id;
    
	protected $path;

	public function __construct(){
	   $this->id = rand(10000,99999); //md5(microtime(true));
	}
    
    final public function ID(){
        return $this->id;
    }

	protected function GetCreatorsPath($level=1){
		$trace = debug_backtrace();
		$arr = explode('/',dirname($trace[$level]['file']));
		$arr = array_reverse($arr);
		return array_shift($arr);
	}

}
