<?php

global $_TPL, $_URI;


switch($_URI[0]) 
{
	case 'graph':
		$analyzer = databaseAnalyzer::getInstance($_URI[1]);
		$analyzer->displayGraphViz();

		die("<h3>Graph generated for database {$_URI[1]}:</h3><img src='{$_TPL['baseDir']}relations.gif?".time()."'>");
	break;
}