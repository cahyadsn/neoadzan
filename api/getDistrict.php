<?php
include "../inc/db.php";
$r=array('status'=>false,'error'=>'an error occured');
if(isset($_GET['id']) && !empty($_GET['id'])){
	$query = $db->prepare("SELECT * FROM {$dbtable} WHERE LEFT(kode,2)=:id AND CHAR_LENGTH(kode)=5 ORDER BY kode");
	$query->execute(array(':id'=>$_GET['id']));
	$data=array();
	while($d = $query->fetchObject()){
		$data[]=array($d->kode,$d->nama);
	}
	if(!empty($data)){
		$r=array('status'=>true,'data'=>$data);
	}
}
header('Content-Type: application/json');
echo json_encode($r);