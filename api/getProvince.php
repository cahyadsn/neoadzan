<?php
include "../inc/db.php";
$r=array('status'=>false,'error'=>'an error occured');
$query = $db->prepare("SELECT * FROM {$dbtable} WHERE CHAR_LENGTH(kode)=2 ORDER BY kode");
$query->execute();
$data=array();
while($d = $query->fetchObject()){
	$data[]=array($d->kode,$d->nama);
}
if(!empty($data)){
	$r=array('status'=>true,'data'=>$data);
}
header('Content-Type: application/json');
echo json_encode($r);