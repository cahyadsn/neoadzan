<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : TimeTraits.php
package  : /cahyadsn/neoadzan
purpose  :
create   : 2018/05/08
last edit: 2018/05/08
author   : cahya dsn
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

copyright (c) 2018 by cahya dsn; cahyadsn@gmail.com
================================================================================*/

trait TimeTraits
{
	    // Time Formats
    var $Time24     = 0;    // 24-hour format
    var $Time12     = 1;    // 12-hour format
    var $Time12NS   = 2;    // 12-hour format with no suffix
    var $Float      = 3;    // floating point number

    var $InvalidTime = '-----';     // The string used for invalid times
    var $timeFormat   = 0;        // time format

	public function convertDate($dateValue,$pattern="Y-m-d") {    
		$unixDate = ($dateValue - 25569) * 86400;
		return gmdate($pattern, $unixDate);
	}
	public function hms($time,$suffix=false){
		$t=fmod($time*24,24.0);
		$dt=fmod($t,1.0);
		$m=fmod($dt,1.0)*60;
		$dm=fmod($m,1.0);
		$s=fmod($dm,1.0)*60;
		$ds=fmod($s,1.0);
		return $suffix?($t-$dt)."h ".($m-$dm)."m ".($s-$ds)."s":($t-$dt).":".($m-$dm).":".($s-$ds);
	}
	function dms1($degree,$t=0){
		$d=floor($degree);
		$dd=fmod($degree,1.0);
		$m=$dd*60;
		$dm=fmod($dd*60,1.0);
		$s=$dm*60;
		$ds=fmod($s,0.01);
		return $t==0?$d.":".($m-$dm).":".round($s-$ds)." ":$d."h ".($m-$dm)."m ".round($s-$ds)."s ";
	}
	
	function dms($degree){
		$d=floor($degree);
		$dd=fmod($degree,1.0);
		$m=$dd*60;
		$dm=fmod($dd*60,1.0);
		$s=$dm*60;
		$ds=fmod($s,0.01);
		return $d."&deg; ".abs($m-$dm)."' ".abs($s-$ds)."\" ";
	}
	    // compute the difference between two times
    function timeDiff($time1, $time2)
    {
        return fmod(($time2 - $time1),24.0);
    }

    // add a leading 0 if necessary
    function twoDigitsFormat($num)
    {
        return ($num <10 ? '0':''). $num;
    }
    // convert float hours to 24h format
    function floatToTime24($time)
    {
        if (is_nan($time))
            return $this->InvalidTime;
        $time = fmod(($time+ 0.5/ 60),24.0);  // add 0.5 minutes to round
        $hours = floor($time);
        $minutes = floor(($time- $hours)* 60);
        return $this->twoDigitsFormat($hours). ':'. $this->twoDigitsFormat($minutes);
    }

    // convert float hours to 12h format
    function floatToTime12($time, $noSuffix = false)
    {
        if (is_nan($time))
            return $this->InvalidTime;
        $time = fmod(($time+ 0.5/ 60),24.0);  // add 0.5 minutes to round
        $hours = floor($time);
        $minutes = floor(($time- $hours)* 60);
        $suffix = $hours >= 12 ? ' pm' : ' am';
        $hours = ($hours+ 12- 1)% 12+ 1;
        return $hours. ':'. $this->twoDigitsFormat($minutes). ($noSuffix ? '' : $suffix);
    }

    // convert float hours to 12h format with no suffix
    function floatToTime12NS($time)
    {
        return $this->floatToTime12($time, true);
    }
	
	function dayDiff($date1,$date2)
	{
		$d1=new DateTime($date1);
		$d2=new DateTime($date2);
		$interval= $d1->diff($d2);
		return $interval->format('%a');
	}
}
