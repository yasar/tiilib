<?php
class TiiBase {

	protected function GetCreatorsPath(){
		$trace = debug_backtrace();
		return array_shift(array_reverse(explode('/',dirname($trace[1]['file']))));
	}

}
