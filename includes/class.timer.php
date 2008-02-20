<?php

/**
 * 
 * Timer Object.
 */


/**
* Timer Object.
* Meest basic object ooit geschreven. Soort van stopwatch.
*
*	 @package ROI-Intranet 
*	 @copyright (C) 2004 ROI.
*	 @link http://intranetserver/includes/timer.class.php
*	 @author Jelle Ursem
*	 @description  Timer library.
*	 @subpackage Libraries
*	 @filesource
*/


class Timer {
	var $startTime,
		$endTime,
		$timeDifference;
	
	/**
	 * Start de Timer
	 */
	function start() {
		$this->startTime = $this->currentTime();
	}

	/**
	 * Stop de timer
	 */
	function stop() {
		$this->endTime = $this->currentTime();
	}

	/**
	 * Geef de tijd terug afgerond op 5 duizensten
	 */
	function getTime() {
		$this->timeDifference = $this->endTime - $this->startTime;
		return round($this->timeDifference, 5);
	}

	/**
	 * Geef de huidige tussentijd weer
	 */
	function currentTime() {
		list($usec, $sec) = explode(' ',microtime());
		return ((float)$usec + (float)$sec);
	}

}



?>