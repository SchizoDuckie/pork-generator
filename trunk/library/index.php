<?php
global $_TPL, $_URI; 

error_reporting(E_ALL);

include('./includes/functions.php');
$_TPL['timer'] = new Timer();
$_TPL['timer']->start();

$_TPL['baseDir'] = 'http://'.$_SERVER['HTTP_HOST']. dirname($_SERVER["SCRIPT_NAME"]).'/';

$_TPL['title'] = 'P.O.R.K. generated backend';
$_TPL['js'] = new jsObject();

$_TPL['styles'][] = './includes/calendar.css';
$_TPL['scripts'][] = './includes/mootools.js';
$_TPL['scripts'][] = './includes/mootools.ext.js';
$_TPL['scripts'][] = './includes/pork.lightbox.js';
$_TPL['scripts'][] = './includes/pork.iframe.js';
$_TPL['scripts'][] = './includes/pork.growl.js';
$_TPL['scripts'][] = './includes/calendar.js';
$_TPL['scripts'][] = './includes/porkfunctions.js';
$_TPL['scripts'][] = './includes/pork.validator.js';
$_TPL['scripts'][] = './includes/fckeditor/fckeditor.js';

$_URI = explode('/', str_replace(strtolower($_TPL['baseDir']), '', urldecode(strtolower('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))));

# laad alle plugins

LoadPlugins('./plugins/');

# template includen.

include('./includes/template.inc.php');

?>