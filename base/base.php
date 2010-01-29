<?php
class TiiBase {
	protected $path;

	public function __construct(){
	}

	protected function GetCreatorsPath(){
		$trace = debug_backtrace();
		$arr = explode('/',dirname($trace[1]['file']));
		$arr = array_reverse($arr);
		return array_shift($arr);
	}

}
