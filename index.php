<?php
global $_TPL, $analyzer, $_URI; 
set_time_limit(0);
error_reporting(E_ALL);

include('./includes/functions.php');

# standaard functies includen.

$_TPL['baseDir'] = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER["SCRIPT_NAME"]).'/';


$_TPL['title'] = 'P.O.R.K. - PHP On Rails (kinda)';
$_TPL['scripts'][] = './includes/mootools.js';
$_TPL['scripts'][] = './includes/mootools.ext.js';
$_TPL['scripts'][] = './includes/pork.iframe.js';
$_TPL['scripts'][] = './includes/porkfunctions.js';
$_TPL['scripts'][] = './includes/mocha.js';
$_TPL['scripts'][] = './includes/simpletabs.js';

$_URI = explode('/', str_replace(strtolower($_TPL['baseDir']), '', urldecode(strtolower('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))));

# laad alle plugins


if(!dbConnection::getInstance()->connect())
{
	$_TPL['error'] = false;
	$_TPL['script'] .= "window.addEvent('load', function() {
		var windowtje = document.mochaDesktop.newWindow({
					id: 'cantconnect',
					title: 'A configuration error has occured',
					loadMethod: 'xhr',
					contentURL: './connect.html',
					width: 400,
					height: 300
				});
		document.mochaDesktop.centerWindow(windowtje);


	});";
	die(include('./includes/template.inc.php'));
}


LoadPlugins('./plugins/');

$_TPL['script'] .= "window.addEvent('load', function() {
	var windowtje = document.mochaDesktop.newWindow({
				id: 'welcome',
				title: 'Welcome',
				loadMethod: 'xhr',
				contentURL: './welcome.html',
				width: 400,
				height: 300
			});
	document.mochaDesktop.centerWindow(windowtje);
});";

# template includen.

include('./includes/template.inc.php');

?>