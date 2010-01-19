<?php
class TArray{
	
	/**
	 * Extend() - will take arrays as parameter, and will merge them
	 * 
	 * @return {Array} Extended array
	 */
	public static function Extend(){
		$args = func_get_args();
		$extended = array();
		if(is_array($args) && count($args)) {
			foreach($args as $array) {
				if(is_array($array)) {
					$extended = array_merge($extended, $array);
				}
			}
		}
		return $extended;
	}
}
