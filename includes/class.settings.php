<?php

	class Settings
	{
		private $settings =array();
		private $filename = false;
		function __construct($filename)
		{
			$this->filename = $filename;
			$this->readSettings();
		}

		function __get($property) 
		{
			return($this->settings[$property]);
		}
	
		function readSettings()
		{
			$input = file_get_contents($this->filename);
			$file = explode("\n", $input );
			for ($i=2; $i<sizeof($file) -1; $i++)
			{
				$property = explode ("=", $file[$i]);
				$prop = trim($property[0]);
				$this->settings[$prop] = trim($property[1]);
			}
		}


}

?>