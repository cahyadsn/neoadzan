<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename     : HijriTraits.php
PURPOSE      : Higrea Related Calculation Traits
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

copyright (c) 2018-2021 by cahya dsn; cahyadsn@gmail.com
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
