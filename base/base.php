<?php
class TiiBase {
	protected $path;

	public function __construct(){
	}

	protected function GetCreatorsPath($level=1){
		$trace = debug_backtrace();
		$arr = explode('/',dirname($trace[$level]['file']));
		$arr = array_reverse($arr);
		return array_shift($arr);
	}

}
