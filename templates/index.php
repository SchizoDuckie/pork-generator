<?php
global $_TPL, $_URI; 

error_reporting(E_ALL);

include('./includes/functions.php');
$_TPL['timer'] = new Timer();
$_TPL['timer']->start();
$_TPL['baseDir'] = 'http://'.$_SERVER['HTTP_HOST'].'/@basedir@/';

$_TPL['title'] = '@title';
$_TPL['js'] = new jsObject();

$_TPL['scripts'][] = './includes/mootools.js';
$_TPL['scripts'][] = './includes/mootools.ext.js';
$_TPL['scripts'][] = './includes/pork.lightbox.js';
$_TPL['scripts'][] = './includes/pork.iframe.js';
$_TPL['scripts'][] = './includes/pork.calendar.js';
$_TPL['scripts'][] = './includes/porkfunctions.js';
$_TPL['scripts'][] = './includes/pork.validator.js';
$_TPL['scripts'][] = './includes/fckeditor/fckeditor.js';


$_URI = explode('/', str_replace(strtolower($_TPL['baseDir']), '', urldecode(strtolower('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))));


LoadPlugins('./plugins/');



include('./includes/template.inc.php');

?>