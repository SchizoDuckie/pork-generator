<?php


class templateengine
{
	var $template, $templateValues, $availableValues;
	function __construct($template='')
	{	
		$this->template = $template;
		$this->availableValues = array();
		$this->templateValues = array();

	}
	
	function __set($key, $value)
	{
		$this->availableValues[$key] = $value;
	}

	function feedValues($input)
	{
		foreach ($input as $key=>$val)
		{
			$this->availableValues[$key] = $val;
		}
	}

	function loadTemplate($template)
	{
		$this->template = $template;
	}

	function grabValues($input)
	{
		preg_match_all('!\@(.*)\@!U', $input, $output);
		return($output);
	}

	function run()
	{
		foreach($this->availableValues as $key=>$val)
		{
			if(is_array($val)) $this->availableValues[$key] = '';
		}
		$tpl = file_get_contents($this->template);
		$this->templateValues = $this->grabValues($tpl);
		for ($i=0; $i<sizeof($this->templateValues[1]); $i++)
		{
			$tpl = str_replace($this->templateValues[0][$i], stripslashes($this->availableValues[$this->templateValues[1][$i]]), $tpl);
		}

		return ($tpl);	

	}

}