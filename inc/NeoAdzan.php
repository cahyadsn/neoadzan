<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : NeoAdzan.php
PURPOSE      : Main Class of NeoAdzan
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
include "TimeTraits.php";
include "TrigonometriTraits.php";
include "HijriTraits.php";
include "Adzan.php";

class NeoAdzan extends Adzan
{
    use HijriTraits;
    
    var $nama_bulan_hijriah=array("DZUL HIJJAH","MUHARRAM","SHAFAR","RABI'UL AWAL","RABI'UL AKHIR","JUMADIL AWAL","JUMADIL AKHIR","RAJAB","SYA'BAN","RAMADHAN","SYAWWAL","DZUL QA'DAH","DZUL HIJJAH");

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

	public function setYearH($thn_hijriah=1441)
	{
		$this->thn_hijriah=$thn_hijriah;
	}
    
	public function checkDate($date,$before=false)
	{
		$h_date=$this->fromGregorianToHijri($date);
		$this->tgl_hijriah=$h_date[1];
		$m_before=$h_date[0];
		$y_before=$h_date[2];
		if($before){
			if($h_date[0]-1==0){
				$m_before=12;
				$y_before=$h_date[2]-1;
			}else{
				$m_before=$h_date[0]-1;
				$y_before=$h_date[2];
			}
		}
		$this->setYearH($y_before);
		$this->setMonthH($m_before);
		return array(
			date('Y',$this->nextMonth),
			date('n',$this->nextMonth),
			date('j',$this->nextMonth),
			$this->thn_hijriah,
			$this->bln_hijriah,
			$this->tgl_hijriah,
			$this->add
		);
	}

	public function getSchedule($year='',$month=''){
		$result='';
		if(empty($year)) $year=date('Y');
		if(empty($month)) $month=date('n');
		$date=strtotime(date($year.'-'.$month.'-1'));
		$end_of_day=date('t', $date);
        $bdate=date('Y-m-t',strtotime($year.'-'.$month.'-1 - 1 day'));
		$sd=strtotime($year.'-'.$month.'-1');
		$sd=strtotime($bdate);
		$edate=date('Y-m-t',strtotime($year.'-'.$month.'-28'));
		$emonth=date('t',strtotime($year.'-'.$month.'-28'));
		$ed=strtotime($edate);
        $hijri= $this->fromGregorianToHijri($date);
        $r_e= $this->fromGregorianToHijri($ed);
		$this->periode=strtoupper($this->nama_bulan_masehi[$month])." {$year}";
        $this->rentang="({$hijri[1]} {$this->nama_bulan_hijriah[$hijri[0]]} {$hijri[2]} s.d. "
					.($r_e[1])." {$this->nama_bulan_hijriah[$r_e[0]]} {$r_e[2]} )";
		for($i=1;$i<=$end_of_day;$i++){
			$times = $this->getPrayerTimes($date, $this->lat, $this->lng, $this->TZ);
			$hari=date('w',$date);
			$day = date('d F Y', $date);
			$day2= "{$i} {$this->nama_bulan_masehi[$month]} {$year}";
			$hijri= $this->fromGregorianToHijri($date);
			$ayamul_bidh=(in_array($hijri[1],array(13,14,15)) && $hijri[0]!=9 && $hijri[0]!=12);
			$result.="<tr class='".(date('Y-n-j')==$year.'-'.$month.'-'.$i?"w3-theme-d1":"w3-theme-l".($i%2==0?'5':'4'))."'>";
			$result.="<td>{$day2}</td>";
            $result.="<td class='w3-hide-small'>".($ayamul_bidh?"*":"")."{$hijri[1]} ".ucwords(strtolower($this->nama_bulan_hijriah[$hijri[0]]))." {$hijri[2]}</td>";           
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
