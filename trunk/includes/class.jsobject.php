<?php

class jsObject
{
	var $xml, $properties;

	function __construct()
	{
		$this->properties = array();
	}

	function __set($key, $value)
	{
		$this->properties[$key] = $value;
	}

	function __get($key)
	{
		if(array_key_exists($key,$this->properties))
		{
			return ($this->properties[$key]);
		}		
	}

	function display()
	{
		foreach ($this->properties as $key=>$val)
		{
			$val = addslashes($val);
			$val = str_replace("\n", "", $val);
			$val = str_replace("\r", "", $val);
			$val = str_replace("\r", "", $val);
			$output .= ($output != '') ? ', ' : '';
			$output .= (substr($val, 0, 1) != '[') ? "$key: '".xhtmlEntities($val)."'" : "$key: ".xhtmlEntities($val);
		}
		die("{{$output}}");
	
	}
}
?>