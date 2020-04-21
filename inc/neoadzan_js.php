<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename : neoadzan_js.php
purpose  :
create   : 2018/05/08
last edit: 200421,180516
author   : cahya dsn
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

copyright (c) 2018-2020 by cahya dsn; cahyadsn@gmail.com
================================================================================*/
session_start();
header("Content-type: text/javascript");
$c=isset($_SESSION['c'])?$_SESSION['c']:(isset($_GET['c'])?$_GET['c']:'indigo');
if(isset($_SESSION['author']) && $_SESSION['author']=='cahyadsn'){
	$v=$_GET['v'];
	session_destroy();
} else {
	die('illegal call');
}
?>
var pesan=function(msg){
	$("#msg_box").html(msg);
	$("#msg_box").addClass("w3-red");
	$("#msg_box").show();
	$("#msg_box").delay(2000).fadeOut();			
}
$(document).ready(function(){
	//--
	$('a.color').on('click',function() {
      var a = $(this).attr('data-value');
      document.getElementById('adzan_css').href = 'css/w3-theme-' + a + '.css';
      $.post('inc/change.color.php', {
          'color': a
      })
	  myPolygon.setOptions({fillColor: a,strokeColor: a});
	});
	//--
	$('.slcProv').on('change',function(){
		$('div#preload').show();
		var url="inc/neoadzan_ajax.php?sid="+Math.random();
		$.post(
			url,
			{id:$(this).val(),y:$('#y').val(),m:$('#m').val()},
			function(d){
				if(!d.status){
					alert(d.status);
				}else{
					$('#kota').html(d.opt);
					$('#sch').html(d.data.sch);
					$('.sprov').html('Provinsi '+d.data.nama);
					$('.skab').html('');
					$('.slat').html(d.data.lat);
					$('.slng').html(d.data.lng);
					$('.stz').html(d.data.tz)
				}
				$('div#preload').hide();
			}
		);
	});
	$('.slcKab').on('change',function(){
		$('div#preload').show();
		var url="inc/neoadzan_ajax.php?sid="+Math.random();
		$.post(
			url,
			{id:$(this).val(),y:$('#y').val(),m:$('#m').val()},
			function(d){
				if(!d.status){
					alert(d.status);
				}else{
					console.log(d);
					$('#sch').html(d.data.sch);
					$('.skab').html(d.data.nama+' , ');
					$('.slat').html(d.data.lat);
					$('.slng').html(d.data.lng);
					$('.stz').html(d.data.tz);
				}
				$('div#preload').hide();
			}
		);
	});
	var m,y;
	var changeMonth = function(){
		$('div#preload').show();
		var url="inc/neoadzan_ajax.php?sid="+Math.random();
		var idx=$('#kota').val();
		if(idx=='') idx=$('#prop').val();
		$.post(
			url,
			{id:idx,y:$('#y').val(),m:$('#m').val()},
			function(d){
				if(!d.status){
					alert(d.status);
				}else{
					//console.log(d);
					$('#sch').html(d.data.sch);
					$('#periode').html(d.data.periode);
                    $('#rentang').html(d.data.rentang);
				}
				$('div#preload').hide();
			}
		);
	};
	$('#prevMonth').on('click',function(e){
		e.preventDefault();
		m=parseInt($('#m').val());
		y=parseInt($('#y').val());
		if(m>1){m-=1;} else {m=12;y-=1;}
		$('#m').val(m);
		$('#y').val(y);
		changeMonth();
	});
	$('#prevYear').on('click',function(e){
		e.preventDefault();
		y=parseInt($('#y').val());
		$('#y').val(--y);
		changeMonth();
	});
	$('#nextMonth').on('click',function(e){
		e.preventDefault();
		m=parseInt($('#m').val());
		y=parseInt($('#y').val());
		if(m<12){m+=1;} else {m=1;y+=1;}
		$('#y').val(y);
		$('#m').val(m);
		changeMonth();
	});
	$('#nextYear').on('click',function(e){
		e.preventDefault();
		y=parseInt($('#y').val());
		$('#y').val(++y);
		changeMonth();
	});
	function deg2dms(t) {
		var a = 0 > t ? "-" : "";
		t = Math.abs(t);
		var i = Math.floor(t),
		n = 60 * (t - i),
		r = Math.floor(n),
		e = 60 * (n - r);
		return a + i + "\u00b0" + r + "'" + e.toFixed(2) + '"'
	}
});