<?php


class Graphviz
{
	var $params, $outFile, $tempFile, $graphvizPath;
	function __construct()
	{
		$settings = new Settings('settings/graphvizsettings.php');
		$this->graphvizPath = $settings->graphvizpath;
		$this->tempFile = $settings->tempfile;
		$this->outFile = $settings->outputfile;
		$this->params = "-T{$settings->outputtype} -o";
	
	}

	function generate($input) 
	{
		file_put_contents($this->tempFile, $input);
		exec($this->graphvizPath." {$this->params} {$this->outFile} {$this->tempFile}");
		return;
	}

}


?>