<?

/*	
 * standard template class. 
 * construct it, set some properties, load a template and all {value} text will be replaced by it's value.
 * @package pork
 */


class templateengine
{
	var $template, $templateValues, $availableValues;
	function __construct($template)
	{	
		$this->availableValues = array();
		$this->templateValues = array();
		$this->template = $template;

	}

	function __set($key, $val)
	{
		$this->availableValues[$key] = $val;
	}

	function __get($key)
	{
		if (array_key_exists($key, $this->availableValues))
		{
			return ($this->availagbleValues[$key]);
		}
		return false;
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
		preg_match_all('!\{(.*)\}!Uis', $input, $output);
		return($output);
	}

	function feedValue($key, $val)
	{
		$this->availableValues[$key] = $val;
	}


	function run()
	{
		$tpl = file_get_contents($this->template);
		$this->templateValues = $this->grabValues($tpl);
		for ($i=0; $i<sizeof($this->templateValues[1]); $i++)
		{
			$tpl = str_replace($this->templateValues[0][$i], stripslashes($this->availableValues[$this->templateValues[1][$i]]), $tpl);
		}
		return ($tpl);	

	}






}