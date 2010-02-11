<?php

function tii_getor(&$var, $default){
	if (isset($var) && ! empty($var)) return $var;
	return $default;
}

function tii_setnot(&$var, $default){
	! isset($var) && $var = $default;
	return $var;
}