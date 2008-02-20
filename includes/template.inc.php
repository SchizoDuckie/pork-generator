<?php
/**
* Dit is de template. Eigenlijk de standaard layout voor deze website, met op een aantal plekken PHP code
* De code zorgt ervoor dat variabelen uit de $_TPL tussen de html terecht komen. Dit werkt op de meeste plekken door
* <?=$_TPL['variabele']? > tussen de HTML toe te voegen. Naar mijn mening is dit nog altijd sneller dan de snelste template class, en zorgt het voor minder overhead. (okee, het ziet er wat nasty'er uit, maar als het eenmaal goed is hoef je bijna niets meer te veranderen)
* Er kunnen nog verschillende andere variabelen dan de hieronder genoemde in de template staan, dit is specifiek per site / design.
* - string $_TPL['title'] Template Titel 
* - array $_TPL['error'] Array met foutmeldingen tijdens script execution
* - array $_TPL['scripts'] Array met te includen Javascripts
* - string $_TPL['script'] String met Javascript
* - string $_TPL['onload'] string met onload Javascript
* - string $_TPL['body'] string met body HTML
*
*/
global $_TPL;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?=$_TPL['title'];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15"/>
<? 
	if (!empty($_TPL['baseDir']))
	{
		echo ("<base href='{$_TPL['baseDir']}'></base>\n");
	}
	if (!empty($_TPL['scripts']))
	{
		for ($i=0; $i<sizeof($_TPL['scripts']);$i++)
		{
		echo('<script language="javascript" src="'.$_TPL['scripts'][$i].'"></script>'."\n");
		}
	}	
?>
<link rel="stylesheet" href="<?=(empty($_TPL['style'])) ? './includes/style.css' : $_TPL['style'];?>" media="screen"/>
<link rel="stylesheet" href='./includes/mocha.css' media="screen"/>
<link rel="stylesheet" href="./includes/print.css" media="print"/>
<link rel="stylesheet" href="./includes/simpletabs.css" media="screen"/>
<?=((!empty($_TPL['script'])) ? '<script language="javascript">'."\n".$_TPL['script']."\n</script>" : '');?>
<?php
if (is_array($_TPL['error']))
{
	echo ('<script language="javascript">
	var windowHTML = " ";
	ErrorWindow = window.open("","ErrorWindow","width=500,height=480,scrollbars=1,resizable=yes");
	ErrorWindow.document.write("<html><title>Foutmeldingen</title><body style=\'background:#f0f0f0; font:11px verdana\'></body></html>");
	ErrorWindow.document.body.innerHTML = "";');
	for ($i = 0; $i< sizeof($_TPL['error']); $i++)
	{
		if (is_array($_TPL['error'][$i][0]))
		{
			$_TPL['error'][$i][0] = printarray($_TPL['error'][$i][0]);
		}
		echo( "\n".'windowHTML += "<div style=\'padding:3px; border-bottom:1px solid black\'><b style=\'color:red\'>Error: </b> '.$_TPL['error'][$i][0]."<br><b>Error Msg:</b> ".$_TPL['error'][$i][1].'<br><b>On Line:</b> '.$_TPL['error'][$i][3].' of '.$_TPL['error'][$i][2].'</div>";');
	}

	echo("
	ErrorWindow.document.body.innerHTML += windowHTML;
	</script>");
}
if (!empty($_TPL['notification']))
{
	$_TPL['onload'] = 'alert("'.$_TPL['notification'].'");';
}
?>
</head>
<body<?=( (!empty($_TPL['onload'])) ? " onload='".$_TPL['onload']."'" : '');?>>
<div id="mochaDesktop">
<div id="mochaDesktopHeader">

<div id="mochaDesktopNavbar" class="windowMenu">
<?php

if(!empty($_TPL['menu']))
{
	echo("<ul>");
	foreach($_TPL['menu'] as $item=>$options)
	{
		echo("<li><a href='#' onclick='return false;'>{$item}</a>");
		if(!empty($options))
		{
			echo("<ul class='{$item}'>");
			foreach($options as $key=>$item)
			{
				echo("<li><a class='mochiLink' href='{$item[0]}'>{$item[1]}</a></li>");
			}
			echo("</ul>");
		}
		echo("</li>");

	}
	echo("</ul>");
}
?>
</div>

	

<div id="title">PHP on Rails <span style='font-size: 14px'>(Kinda)</span></div>
<div id="mochaDock">
	<div id="mochaDockPlacement"></div>
	<div id="mochaDockAutoHide"></div>
</div>
<div id="header">
	
</div><!-- mochaDesktop end -->


</body>
</html>