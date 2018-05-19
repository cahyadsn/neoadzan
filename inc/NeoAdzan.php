<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : NeoAdzan.php
package  : /cahyadsn/neoadzan
purpose  :
create   : 2018/05/08
last edit: 2018/05/19
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
include "TimeTraits.php";
include "TrigonometriTraits.php";
include "Adzan.php";

class NeoAdzan extends Adzan
{
	
	var $nama_bulan_masehi=array("Desember","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
	var $add=0;
	
	var $tgl_masehi;
	var $bln_masehi;
	var $thn_masehi;
	
	var $tgl_hijriah;
	var $thn_hijriah;
	var $bln_hijriah;
	var $WD;
	var $PA;
	var $AZm;
	var $AZh;
	var $z;
	var $hc;
	var $dhc;
	var $UB;
	var $nextMonth;
	
	var $lat;
	var $lng;
	var $elev;
	var $TZ;
	var $imkan_rukyat;
	var $periode;
	var $rentang;
	
	var $qiblaDirection;
	var $qiblaDistance;
	
	function __construct($methodID = 8)
	{
		parent::__construct($methodID);
		$this->setMonthH();
		$this->setYearH();
		$this->TZ=isset($_POST['z'])?$_POST['z']:7;
		$this->lat=isset($_POST['lat'])?$_POST['lat']:-6.17501;
		$this->lng=isset($_POST['lng'])?$_POST['lng']:106.820497;
		$this->elev=isset($_POST['h'])?$_POST['h']:10;
	}
	
	public function setLatLng($lat,$lng)
	{
		$this->lat=$lat;
		$this->lng=$lng;
	}
	
	public function setElevation($elev=0)
	{
		$this->elev=$elev;
	}
		
	public function setTimeZone($TZ)
	{
		$this->TZ=$TZ;
	}
	
	public function setMonthH($bln_hijriah=8)
	{
		$this->bln_hijriah=$bln_hijriah;
	}
	
	public function setYearH($thn_hijriah=1439)
	{
		$this->thn_hijriah=$thn_hijriah;
	}

	public function getSchedule($year='',$month=''){
		$result='';
		if(empty($year)) $year=date('Y');
		if(empty($month)) $month=date('n');
		$date=strtotime(date($year.'-'.$month.'-1'));
		$end_of_day=date('t', $date);
		$this->periode=strtoupper($this->nama_bulan_masehi[$month])." {$year}";
		for($i=1;$i<=$end_of_day;$i++){
			$times = $this->getPrayerTimes($date, $this->lat, $this->lng, $this->TZ);
			$hari=date('w',$date);
			$day = date('d F Y', $date);
			$day2= "{$i} {$this->nama_bulan_masehi[$month]} {$year}";
			$result.="<tr class='".(date('Y-n-j')==$year.'-'.$month.'-'.$i?"w3-theme-d1":"w3-theme-l".($i%2==0?'5':'4'))."'>";
			$result.="<td>{$day2}</td>";
			foreach($times as $k=>$t){
				$result.=(!in_array($k,array(2,3,6))?"<td".($k==0?" class='w3-hide-small'":"").">{$t}</td>":"");
			}
			$result.="</tr>";
			$date = strtotime($day.' + 1 Days');  // next day
		}
		return $result;
	}

	public function getMonthly($year='',$month=''){
		$result='';
		if(empty($year)) $year=date('Y');
		if(empty($month)) $month=date('n');
		$date=strtotime(date($year.'-'.$month.'-1'));
		$end_of_day=date('t', $date);
		$this->periode=strtoupper($this->nama_bulan_masehi[$month])." {$year}";
		$jadwal=array();
		for($i=1;$i<=$end_of_day;$i++){
			$times = $this->getPrayerTimes($date, $this->lat, $this->lng, $this->TZ);
			$hari=date('w',$date);
			$day = date('d F Y', $date);
			$day2= "{$i} {$this->nama_bulan_masehi[$month]} {$year}";
			$jadwal[$i]['tgl']=$day2;
			$jadwal[$i]['shubuh']=$times[1];
			$jadwal[$i]['dhuhur']=$times[4];
			$jadwal[$i]['ashar']=$times[5];
			$jadwal[$i]['maghrib']=$times[7];
			$jadwal[$i]['isya']=$times[8];
			$date = strtotime($day.' + 1 Days');  // next day
		}
		return array(
			'status'=>true,
			'data'=>array(
				'lokasi'=>array(
					'lat'=>$this->lat,
					'lng'=>$this->lng,
					'tz'=>$this->TZ
				),
				'periode'=>array(
					'tahun'=>$year,
					'bulan'=>$month,
				),
				'jadwal'=>$jadwal
			)
		);
	}

	public function getDaily($year='',$month='',$day1=''){
		$result='';
		if(empty($year)) $year=date('Y');
		if(empty($month)) $month=date('n');
		if(empty($day1)) $day1=date('j');
		$date=strtotime(date($year.'-'.$month.'-1'));
		$end_of_day=date('t', $date);
		$jadwal=array();
		for($i=1;$i<=$end_of_day;$i++){
			$times = $this->getPrayerTimes($date, $this->lat, $this->lng, $this->TZ);
			$hari=date('w',$date);
			$day = date('d F Y', $date);
			$day2= "{$i} {$this->nama_bulan_masehi[$month]} {$year}";
			$jadwal[$i]['shubuh']=$times[1];
			$jadwal[$i]['dhuhur']=$times[4];
			$jadwal[$i]['ashar']=$times[5];
			$jadwal[$i]['maghrib']=$times[7];
			$jadwal[$i]['isya']=$times[8];
			$date = strtotime($day.' + 1 Days');  // next day
		}
		return array(
			'status'=>true,
			'data'=>array(
				'lokasi'=>array(
					'lat'=>$this->lat,
					'lng'=>$this->lng,
					'tz'=>$this->TZ
				),
				'periode'=>array(
					'tahun'=>$year,
					'bulan'=>$month,
					'tgl'=>$day1
				),
				'jadwal'=>$jadwal[$day1]
			)
		);
	}

}
