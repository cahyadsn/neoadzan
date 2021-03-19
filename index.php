<?php
/*
================================================================================
 *  BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : index.php
PURPOSE      : main page application
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
session_start();
$c=isset($_SESSION['c'])?$_SESSION['c']:(isset($_GET['c'])?$_GET['c']:'indigo');
define("_AUTHOR","cahyadsn");
$_SESSION['c']=$c;
$_SESSION['author']='cahyadsn';
$_SESSION['ver']=sha1(rand());
include 'inc/db.php';
include 'inc/NeoAdzan.php';
$neoadzan=new NeoAdzan();
$neoadzan->setLatLng(-6.17501,106.820497);
$neoadzan->setTimeZone(7);
$sch=$neoadzan->getSchedule(date('Y'),date('n'));
$version='1.0.2';
$app_name='NeoAdzan!';
/*header('Expires: '.date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');*/
?>
<!DOCTYPE html>
<html lang='en'>
    <head>
    <title><?php echo "{$app_name} v {$version}";?></title>
    <meta charset="utf-8" />
    <meta http-equiv="expires" content="<?php echo date('r');?>" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta http-equiv="content-language" content="en" />
        <meta name="author" content="Cahya DSN" />
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
        <meta name="keywords" content="php, mysql, jadwal, waktu, shalat, cahyadsn" />
        <meta name="description" content="<?php echo "{$app_name} v {$version}";?> created by cahya dsn, Jadwal Waktu Shalat, dalam bahasa pemrograman PHP dan database MySQL" />
        <meta name="robots" content="index, follow" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="css/w3.css">
        <link rel="stylesheet" href="css/w3-theme-<?php echo $c;?>.css" media="all" id="adzan_css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway" //-->
    <link rel="stylesheet" href="css/neoadzan_css.php?v=<?php echo md5(filemtime('css/neoadzan_css.php'));?>">
    </head>
    <body>
        <div class="w3-top">
            <div class="w3-bar w3-theme-d5">
                <span class="w3-bar-item"># NeoAdzan v<?php echo $version;?></span>
                <button onclick="document.getElementById('id01').style.display='block'" class="w3-bar-item w3-button">Login</button>
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Themes</button>
                    <div class="w3-dropdown-content w3-white w3-card-4" id="theme">
                        <?php
                        $color=array("black","brown","pink","orange","amber","lime","green","teal","purple","indigo","blue","cyan");
                        foreach($color as $clr){
                            echo "<a href='#' class='w3-bar-item w3-button w3-{$clr} color' data-value='{$clr}'> </a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="w3-container">
        <div class="w3-card-4">
            <h2>&nbsp;</h2>
            <div class="w3-panel w3-bar w3-theme-d3">
                <h3 class="w3-theme-d3"><?php echo "{$app_name} v {$version}";?></h3>
            </div>
            <div class="w3-container">
                <div class="w3-row">
                    <div class="w3-col m12 w3-padding" id="msg_box"></div>
                    <div class="w3-col m6 w3-padding">
                        <label class="w3-col s6 m3">Pilih Provinsi</label>
                        <div class="w3-col s6 m3">
                            <select name="prop" id="prop" class="w3-select w3-hover-theme slcProv" readonly>
                                <option value="">Pilih Provinsi</option>
                                <?php
                                $query=$db->prepare("SELECT kode,nama FROM {$dbtable} WHERE CHAR_LENGTH(kode)=2 ORDER BY nama");
                                $query->execute();
                                while ($data=$query->fetchObject()){
                                    echo '<option value="'.$data->kode.'"'.($data->kode=='31'?' selected':'').'>'.$data->nama.'</option>';
                                }
                              ?>
                            <select>
                        </div>
                    </div>
                    <div class="w3-col m6 w3-padding" id="kab_box">
                        <label class="w3-col s6 m3">Pilih Kota/Kab</label>
                      <div class="w3-col s6 m3">
                        <select name="kota" id="kota" class="w3-select w3-hover-theme slcKab" readonly>
                          <option value="">Pilih Kota</option>
                          <?php
                          $query=$db->prepare("SELECT kode,nama FROM {$dbtable} WHERE CHAR_LENGTH(kode)=5 AND kode LIKE '31.%' ORDER BY nama");
                          $query->execute();
                          while ($data=$query->fetchObject()){
                            echo '<option value="'.$data->kode.'"'.($data->kode=='31.71'?' selected':'').'>'.$data->nama.'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                </div>
            </div>
            <div id="preload" class="w3-bar w3-center"><img src="img/preload.svg"></div>
            <div id="adzan_box" class="w3-responsive">
                <table class='w3-table'>
                    <thead>
                        <tr>
                            <th class="w3-theme-d3 c" colspan="8">
                                <h3>Jadwal Waktu Shalat</h3>
                            </th>
                        </tr>
                        <tr>
                            <th class="w3-theme-d2 c" colspan="8">
                                <span id='prev' class='w3-col s2 m4'>
                                    <div class="w3-btn-group">
                                        <button class="w3-btn" id='prevYear' title='previous Year'><i class='fa fa-angle-double-left'></i></button>
                                        <button class="w3-btn" id='prevMonth' title='previous Month'><i class='fa fa-angle-left'></i></button>
                                    </div>
                                </span>
                                <span id='periode' class='w3-col s8 m4'><?php echo $neoadzan->periode;?></span>
                                <span id='next' class='w3-col s2 m4'>
                                    <div class="w3-btn-group">
                                        <button class="w3-btn" id='nextMonth' title='next Month'><i class='fa fa-angle-right'></i></button>
                                        <button class="w3-btn" id='nextYear' title='next Year'><i class='fa fa-angle-double-right'></i></button>
                                    </div>
                                </span>
                                <span id='rentang' class='w3-col'><?php echo $neoadzan->rentang;?></span>
                                <input type='hidden' id='m' value='<?php echo date('n');?>'>
                                <input type='hidden' id='y' value='<?php echo date('Y');?>'>
                            </th>
                        </tr>
                        <tr>
                            <th class="w3-theme-d1" colspan="8">
                                <b>
                                <span class="w3-col m6 c"><span class='skab'>Kota Adm. Jakarta Pusat,</span><span class='sprov'> Provinsi DKI Jakarta</span></span>
                                <span class="w3-col m6 c">
                                (<span class='slat'><?php echo $neoadzan->dms(6.17501);?> LS</span>
                                 <span class='slng'><?php echo $neoadzan->dms(106.820497);?> BT</span>
                                 GMT<span class='stz'>+7</span>)</span></b>
                            </th>
                        </tr>
                        <tr class="w3-theme-l1">
                            <th>Tgl Masehi</th>
                            <th>Tgl Hijriah</th>
                            <th class='w3-hide-small'>Imsak</th>
                            <th>Shubuh</th>
                            <th>Dhuhur</th>
                            <th>Ashar</th>
                            <th>Maghrib</th>
                            <th>Isya'</th>
                        </tr>
                    </thead>
                    <tbody id='sch'>
                    <?php echo $sch;?>
                    </tbody>
                </table>
                <input type='hidden' id='direc' value='295'>
            </div>
            <div class="w3-theme-d5 w3-padding">source code : <a href='https://github.com/cahyadsn/wilayah'>https://github.com/cahyadsn/neoadzan</a></div>
        </div>
        <h1 class='w3-padding'>&nbsp;</h1>
        <h2></h2>
        </div>
            <div id="id01" class="w3-modal">
                <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
                  <div class="w3-center w3-theme-d1 w3-padding-16"><br>
                    <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Close Modal">&times;</span>
                  </div>
                  <div class="w3-container">
                    <div class="w3-section">
                      <label><b>Username</b></label>
                      <input class="w3-input w3-border w3-margin-bottom" type="text" placeholder="Enter Username" name="usrname" required autocomplete="off">
                      <label><b>Password</b></label>
                      <input class="w3-input w3-border" type="password" placeholder="Enter Password" name="psw" required autocomplete="off">
                    </div>
                  </div>
                  <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                    <button onclick="document.getElementById('id01').style.display='none'" type="button" class="w3-button w3-red">Cancel</button>
                    <button class="w3-button w3-theme-d3" type="submit">Login</button>
                  </div>
                </div>
            </div>
            <div id="id02" class="w3-modal">
                <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
                  <div class="w3-center w3-theme-d1 w3-padding-16"><br>
                    <span onclick="document.getElementById('id02').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Close Modal">&times;</span>
                  </div>
                  <div class="w3-container">
                    <div class="w3-section">
                      <h2>Sorry for your inconvinience, this feature still under construction</h2>
                    </div>
                  </div>
                  <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                    <button onclick="document.getElementById('id02').style.display='none'" type="button" class="w3-button w3-red">Close</button>
                  </div>
                </div>
            </div>
        </div>
        <div class="w3-bottom">
          <div class="w3-bar w3-theme-d4 w3-center w3-padding">
              NeoAdzan v<?php echo $version;?> copyright &copy; 2018<?php echo (date('Y')>2018?date('-Y'):'');?> by <a href='mailto:cahyadsn@gmail.com'>cahya dsn</a><br />
          </div>
      </div>
    </body>
    <script src="js/jquery.min.js"></script>
    <script src="inc/neoadzan_js.php?v=<?php echo $_SESSION['ver'];?>"></script>
</html>
