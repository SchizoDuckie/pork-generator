<?
/**
 *  Function library. Holds the default functions and code that has to be executed every time.
 *	@package pork
 */

global $_TPL;

session_start();
setlocale (LC_TIME, "Dutch");
set_error_handler('errorPopup');
include('datefunctions.php');

if(!empty($_POST))
{
	if(get_magic_quotes_gpc() == true)
	{
		foreach($_POST as $key=>$val)
		{
			if(!is_array($val))
			{
				$_POST[$key] = stripslashes($val);
			}
		}
	}
}

/**
 * Als een functie niet bestaat, wordt er automagisch geprobeerd om  $classname.class.php te includen.
 * @param string $classname automagisch te includen classname
 */
function __autoload($className)
{
	$className = strtolower($className);
	$classFile = "./includes/class.{$className}.php";
	if (file_exists($classFile))
	{
		include($classFile);
	}
	else
	{
		die ("$classFile niet gevonden");
	}
}


/**
 * Custom PHP error handler. Plaatst een foutmelding in $_TPL['error'] zodat deze in de template uitgelezen kan worden.
 * Foutmeldingen worden in de meeste template.inc.php's getransformed naar een javascript Popup zodat de layout niet verneukt wordt.
 * @param int $errno foutmelding type
 * @param string $errstr foutmelding
 * @param string $errfile source file 
 * @param int $errline regel waar de foutmelding is
 */
 function errorPopup($errno, $errstr, $errfile, $errline)
{
	global $_TPL;
	$errorarray = Array(  1 => "Error", 2 => "Warning", 4 => "Parse Error", 8 => "Notice", 16 => "Core Error", 32 => "Core Warning", 64 => "Compile Error", 128 => "Compile Warning", 256 => "User Error", 512 => "User Warning", 1024 => "User Notice", 2048 => "Strict PHP");
	$errstr = str_replace("\r", "", str_replace("\n", "", nl2br($errstr)));
	if ($errno != 8 && $errno != 2048)
	{
		if (array_key_exists($errno, $errorarray)) 
		{ 
			$errno = $errorarray[$errno]; 
		}
		/*$fp = fopen('./logs/errors.html', 'a+'); 
	  fwrite($fp, "<div class='scripterror'>PHP generated error: ".date("d-n-Y H:m:s")."<br>"); 
	  fwrite($fp, "<span class='errormsg'>$errno} - {$errstr} in {$errfile} on {$errline}</span><br>");
      fwrite($fp, "url: {$_SERVER['SCRIPT_NAME']}?{$_SERVER['QUERY_STRING']}<br>"); 
      fwrite($fp, "referer: {$_SERVER['HTTP_REFERER']}<br>"); 
      if (!empty($_POST['cat'])) 
      {      
           $output = "<div class='expandable'>POSTed variables Available: (click here to expand)
		   <pre>";
           foreach ($_POST as $key=>$val) 
           { 
                $output .= htmlspecialchars("$key: $val\n", ENT_QUOTES); 
           } 
           $output .= "</pre></div>"; 
           fwrite($fp, $output); 
      } 
   //   fwrite($fp, "<div class='expandable'>Debug backtracing: (click here to expand)"); 
	//  $errorarray = debug_backtrace();
  //  fwrite($fp, print_array(array_reverse($errorarray))."</div></div>");
	 fwrite($fp, "</div>"); 
      fclose($fp); 
		*/
		$_TPL['error'][] = Array($errno, $errstr, $errfile, addslashes($errline));
	}        
}

/**
 * Custom PHP error handler. Plaatst een foutmelding in $_TPL['error'] zodat deze in de template uitgelezen kan worden.
 * Foutmeldingen worden in de meeste template.inc.php's getransformed naar een javascript Popup zodat de layout niet verneukt wordt.
 * @param string $input foutmelding
 */
function throw_error($input)
{
	global $_TPL;
	$errorarray = debug_backtrace(); 
   
	if (is_array($input) || is_object($input)) { $input = print_array($input); } 
	$_TPL['error'][] = Array("Thrown from script", $input, addslashes($errorarray[0]['file']), $errorarray[0]['line']);
}

function log_error($input) 
{

     $fp = fopen('./logs/errors.html', 'a+'); 
      fwrite($fp, "<div class='scripterror'>Script generated error: ".date("d-n-Y H:m:s")."<br>"); 
	  fwrite($fp, "<span class='errormsg'>{$input}</span><br>");
      fwrite($fp, "url: {$_SERVER['SCRIPT_NAME']}?{$_SERVER['QUERY_STRING']}<br>"); 
      fwrite($fp, "referer: {$_SERVER['HTTP_REFERER']}<br>"); 
      if (!empty($_POST['cat'])) 
      {      
           $output = "<div class='expandable'>POSTed variables Available: (click here to expand)
		   <pre>";
           foreach ($_POST as $key=>$val) 
           { 
                $output .= htmlspecialchars("{$key}: {$val}\n", ENT_QUOTES);
           } 
           $output = "</pre></div>"; 
           fwrite($fp, $output); 
      } 
	  fwrite($fp, "</div>");
	  fclose($fp); 
}


/**
 * Deze functie laadt de plugins uit $dirname in.
 * Leest de complete directory in, en als een bestand de '.php' extentie heeft wordt deze file ge-include.
 * Plugins werken met een switch() op $_GET of $_POST. Als een plugin de goede $_GET of $_POST param tegenkomt, wordt de
 * onderliggende code automagisch uitgevoerd.
 * @param string $dirname naam van de directory waar de plugins staan
*/
function LoadPlugins($dirname)
{
	global $_TPL, $db;
	$files = glob("{$dirname}*.php");
	foreach ($files as $currentfile)
	{
		require($currentfile);
	}
}


