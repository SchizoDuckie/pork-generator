<?
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title><?=$_TPL['title'];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? 
	if (!empty($_TPL['baseDir']))
	{
		echo ("<base href='{$_TPL['baseDir']}' ></base>\n");
	}
	if (!empty($_TPL['scripts']))
	{
		$_TPL['scripts'] = array_unique($_TPL['scripts']);
		for ($i=0; $i<sizeof($_TPL['scripts']);$i++)
		{
		echo('<script language="javascript" src="'.$_TPL['scripts'][$i].'"></script>'."\n");
		}
	}
	if (!empty($_TPL['dataLoader']))
	{
		echo("<script language='javascript' src='./includes/dataloader.js'></script>\n");
		$_TPL['onload'] .= 'document.dataLoader.setBaseDir("'.$_TPL['baseDir'].'");';
	}
	
?>
<? if(!empty($_TPL['styles'])) {
	foreach($_TPL['styles'] as $css)
	{
		echo("<link rel='stylesheet' type='text/css' href='{$css}'>");
	}
}?>
<link rel="stylesheet" href="<?=(empty($_TPL['style'])) ? './includes/style.css' : $_TPL['style'];?>" media="screen">
<link rel="stylesheet" href="./includes/print.css" media="print">
<?=((!empty($_TPL['script'])) ? '<script language="javascript">'."\n".$_TPL['script']."\n</script>" : '');?>
<?
if (is_array($_TPL['error']))
{
	for ($i = 0; $i< sizeof($_TPL['error']); $i++)
	{
		if (is_array($_TPL['error'][$i][0]) || is_object($_TPL['error'][$i][0]))
		{
			$_TPL['error'][$i][1] = printarr($_TPL['error'][$i][1]);
		}
		$_TPL['error'][$i][1] = str_replace("\r", "", str_replace("\n", '\n', $_TPL['error'][$i][1]));
		$_TPL['error'][$i][1] = str_replace("'", "\'", $_TPL['error'][$i][1]);
		echo( "<script>console.group('{$_TPL['error'][$i][0]} On Line: {$_TPL['error'][$i][3]} of {$_TPL['error'][$i][2]}'); console.info( '{$_TPL['error'][$i][1]}');console.groupEnd();</script>");
	}
}
if (!empty($_TPL['notification']))
{
	$_TPL['onload'] = 'alert("'.$_TPL['notification'].'");';
}
?>
</head>
<body<?=( (!empty($_TPL['onload'])) ? " onload='".$_TPL['onload']."'" : '');?>>
<div id="wrap">
	<div id="header">
		<div id="title">P.O.R.K.<p>Generated backend</p></div>
	</div>
	<div id='menu'>
	<ul>
	<?
		foreach ($_TPL['menu'] as $item=>$array)
		{
			echo ("<li><strong>{$item}</strong>\n<ul>");
			for($i=0; $i<sizeof($array); $i++)
			{	
				echo ("<li><a href='./{$array[$i][1]}/{$array[$i][2]}'>{$array[$i][0]}</a></li>\n");	
			}
			echo ("</ul></li>");
		}
	?>
	</ul>
	</div>
	<div id="content">
		<div class="body">
			<?=$_TPL['body'];?>
		</div>
	</div>
	<div id="footer">
		<em></em>
	</div>
</div>
</div>
<? $_TPL['timer']->stop(); echo $_TPL['timer']->getTime(); ?>
</body>
</html>