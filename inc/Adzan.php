<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : Adzan.php
package  : /cahyadsn/neoadzan
purpose  :
create   : 2018/05/08
last edit: 2018/05/15
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
class Adzan
{

	use TimeTraits,
		TrigonometriTraits;
    //------------------------ Constants --------------------------
    // Calculation Methods
    var $Custom     = 0;    // Custom Setting
    var $Jafari     = 1;    // Ithna Ashari
    var $Karachi    = 2;    // University of Islamic Sciences, Karachi
    var $ISNA       = 3;    // Islamic Society of North America (ISNA)
    var $MWL        = 4;    // Muslim World League (MWL)
    var $Makkah     = 5;    // Umm al-Qura, Makkah
    var $Egypt      = 6;    // Egyptian General Authority of Survey
    var $Tehran     = 7;    // Institute of Geophysics, University of Tehran
	var $Depag		= 8;	// Departemen Agama RI
    // Juristic Methods
    var $Shafii     = 0;    // Shafii (standard)
    var $Hanafi     = 1;    // Hanafi
    // Adjusting Methods for Higher Latitudes
    var $None       = 0;    // No adjustment
    var $MidNight   = 1;    // middle of night
    var $OneSeventh = 2;    // 1/7th of night, sab'u lail
    var $AngleBased = 3;    // angle/60th of night
    // Time Names
	var $time_names=array(
		'imsak'=>0,
		'fajr'=>1,
		'sunrise'=>2,
		'dhuha'=>3,
		'dzuhr'=>4,
		'ashr'=>5,
		'sunset'=>6,
		'maghrib'=>7,
		'isha'=>8,
		'midnight'=>9,
		'third_night'=>10,
		'seventh_night'=>11
	);

	var $ihtilat_duration=array(
		'fajr'=>2,
		'dzuhr'=>4,
		'ashr'=>2,
		'maghrib'=>2,
		'isha'=>2
	);
	
    var $timeNames = array(
		'Imsak',
        'Fajr',
        'Sunrise',
		'Dhuha',
        'Dhuhr',
        'Asr',
        'Sunset',
        'Maghrib',
        'Isha',
		'Mid Night',
		'Third of Night',
		'Seven of Night'
    );
    //---------------------- Global Variables --------------------
    var $calcMethod   = 0;        // caculation method
    var $asrJuristic  = 0;        // Juristic method for Asr
	
	//-- Ihtilat time adjusment
    var $fajrMinutes  = 2;        // minutes after Fajr
    var $dhuhrMinutes = 4;        // minutes after mid-day for Dhuhr
    var $ashrMinutes  = 2;        // minutes after Ashr
    var $maghribMinutes  = 2;        // minutes after Sunset for Maghrib
    var $ishaMinutes  = 2;        // minutes after Isha

	
	var $adjustHighLats = 0;    // adjusting method for higher latitudes
    var $lat;        // latitude
    var $lng;        // longitude
    var $timeZone;   // time-zone
    var $JDate;      // Julian date
    //--------------------- Technical Settings --------------------
    var $numIterations = 1;        // number of iterations needed to compute times
    //------------------- Calc Method Parameters --------------------
    var $methodParams = array();
    /*  var $methodParams[methodNum] = array(fa, ms, mv, is, iv);
			fs : fajr selector  (0 = angle; 1 = minutes before sunrise)
            fv : fajr parameter value (in angle or minutes)
            ms : maghrib selector (0 = angle; 1 = minutes after sunset)
            mv : maghrib parameter value (in angle or minutes)
            is : isha selector (0 = angle; 1 = minutes after maghrib)
            iv : isha parameter value (in angle or minutes)
    */
	var $fiqh_parameters=array(
		'imsak_selector', 	//-- imsak selector  (0 = angle; 1 = minutes before fajr)
		'imsak_value',		//-- imsak parameter value (in angle or minutes)
		'fajr_selector',	//-- fajr selector  (0 = angle; 1 = minutes before sunrise)
		'fajr_value',		//-- fajr parameter value (in angle or minutes)
		'dhuha_selector',	//-- dhuha selector  (0 = angle; 1 = minutes after sunrise)
		'dhuha_value',		//-- dhuha parameter value (in angle or minutes)
		'maghrib_selector',	//-- maghrib selector (0 = angle; 1 = minutes after sunset)
		'maghrib_value',	//-- maghrib parameter value (in angle or minutes)
		'isha_selector',	//-- isha selector (0 = angle; 1 = minutes after maghrib)
		'isha_value',		//-- isha parameter value (in angle or minutes)
	);