/**
 * Voert een print_r uit van een array en stuurt deze terug met tussen <code><pre> en </pre></code> om de opmaak te behouden.
 * @param string $array input array
 */
function print_array($array)
{
	ob_start();
	
	print_r($array);
	$output = '<pre>';
	$output .= str_replace("\n", "<br>", addslashes(ob_get_contents()));
	ob_end_clean();
	$output .= '</pre>';
	return($output);
}


/**
 * Sorteert een array multidimensionaal op zo'n manier dat alle key's en values nog bij elkaar kloppen op een bepaalde key van $marray.
 * @param array $marray multidimensionale array
 * @param string $column Te sorteren array key
 */
function array_csort($marray, $column)
{
	if (is_array($marray))
	{
		foreach ($marray as $row)
		{
			$sortarr[] = strtolower($row[$column]);
		}
		@array_multisort($sortarr, $marray);
	}
	return $marray;
}


/**
 * Strip de extentie (bijv .php) van een bestandsnaam af.
 * Nuttig voor het weergeven van bijv. een lijst van bestanden.
 * @param string $filename te strippen bestandsnaam 
 */
function strip_ext($filename)
{
	return(substr($filename, 0, strrpos($filename, '.')));
}

/**
 * Stuurt de extentie van een bestandsnaam terug
 * Nuttig voor bijv. het filteren van .jpg of .php bestanden uit een directory.
 * @param string $filename bestand waar de extentie van gestript wordt.
 */
 function get_ext($filename)
{
	return(substr($filename, strrpos($filename, '.'), strlen($filename)));
}


/**
 * Converteert 'gevaarlijke' entities voor XHTML naar HTML codes door middel van een string-replace van een array van keys van gevaarlijke entities
 * met een value van de 'goede' entity.
 * @param string $inputtext Text waar we de string replace op los laten
 * @returns string $inputtext gewijzigde string
 */
function xHtmlEntities($inputtext)
{
	$replaceArray = array('�' => '&#338;', '�' => '&#339;', '�' => '&#352;', '�' => '&#353;', '�' => '&#376;', '�' => '&#8216;', '�' => '&#8217;', '�' => '&#8218;', '�' => '&#8220;', '�' => '&#8221;', '�' => '&#8222;', '�' => '&#8224;', '�' => '&#8225;', '�' => '&#8240;', '�' => '&#8249;', '�' => '&#8250;', '�' => '&#8364;', '�' => '&#8482;', '�' => '&#192;', '�' => '&#193;', '�' => '&#194;', '�' => '&#195;', '�' => '&#196;', '�' => '&#197;', '�' => '&#198;', '�' => '&#199;', '�' => '&#200;', '�' => '&#201;', '�' => '&#202;', '�' => '&#203;', '�' => '&#204;', '�' => '&#205;', '�' => '&#206;', '�' => '&#207;', '�' => '&#208;', '�' => '&#209;', '�' => '&#210;', '�' => '&#211;', '�' => '&#212;', '�' => '&#213;', '�' => '&#214;', '�' => '&#216;', '�' => '&#217;', '�' => '&#218;', '�' => '&#219;', '�' => '&#220;', '�' => '&#221;', '�' => '&#222;', '�' => '&#223;', '�' => '&#224;', '�' => '&#225;', '�' => '&#226;', '�' => '&#227;', '�' => '&#228;', '�' => '&#229;', '�' => '&#230;', '�' => '&#231;', '�' => '&#232;', '�' => '&#233;', '�' => '&#234;', '�' => '&#235;', '�' => '&#236;', '�' => '&#237;', '�' => '&#238;', '�' => '&#239;', '�' => '&#240;', '�' => '&#241;', '�' => '&#242;', '�' => '&#243;', '�' => '&#244;', '�' => '&#245;', '�' => '&#246;', '�' => '&#248;', '�' => '&#249;', '�' => '&#250;', '�' => '&#251;', '�' => '&#252;', '�' => '&#253;', '�' => '&#254;', '�' => '&#255;', '�' => '&#161;', '�' => '&#164;', '�' => '&#162;', '�' => '&#163;', '�' => '&#165;', '�' => '&#166;', '�' => '&#167;', '�' => '&#168;', '�' => '&#169;', '�' => '&#170;', '�' => '&#171;', '�' => '&#172;', '�' => '&#174;', '�' => '&trade;', '�' => '&#175;', '�' => '&#176;', '�' => '&#177;', '�' => '&#178;', '�' => '&#179;', '�' => '&#180;', '�' => '&#181;', '�' => '&#182;', '�' => '&#183;', '�' => '&#184;', '�' => '&#185;', '�' => '&#186;', '�' => '&#187;', '�' => '&#188;', '�' => '&#189;', '�' => '&#190;', '�' => '&#191;', '�' => '&#215;', '�' => '&#247;');  
	foreach ($replaceArray as $key=>$val)
	{
		$inputtext= str_replace($key, $val, $inputtext);
	}
	return ($inputtext);

}   

function glob_r($sDir, $sPattern, $nFlags = NULL)
{
  $files = glob("$sDir/$sPattern", $nFlags);
  $aFiles = ($files) ? $files : array();
  foreach (glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir)
  {
	 $input = glob_r($sSubDir, $sPattern, $nFlags);
	 $aSubFiles = ($input) ? $input : array();
   $aFiles = (is_array($aFiles)) ? array_merge($aFiles, $aSubFiles) : $aFiles; 
  }
  return $aFiles;
} 

?>