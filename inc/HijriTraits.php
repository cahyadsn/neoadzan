<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : HijriTraits.php
package  : /cahyadsn/neoadzan
purpose  :
create   : 2018/05/08
last edit: 2020/04/20
author   : cahya dsn
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

copyright (c) 2018-2020 by cahya dsn; cahyadsn@gmail.com
================================================================================*/
trait HijriTraits
{
    public function fromGregorianToHijri($time = null)
    {
         if ($time === null) $time = time();
         $m = date('m', $time);
         $d = date('d', $time);
         $y = date('Y', $time);
         return $this->fromJDToHijri(cal_to_jd(CAL_GREGORIAN, $m, $d, $y));
    }

    public function fromHijriToGregorian($m, $d, $y)
    {
         return jd_to_cal(CAL_GREGORIAN,
             $this->fromHijriToJD($m, $d, $y));
    }

     # Julian Day Count To Hijri
    public function fromJDToHijri($jd)
    {
         $jd = $jd - 1948440 + 10632;
         $n  = (int)(($jd - 1) / 10631);
         $jd = $jd - 10631 * $n + 354;
         $j  = ((int)((10985 - $jd) / 5316)) *
             ((int)(50 * $jd / 17719)) +
             ((int)($jd / 5670)) *
             ((int)(43 * $jd / 15238));
         $jd = $jd - ((int)((30 - $j) / 15)) *
             ((int)((17719 * $j) / 50)) -
             ((int)($j / 16)) *
             ((int)((15238 * $j) / 43)) + 29;
         $m  = (int)(24 * $jd / 709);
         $d  = $jd - (int)(709 * $m / 24);
         $y  = 30*$n + $j - 30;
         return array($m, $d, $y);
    }

     # Hijri To Julian Day Count
    public function fromHijriToJD($m, $d, $y)
    {
         return (int)((11 * $y + 3) / 30) +
             354 * $y + 30 * $m -
             (int)(($m - 1) / 2) + $d + 1948440 - 385;
    }
 };