    //----------------------- Constructors -------------------------
    function __construct($methodID = 8)
    {

        $this->methodParams[$this->Custom]    = array(1, 10, 0, 18, 1, 0, 0, 17);
		$this->methodParams[$this->ISNA]      = array(1, 10, 0, 15, 1, 0, 0, 15);
        $this->methodParams[$this->Jafari]    = array(1, 10, 0, 16, 0, 4, 0, 14);
        $this->methodParams[$this->Karachi]   = array(1, 10, 0, 18, 1, 0, 0, 18);
        $this->methodParams[$this->MWL]       = array(1, 10, 0, 18, 1, 0, 0, 17);
        $this->methodParams[$this->Makkah]    = array(1, 10, 0, 18.5, 1, 0, 1, 90);
        $this->methodParams[$this->Egypt]     = array(1, 10, 0, 19.5, 1, 0, 0, 17.5);
        $this->methodParams[$this->Tehran]    = array(1, 10, 0, 17.7, 0, 4.5, 0, 14);
		$this->methodParams[$this->Depag]  	  = array(1, 10, 0, 20, 1, 1, 0, 18);
        $this->setCalcMethod($methodID);
    }

    //-------------------- Interface Functions --------------------

    // return prayer times for a given date
    function getDatePrayerTimes($year, $month, $day, $latitude, $longitude, $timeZone)
    {
        $this->lat = $latitude;
        $this->lng = $longitude;
        $this->timeZone = $timeZone;
        $this->JDate = cal_to_jd(CAL_GREGORIAN,$month, $day, $year)- $longitude/ (15* 24);
        return $this->computeDayTimes();
    }

    // return prayer times for a given timestamp
    function getPrayerTimes($timestamp, $latitude, $longitude, $timeZone)
    {
        $date = @getdate($timestamp);
        return $this->getDatePrayerTimes(
						$date['year'],
						$date['mon'],
						$date['mday'],
						$latitude,
						$longitude,
						$timeZone
					);
    }

    // set the calculation method
    function setCalcMethod($methodID)
    {
        $this->calcMethod = $methodID;
    }

    // set the juristic method for Asr
    function setAsrMethod($methodID)
    {
        if ($methodID < 0 || $methodID > 1)
            return;
        $this->asrJuristic = $methodID;
    }

    // set the angle for calculating Fajr
    function setFajrAngle($angle)
    {
        $this->setCustomParams(array($angle, null, null, null, null));
    }

    // set the angle for calculating Maghrib
    function setMaghribAngle($angle)
    {
        $this->setCustomParams(array(null, 0, $angle, null, null));
    }

    // set the angle for calculating Isha
    function setIshaAngle($angle)
    {
        $this->setCustomParams(array(null, null, null, 0, $angle));
    }
	
    // set the minutes after mid-day for calculating Dhuhr
    function setDhuhrMinutes($minutes)
    {
        $this->dhuhrMinutes = $minutes;
    }

    // set the minutes after Sunset for calculating Maghrib
    function setMaghribMinutes($minutes)
    {
        $this->setCustomParams(array(null, 1, $minutes, null, null));
    }

    // set the minutes after Maghrib for calculating Isha
    function setIshaMinutes($minutes)
    {
        $this->setCustomParams(array(null, null, null, 1, $minutes));
    }

    // set custom values for calculation parameters
    function setCustomParams($params)
    {
        for ($i=0; $i<8; $i++)
        {
            if ($params[$i] == null)
                $this->methodParams[$this->Custom][$i] = $this->methodParams[$this->calcMethod][$i];
            else
                $this->methodParams[$this->Custom][$i] = $params[$i];
        }
        $this->calcMethod = $this->Custom;
    }

