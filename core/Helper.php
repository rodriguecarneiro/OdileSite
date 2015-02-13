<?php

namespace Core;

class Helper 
{
	public function __call($name, $params){
		include_once HELPERS. $name . '.php';
		return call_user_func_array($name, $params);  
	}
}