<?
/**
* Datum Functie library
*
* DateFunctions.php is de function library die gebruikt wordt als er ergens gerekend wordt met data. Veel voorkomende functies voor optellen / aftrekken van data staan hier in.
*	 
*	 @package pork
*	 @author Jelle Ursem
*	 @description  Date Function library.
*/


/**
 * Equivalent van mysql's current_date() functie. Geeft standaard de huidige datum terug in het "d-m-Y" formaat, maar is aan te passen d.m.v. de parameter naar elk gewenst formaat.
 * @param string $format valid PHP datum formaat
 */
function current_date($format = "d-m-Y")
{
	return (date($format));
}

/**
 * MySQL Based datum-conversie. 
 * Aangezien MySQL beter rekening houdt met schrikkeljaren, etc. verkies ik om datum transformaties via MySQL te laten lopen via 
 * een simpele query m.b.v. SELECT DATE_FORMAT($date, $format)
 * @param string $date datum om om te zetten
 */
function dateconvert($date)
{
	$date = str_replace("/", '-', $date);
	$newdate = mysql_result(mysql_query("SELECT DATE_FORMAT('".$date."', '%d-%m-%Y')"),0);
	return($newdate);
}


/**
 * Datum-conversie. 
 * Converteert een datum van YYYY-MM-DD formaat naar DD-MM-YYYY formaat
 * @param string $indate datum om om te zetten
 */
function dateformat($indate)
{
	$outdate = substr($indate, 6, 4).'-'.substr($indate, 3, 2).'-'.substr($indate, 0, 2);
	return($outdate);
}

/**
 * Welke dag van de week is het in int.
 */
function getDayOfWeek()
{
	return ((date('w') + 6) % 7);
}

/**
 * Berekent hoeveel dagen er in $month van $year zitten.
 * @param int $month te berekenen maand (standaard huidige maand)
 * @param int $year te berekenen jaar (standaard huidige jaar)
 */
function getDaysInMonth($month=null,$year=null)
{
	if ($month==null)
	{
		$month = date("n",time());
	}
	if ($year=null)
	{
	   $year = date("Y",time());
	}
	$dim = date( "j", mktime(0, 0, 0, $month + 1, 1, $year) - 1 );
	return $dim;
}



/**
 * trek een bepaald aantal dagen of maanden af van $startdate
 * @param date $startdate startdatum in formaat $dag-$maand-$year
 * @param int $interval aantal af te trekken maanden of dagen
 * @param string $type 'month' voor maanden, 'day' voor dagen
 * @returns date Bewerkte datum
 */
function date_sub($startdate, $interval, $type='month')
{
	list($day, $month, $year) = split('([^0-9])', $startdate); 
	switch ($type)
	{
		case 'month':
			return(date("d-m-Y", mktime(0,0,0,$month-$interval,$day,$year)));
		break;
		case 'day':
			return(date("d-m-Y", mktime(0,0,0,$month,$day-$interval,$year)));
		break;
	}
}

/**
 * tel een bepaald aantal dagen of maanden op bij $startdate
 * @param date $startdate startdatum in formaat $dag-$maand-$year
 * @param int $interval aantal af te trekken maanden of dagen
 * @param string $type 'month' voor maanden, 'day' voor dagen
 * @returns date Bewerkte datum
 */
function date_add($startdate, $interval, $type='month')
{
	list($day, $month, $year) = split('([^0-9])', $startdate); 
	switch ($type)
	{
		case 'month':
			return(date("d-m-Y", mktime(0,0,0,$month+$interval,$day,$year)));
		break;
		case 'day':
			return(date("d-m-Y", mktime(0,0,0,$month,$day+$interval,$year)));
		break;
	}
}


/**
 * Converteer $indate naar een formaat dat de MySQL database snapt
 * @param date $indate datum in formaat DD-MM-YYYY
 * @returns date $outdate datum in formaat YYYY-MM-DD
 */
function dbdate($indate)
{
	$outdate = substr($indate, 6, 4).'-'.substr($indate, 3, 2).'-'.substr($indate, 0, 2);
	return($outdate);
}

function mssqldate($indate, $addTime = true)
{
	list($day, $month, $year) = split('([^0-9])', $indate); 
	$tm = ($addTime) ? " 00:00:00.000" : '';
	return ("{$year}-{$month}-{$day}{$tm}");
}

function mssqltime($indate)
{
	list($hour, $minute) = split('([^0-9])', $indate); 
	return ("1899-12-30 {$hour}:{$minute}:00.000");
}



/**
 * Converteer $indate naar een nederlands formaat vanaf een MySQL formaat
 * @param date $indate datum in formaat YYYY-MM-DD
 * @returns date $outdate datum in formaat DD-MM-YYYY
 */
function nldate($indate)
{
	list($year, $month, $day) = split('([^0-9])', $indate); 
	$indate = str_replace("00:00:00.000", "", $indate);
	$outdate = "{$day}-{$month}-{$year}";
	return($outdate);
}


/**
 * Rekent het verschil uit tussen 2 data in dagen
 * @param date $startdate in formaat DD-MM-YYYY
 * @param date $enddate in formaat DD-MM-YYYY
 * @returns int aantal dagen
 */
function date_diff($startdate, $enddate) 
{

	if ($startdate == '0000-00-00' || $enddate == '0000-00-00') { return(0); }
	if ($startdate < $enddate)
	{
		$tmp = $startdate;
		$enddate = $startdate;
		$startdate = $tmp;
	}

    list($startyear, $startmonth, $startday) = split('([^0-9])', $startdate); 
	list($endyear, $endmonth, $endday) = split('([^0-9])', $enddate); 
	return (date("z", mktime(0,0,0,$startmonth,$startday,$startyear) - mktime(0,0,0,$endmonth,$endday,$endyear)));
}



?>