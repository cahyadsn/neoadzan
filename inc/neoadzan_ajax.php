<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : neoadzan_ajax.php
purpose  :
create   : 2018/04/08
last edit: 2018/04/08
author   : cahya dsn
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

copyright (c) 202018 by cahya dsn; cahyadsn@gmail.com
================================================================================*/
include "db.php";
include "NeoAdzan.php";

$r=array('status'=>false,'error'=>'an error occured');
if (!empty($_POST['id'])){
  $query = $db->prepare("SELECT * FROM {$dbtable} WHERE kode=:id");
  $query->execute(array(':id'=>$_POST['id']));
  $d = $query->fetchObject();
  $y=(isset($_POST['y']) && !empty($_POST['y']))?$_POST['y']:date('Y');
  $m=(isset($_POST['m']) && !empty($_POST['m']))?$_POST['m']:date('n');
  if(empty($d->lat)){
    $r=array('status'=>false,'error'=>'data not found','tz'=>$d->tz);
  }else{
	$neoadzan=new NeoAdzan();
	$neoadzan->setLatLng($d->lat,$d->lng);
	$neoadzan->setTimeZone($d->tz);
	$sch=$neoadzan->getSchedule($y,$m);
    $data=array(
		'kode'=> $d->kode,
		'nama'=>ucwords(strtolower($d->nama)),
		'lat'=> $neoadzan->dms(abs($d->lat)).($d->lat>=0?' LU':' LS'),
		'lng'=> $neoadzan->dms(abs($d->lng)).($d->lng>=0?' BT':' BB'),
		'tz'=> ' '.($d->tz>=0?'+':'-').abs($d->tz),
		'periode' => $neoadzan->periode,
		'sch'=>$sch
	);
    $r=array('status'=>true,'data'=>$data);
  }
  if(empty($_GET['geo'])){
    $n=strlen($_POST['id']);
  	$m=($n==2?5:($n==5?8:13));
  	$wil=($n==2?'Kota/Kab':($n==5?'Kecamatan':'Desa/Kelurahan'));
  	$query = $db->prepare("SELECT * FROM {$dbtable} WHERE LEFT(kode,:n)=:id AND CHAR_LENGTH(kode)=:m ORDER BY nama");
  	$query->execute(array(':n'=>$n,':id'=>$_POST['id'],':m'=>$m));
  	$opt="<option value=''>Pilih {$wil}</option>";    
  	while($d = $query->fetchObject()){
  		$opt.="<option value='{$d->kode}'>{$d->nama}</option>";
  	}
    $r['opt']=$opt;
    $r['n']=$n;
  }
}
header('Content-Type: application/json');
echo json_encode($r);