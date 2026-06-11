<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : neoadzan_js.php
PURPOSE      : generate js script 
AUTHOR       : CAHYA DSN
CREATED DATE : 2018-01-25
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
session_start();
$expires = 60*60*24*7; // 1 week
header("Content-type: text/javascript");
header("Cache-Control: public, max-age=$expires");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expires) . " GMT");
$c=isset($_SESSION['c'])?$_SESSION['c']:(isset($_GET['c'])?$_GET['c']:'indigo');
if(isset($_SESSION['author']) && $_SESSION['author']=='cahyadsn'){
	$v=$_GET['v'];
} else {
	die('illegal call');
}
?>
var pesan = function(msg, type = 'error') {
    var msgBox = document.getElementById("msg_box");
    msgBox.innerHTML = msg;
    msgBox.classList.remove("msg-success", "msg-error");
    msgBox.classList.add(type === 'success' ? "msg-success" : "msg-error");
    msgBox.style.display = "block";
    msgBox.style.opacity = "1";
    setTimeout(function() {
        msgBox.style.transition = "opacity 1s";
        msgBox.style.opacity = "0";
        setTimeout(function() {
            msgBox.style.display = "none";
            msgBox.style.transition = "";
        }, 1000);
    }, 2000);
}

function post(url, data) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    }).then(response => response.json());
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.theme-dot').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var a = this.getAttribute('data-value');
            document.documentElement.setAttribute('data-theme', a);
            post('inc/change.color.php', { 'color': a });
        });
    });

    document.querySelectorAll('.slcProv').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('preload').style.display = 'block';
            var url = "inc/neoadzan_ajax.php?sid=" + Math.random();
            post(url, {
                id: this.value,
                y: document.getElementById('y').value,
                m: document.getElementById('m').value
            }).then(function(d) {
                if (!d.status) {
                    pesan(d.error);
                } else {
                    document.getElementById('kota').innerHTML = d.opt;
                    document.getElementById('sch').innerHTML = d.data.sch;
                    document.querySelectorAll('.sprov').forEach(function(el) { el.innerHTML = 'Provinsi ' + d.data.nama; });
                    document.querySelectorAll('.skab').forEach(function(el) { el.innerHTML = ''; });
                    document.querySelectorAll('.slat').forEach(function(el) { el.innerHTML = d.data.lat; });
                    document.querySelectorAll('.slng').forEach(function(el) { el.innerHTML = d.data.lng; });
                    document.querySelectorAll('.stz').forEach(function(el) { el.innerHTML = d.data.tz; });
                }
                document.getElementById('preload').style.display = 'none';
            });
        });
    });

    document.querySelectorAll('.slcKab').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('preload').style.display = 'block';
            var url = "inc/neoadzan_ajax.php?sid=" + Math.random();
            post(url, {
                id: this.value,
                y: document.getElementById('y').value,
                m: document.getElementById('m').value
            }).then(function(d) {
                if (!d.status) {
                    pesan(d.error);
                } else {
                    document.getElementById('sch').innerHTML = d.data.sch;
                    document.querySelectorAll('.skab').forEach(function(el) { el.innerHTML = d.data.nama + ' , '; });
                    document.querySelectorAll('.slat').forEach(function(el) { el.innerHTML = d.data.lat; });
                    document.querySelectorAll('.slng').forEach(function(el) { el.innerHTML = d.data.lng; });
                    document.querySelectorAll('.stz').forEach(function(el) { el.innerHTML = d.data.tz; });
                }
                document.getElementById('preload').style.display = 'none';
            });
        });
    });

    var changeMonth = function() {
        document.getElementById('preload').style.display = 'block';
        var url = "inc/neoadzan_ajax.php?sid=" + Math.random();
        var idx = document.getElementById('kota').value;
        if (idx == '') idx = document.getElementById('prop').value;
        post(url, {
            id: idx,
            y: document.getElementById('y').value,
            m: document.getElementById('m').value
        }).then(function(d) {
            if (!d.status) {
                pesan(d.error);
            } else {
                document.getElementById('sch').innerHTML = d.data.sch;
                document.getElementById('periode').innerHTML = d.data.periode;
                document.getElementById('rentang').innerHTML = d.data.rentang;
            }
            document.getElementById('preload').style.display = 'none';
        });
    };

    document.getElementById('prevMonth').addEventListener('click', function(e) {
        e.preventDefault();
        var m = parseInt(document.getElementById('m').value);
        var y = parseInt(document.getElementById('y').value);
        if (m > 1) { m -= 1; } else { m = 12; y -= 1; }
        document.getElementById('m').value = m;
        document.getElementById('y').value = y;
        changeMonth();
    });

    document.getElementById('prevYear').addEventListener('click', function(e) {
        e.preventDefault();
        var y = parseInt(document.getElementById('y').value);
        document.getElementById('y').value = --y;
        changeMonth();
    });

    document.getElementById('nextMonth').addEventListener('click', function(e) {
        e.preventDefault();
        var m = parseInt(document.getElementById('m').value);
        var y = parseInt(document.getElementById('y').value);
        if (m < 12) { m += 1; } else { m = 1; y += 1; }
        document.getElementById('y').value = y;
        document.getElementById('m').value = m;
        changeMonth();
    });

    document.getElementById('nextYear').addEventListener('click', function(e) {
        e.preventDefault();
        var y = parseInt(document.getElementById('y').value);
        document.getElementById('y').value = ++y;
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
