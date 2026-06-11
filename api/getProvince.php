<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : getProvince.php
PURPOSE      : API for getting province list
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
include "../inc/Cache.php";

$cache = new Cache();
$cache_key = 'provinces';
$cached_result = $cache->get($cache_key, 2592000); // 30 days

if ($cached_result) {
    header('Content-Type: application/json');
    echo json_encode($cached_result);
    exit;
}

$r=array('status'=>false,'error'=>'an error occured');
$query = $db->prepare("SELECT kode, nama FROM {$dbtable} WHERE CHAR_LENGTH(kode)=2 ORDER BY kode");
$query->execute();
$data=array();
while($d = $query->fetchObject()){
	$data[]=array($d->kode,$d->nama);
}
if(!empty($data)){
	$r=array('status'=>true,'data'=>$data);
    $cache->set($cache_key, $r);
}
header('Content-Type: application/json');
echo json_encode($r);