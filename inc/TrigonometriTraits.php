<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
Filename     : TrigonometriTraits.php
PURPOSE      : Trigonometri Calculation Traits
AUTHOR       : CAHYA DSN
CREATED DATE : 2018-01-25
UPDATED DATE : 2021-03-07
DEMO SITE    : http://neoadzan.cahyadsn.com
SOURCE CODE  : https://github.com/cahyadsn/neoadzan
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the MIT License.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

See the MIT License for more details

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
