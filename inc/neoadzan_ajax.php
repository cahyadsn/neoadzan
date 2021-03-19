<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : neoadzan_ajax.php
PURPOSE      : Calculating and Return Result via Ajax Call
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
        'rentang' => $neoadzan->rentang,
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