    // set adjusting method for higher latitudes
    function setHighLatsMethod($methodID)
    {
        $this->adjustHighLats = $methodID;
    }

    // set the time format
    function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;
    }

   //---------------------- Calculation Functions -----------------------

    // References:
    // http://www.ummah.net/astronomy/saltime
    // http://aa.usno.navy.mil/faq/docs/SunApprox.html

    // compute declination angle of sun and equation of time
    function sunPosition($jd)
    {
        $D = $jd - 2451545.0;
        $g = fmod((357.529 + 0.98560028* $D),360);
        $q = fmod((280.459 + 0.98564736* $D),360);
        $L = fmod(($q + 1.915* $this->dsin($g) + 0.020* $this->dsin(2*$g)),360);
        $R = 1.00014 - 0.01671* $this->dcos($g) - 0.00014* $this->dcos(2*$g);
        $e = 23.4397 - 0.00000036* $D;
        $dec = $this->darcsin($this->dsin($e)* $this->dsin($L));
        $RA = $this->darctan2($this->dcos($e)* $this->dsin($L), $this->dcos($L))/ 15;
        $RA = fmod($RA,24.0);
		//$dec=abs($dec);
        $EqT = $q/15 - $RA;
        return array($dec, $EqT);
    }

    // compute equation of time
    function equationOfTime($jd)
    {
        $sp = $this->sunPosition($jd);
        return $sp[1];
    }

    // compute declination angle of sun
    function sunDeclination($jd)
    {
        $sp = $this->sunPosition($jd);
        return $sp[0];
    }

    // compute mid-day (Dhuhr, Zawal) time
    function computeMidDay($t)
    {
        $T = $this->equationOfTime($this->JDate+ $t);
        $Z = fmod((12- $T),24.0);
        return abs($Z);
    }

    // compute time for a given angle G
    function computeTime($G, $t)
    {
        $D = $this->sunDeclination($this->JDate+ $t);
        $Z = $this->computeMidDay($t);
        $V = 1/15* $this->darccos((-$this->dsin($G)- $this->dsin($D)* $this->dsin($this->lat))/
                ($this->dcos($D)* $this->dcos($this->lat)));
		//echo "{$Z} # {$V} ";
        return $Z+ ($G>90 ? -$V : $V);
    }

    // compute the time of Asr
    function computeAsr($step, $t)  // Shafii: step=1, Hanafi: step=2
    {
        $D = $this->sunDeclination($this->JDate+ $t);
        $G = -$this->darccot($step+ $this->dtan(abs($this->lat- $D)));
        return $this->computeTime($G, $t);
    }
    //---------------------- Compute Prayer Times -----------------------

    // compute prayer times at given julian date
    function computeTimes($times)
    {
        $t = $this->dayPortion($times);
		//print_r($t);
		$Imsak   = $this->computeTime(180 - $this->methodParams[$this->calcMethod][3], $t[0]);
		//print_r($Imsak);
        $Fajr    = $this->computeTime(180 - $this->methodParams[$this->calcMethod][3], $t[1]);
        
		$AstronomicalDawn= -18.0;
		$NauticalDawn = -12.0;
		$CivilDawn =-6.0;
		$Sunrise = $this->computeTime(180 - 0.833, $t[2]);
		
		$Dhuha	 = $this->computeTime(180+15, $t[3]);
        $Dhuhr   = $this->computeMidDay($t[4]);
        $Asr     = $this->computeAsr(1+ $this->asrJuristic, $t[5]);
        $Sunset  = $this->computeTime(0.833, $t[6]);;
		$CivilDusk = 6.0;
		$NauticalDusk = 12.0;
		$AstronomicalDusk= 18.0;


        $Maghrib = $this->computeTime($this->methodParams[$this->calcMethod][5], $t[7]);
        $Isha    = $this->computeTime($this->methodParams[$this->calcMethod][7], $t[8]);
        return array($Imsak, $Fajr, $Sunrise, $Dhuha, $Dhuhr, $Asr, $Sunset, $Maghrib, $Isha);
    }

    // compute prayer times at given julian date
    function computeDayTimes()
    {
        $times = array(4, 5, 6, 7, 12, 15, 18, 18, 19 , 0,  1, 4); //default times
        for ($i=1; $i<=$this->numIterations; $i++){
            $times = $this->computeTimes($times);
			//print_r($times);
		}
        $times = $this->adjustTimes($times);
		//print_r($times);
        return $this->adjustTimesFormat($times);
    }

    // adjust times in a prayer time array
    function adjustTimes($times)
    {
        $n=count($times);
		for($i=0;$i<$n;$i++){
            $times[$i]  += $this->timeZone- $this->lng/ 15;
		}
		if ($this->methodParams[$this->calcMethod][0] == 1) // Imsak
            $times[0] = $times[1] - $this->methodParams[$this->calcMethod][1]/ 60;
        $times[0] += $this->fajrMinutes / 60;
		if ($this->methodParams[$this->calcMethod][2] == 1) // Fajr
            $times[1] = $times[2] - $this->methodParams[$this->calcMethod][3]/ 60;
		$times[1] += $this->fajrMinutes /60; 
		$times[4] += $this->dhuhrMinutes / 60; // Dhuhr
		$times[5] += $this->ashrMinutes / 60;  // Ashr
		if ($this->methodParams[$this->calcMethod][4] == 1) // Maghrib
            $times[7] = $times[6]+ $this->methodParams[$this->calcMethod][5]/ 60;
		$times[7] += $this->maghribMinutes / 60;
        if ($this->methodParams[$this->calcMethod][6] == 1) // Isha
            $times[8] = $times[7]+ $this->methodParams[$this->calcMethod][7]/ 60;
        $times[8] += $this->ishaMinutes / 60;
		if ($this->adjustHighLats != $this->None)
            $times = $this->adjustHighLatTimes($times);
        return $times;
    }

    // convert times array to given time format
    function adjustTimesFormat($times)
    {
        if ($this->timeFormat == $this->Float)
            return $times;
        foreach($times as $i=>$t)
            if ($this->timeFormat == $this->Time12)
                $times[$i] = $this->floatToTime12($t);
            else if ($this->timeFormat == $this->Time12NS)
                $times[$i] = $this->floatToTime12($t, true);
            else
                $times[$i] = $this->floatToTime24($t);
        return $times;
    }

    // adjust Fajr, Isha and Maghrib for locations in higher latitudes
    function adjustHighLatTimes($times)
    {
        $nightTime = $this->timeDiff($times[5], $times[2]); // sunset to sunrise
        // Adjust Fajr
        $FajrDiff = $this->nightPortion($this->methodParams[$this->calcMethod][0])* $nightTime;
        if (is_nan($times[0]) || $this->timeDiff($times[0], $times[1]) > $FajrDiff)
            $times[0] = $times[1]- $FajrDiff;
        // Adjust Isha
        $IshaAngle = ($this->methodParams[$this->calcMethod][3] == 0) ? $this->methodParams[$this->calcMethod][4] : 18;
        $IshaDiff = $this->nightPortion($IshaAngle)* $nightTime;
        if (is_nan($times[6]) || $this->timeDiff($times[4], $times[6]) > $IshaDiff)
            $times[6] = $times[4]+ $IshaDiff;
        // Adjust Maghrib
        $MaghribAngle = ($this->methodParams[$this->calcMethod][1] == 0) ? $this->methodParams[$this->calcMethod][2] : 4;
        $MaghribDiff = $this->nightPortion($MaghribAngle)* $nightTime;
        if (is_nan($times[5]) || $this->timeDiff($times[4], $times[5]) > $MaghribDiff)
            $times[5] = $times[4]+ $MaghribDiff;
        return $times;
    }

    // the night portion used for adjusting times in higher latitudes
    function nightPortion($angle)
    {
        if ($this->adjustHighLats == $this->AngleBased)
            return 1/60* $angle;
        if ($this->adjustHighLats == $this->MidNight)
            return 1/2;
        if ($this->adjustHighLats == $this->OneSeventh)
            return 1/7;
    }

    // convert hours to day portions
    function dayPortion($times)
    {
        foreach($times as $i=>$t)
            $times[$i] = $t/ 24;
        return $times;
    }

}
