<?php
include "../inc/db.php";
include "../inc/NeoAdzan.php";
$r=array('status'=>false,'error'=>'an error occured');
$y=(isset($_POST['y']) && !empty($_POST['y']))?$_POST['y']:date('Y');
$m=(isset($_POST['m']) && !empty($_POST['m']))?$_POST['m']:date('n');
if(!empty($_POST['lat'])){
	$lat=$_POST['lat'];
	$lng=$_POST['lng'];
	$tz=(isset($_POST['tz']) && !empty($_POST['tz']))?$_POST['tz']:floor($_POST['lng']/15);
}
if (!empty($_POST['id'])){
  $query = $db->prepare("SELECT * FROM {$dbtable} WHERE kode=:id");
  $query->execute(array(':id'=>$_POST['id']));
  $d = $query->fetchObject();
  if(empty($d->lat)){
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
	$r=$neoadzan->getMonthly($y,$m);
}
header('Content-Type: application/json');
echo json_encode($r);