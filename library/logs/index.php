<? 
if (array_key_exists('action', $_GET) && $_GET['action'] == 'clear')
{
	@unlink('./errors.html');
	header('location: index.php');
}?>
<html>
	<head>
		<title>Groeiservice script errors</title>
		<style>
			*
			{
				font-family: verdana;
				font-size: 12px;
			}
			.scripterror
			{
				border: 1px solid black;
				background-color: #EFEFEF;
				padding: 5px;
				margin: 5px;
			}
			DIV.expandable PRE
			{
				display: none;
				font-weight: normal;
				font-size: 11px;
				font-family: courier;
				background-color: white;
				border: 1px inset black;
			}


			.errormsg
			{
				color: red;
				font-weight: bold;
			}
		</style>
		<script>
				function addExpanders()
				{	
					var divList = document.getElementsByTagName("div");
					for (var i in divList)
					{
						divList[i].onclick=function(){this.lastChild.style.display= (this.lastChild.style.display == '' || this.lastChild.style.display=='undefined') ? 'block' : '';};
					}

				}
		</script>	
	</head>
<body onload="addExpanders()">
<a href='index.php?action=clear'>Logfile opschonen</a><br>
<? if (file_exists("./errors.html"))
{
	echo file_get_contents("./errors.html"); 
}
else
{
	echo("(nog) Geen errors! \o/");
}
?>
</body>
</html>