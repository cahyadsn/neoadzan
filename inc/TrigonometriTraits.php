<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : TrigonometriTraits.php
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
trait TrigonometriTraits
{
	//---------------------- Trigonometric Functions -----------------------

    // degree sin
    function dsin($d)
    {
        return sin(deg2rad($d));
    }

    // degree cos
    function dcos($d)
    {
        return cos(deg2rad($d));
    }

    // degree tan
    function dtan($d)
    {
        return tan(deg2rad($d));
    }

    // degree arcsin
    function darcsin($x)
    {
        return rad2deg(asin($x));
    }

    // degree arccos
    function darccos($x)
    {
        return rad2deg(acos($x));
    }

    // degree arctan
    function darctan($x)
    {
        return rad2deg(atan($x));
    }

    // degree arctan2
    function darctan2($y, $x)
    {
        return rad2deg(atan2($y, $x));
    }

    // degree arccot
    function darccot($x)
    {
        return rad2deg(atan(1/$x));
    }

    // range reduce angle in degrees.
    function fixangle($a)
    {
        $a = $a - 360.0 * floor($a / 360.0);
        $a = $a < 0 ? $a + 360.0 : $a;
        return $a;
    }
}
