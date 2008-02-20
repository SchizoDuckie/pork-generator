<?php

global $_URI;




$_TPL['menu']['Databases'] = databaseAnalyzer::displayDatabases();
$_TPL['menu']['Cache'][]  = array('./clearcache/', 'Clear all cached data');
	

if ($_URI[0] == 'clearcache')
{
	$_SESSION = array();
	echo ('<h2>Data cleared</h2><p>All stored session data was wiped</p>');
	die();
}

?>