<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : getDaily.php
PURPOSE      : API for daily prayer time schedule
AUTHOR       : CAHYA DSN
CREATED DATE : 2021-03-07
UPDATED DATE : 2026-06-11 15:45:00
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

copyright (c) 2018-2026 by cahya dsn; cahyadsn@gmail.com
================================================================================*/
include "../inc/db.php";
include "../inc/NeoAdzan.php";
include "../inc/Cache.php";

$cache = new Cache();
$cache_key = 'daily_' . serialize($_POST);
$cached_result = $cache->get($cache_key);

if ($cached_result) {
    header('Content-Type: application/json');
    echo json_encode($cached_result);
    exit;
}

$r=array('status'=>false,'error'=>'an error occured');
$y=(isset($_POST['y']) && !empty($_POST['y']))?$_POST['y']:date('Y');
$m=(isset($_POST['m']) && !empty($_POST['m']))?$_POST['m']:date('n');
$day=(isset($_POST['d']) && !empty($_POST['d']))?$_POST['d']:date('j');
if(!empty($_POST['lat'])){
	$lat=$_POST['lat'];
	$lng=$_POST['lng'];
	$tz=(isset($_POST['tz']) && !empty($_POST['tz']))?$_POST['tz']:floor($_POST['lng']/15);
}
if (!empty($_POST['id'])){
  $query = $db->prepare("SELECT lat, lng, tz FROM {$dbtable} WHERE kode=:id");
  $query->execute(array(':id'=>$_POST['id']));
  $d = $query->fetchObject();
  if(empty($d) || empty($d->lat)){
    $r=array('status'=>false,'error'=>'data not found');
  }else{
	$lat=$d->lat;
	$lng=$d->lng;
	$tz=$d->tz;
  }
}
if(empty($lat)){
	$r=array('status'=>false,'error'=>'data not found');
}else{
	$neoadzan=new NeoAdzan();
	$neoadzan->setLatLng($lat,$lng);
	$neoadzan->setTimeZone($tz);
	$r=$neoadzan->getDaily($y,$m,$day);
}

if ($r['status']) {
    $cache->set($cache_key, $r);
}

header('Content-Type: application/json');
echo json_encode($r);