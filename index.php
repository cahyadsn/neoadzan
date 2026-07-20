<?php
/*
================================================================================
 *  BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : index.php
PURPOSE      : main page application
AUTHOR       : CAHYA DSN
CREATED DATE : 2018-01-25
UPDATED DATE : 2026-06-11 15:04:15
DEMO SITE    : http://neoadzan.cahyadsn.com
SOURCE CODE  : https://github.com/cahyadsn/neoadzan
================================================================================*/
session_start();

// Theme color: session > cookie > GET > default
$c = isset($_SESSION['c']) ? $_SESSION['c']
   : (isset($_COOKIE['neoadzan_theme']) ? $_COOKIE['neoadzan_theme']
   : (isset($_GET['c']) ? $_GET['c'] : 'indigo'));

// Dark/light mode: session > cookie > default
$mode = isset($_SESSION['mode']) ? $_SESSION['mode']
      : (isset($_COOKIE['neoadzan_mode']) ? $_COOKIE['neoadzan_mode'] : 'light');

// Whitelist validation
$allowed_colors = ['black','brown','pink','orange','amber','lime','green','teal','purple','indigo','blue','cyan'];
$allowed_modes  = ['light','dark'];
if (!in_array($c, $allowed_colors)) $c = 'indigo';
if (!in_array($mode, $allowed_modes)) $mode = 'light';

// Sync to session
$_SESSION['c'] = $c;
$_SESSION['mode'] = $mode;

// Sync to cookie (1 year expiry)
$cookie_opts = [
    'expires'  => time() + 60*60*24*365,
    'path'     => '/',
    'secure'   => false,
    'httponly'  => false,
    'samesite'  => 'Lax'
];
setcookie('neoadzan_theme', $c, $cookie_opts);
setcookie('neoadzan_mode', $mode, $cookie_opts);

define("_AUTHOR","cahyadsn");
$_SESSION['author']='cahyadsn';
$_SESSION['ver']=sha1(rand());
include 'inc/db.php';
include 'inc/NeoAdzan.php';
include 'inc/Cache.php';

$cache = new Cache();
$cache_key = 'initial_schedule_' . date('Y-n');
$cached_data = $cache->get($cache_key);

if ($cached_data) {
    $sch = $cached_data['sch'];
    $periode = $cached_data['periode'];
    $rentang = $cached_data['rentang'];
    $neoadzan=new NeoAdzan();
} else {
    $neoadzan=new NeoAdzan();
    $neoadzan->setLatLng(-6.17501,106.820497);
    $neoadzan->setTimeZone(7);
    $sch=$neoadzan->getSchedule(date('Y'),date('n'));
    $periode = $neoadzan->periode;
    $rentang = $neoadzan->rentang;
    $cache->set($cache_key, [
        'sch' => $sch,
        'periode' => $periode,
        'rentang' => $rentang
    ]);
}
$version='2.0.0';
$app_name='NeoAdzan!';
?>
<!DOCTYPE html>
<html lang='en' data-theme="<?php echo $c;?>" data-mode="<?php echo $mode;?>">
    <head>
    <title><?php echo "{$app_name} v {$version}";?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
    <meta name="author" content="Cahya DSN" />
    <meta name="description" content="<?php echo "{$app_name} v {$version}";?> created by cahya dsn, Jadwal Waktu Shalat, dalam bahasa pemrograman PHP dan database MySQL" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/modern.css?v=<?php echo filemtime('css/modern.css');?>">
    <link rel="stylesheet" href="css/neoadzan_css.php?v=<?php echo filemtime('css/neoadzan_css.php');?>">
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><?php echo "{$app_name} v {$version}";?></h2>
                    <div class="nav-buttons">
 					    <div class="theme-selector">
                            <?php
                            $colors=array(
                            "black"=>"#000000",
                            "brown"=>"#795548",
                            "pink"=>"#e91e63",
                            "orange"=>"#ff9800",
                            "amber"=>"#ffc107",
                            "lime"=>"#cddc39",
                            "green"=>"#4caf50",
                            "teal"=>"#009688",
                            "purple"=>"#9c27b0",
                            "indigo"=>"#3f51b5",
                            "blue"=>"#2196f3",
                            "cyan"=>"#00bcd4"
                        );
                        foreach($colors as $clr => $hex){
                            echo "<div class='theme-dot' style='background-color: {$hex};' data-theme='{$clr}' data-value='{$clr}' title='{$clr}'></div>";
                        }
                        ?>
						</div>
						<div id="themeToggle" class="theme-toggle" title="Toggle Dark/Light Mode">
                            <i class="fa fa-moon-o"></i>
                        </div>
                    </div>
                </div>
                
                <div id="msg_box" class="msg-box"></div>

                <div class="grid">
                    <div class="form-group">
                        <label for="prop">Pilih Provinsi</label>
                        <select name="prop" id="prop" class="slcProv">
                            <option value="">Pilih Provinsi</option>
                            <?php
                            $prov_cache_key = 'index_provinces';
                            $provinces = $cache->get($prov_cache_key, 2592000); // 30 days
                            if (!$provinces) {
                                $provinces = [];
                                $query=$db->prepare("SELECT kode,nama FROM {$dbtable} WHERE CHAR_LENGTH(kode)=2 ORDER BY nama");
                                $query->execute();
                                while ($data=$query->fetchObject()){
                                    $provinces[] = ['kode' => $data->kode, 'nama' => $data->nama];
                                }
                                $cache->set($prov_cache_key, $provinces);
                            }
                            foreach ($provinces as $prov) {
                                echo '<option value="'.$prov['kode'].'"'.($prov['kode']=='31'?' selected':'').'>'.$prov['nama'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" id="kab_box">
                        <label for="kota">Pilih Kota/Kab</label>
                        <select name="kota" id="kota" class="slcKab">
                            <option value="">Pilih Kota</option>
                            <?php
                            $dist_cache_key = 'index_districts_31';
                            $districts = $cache->get($dist_cache_key, 2592000); // 30 days
                            if (!$districts) {
                                $districts = [];
                                $query=$db->prepare("SELECT kode,nama FROM {$dbtable} WHERE CHAR_LENGTH(kode)=5 AND kode LIKE '31.%' ORDER BY nama");
                                $query->execute();
                                while ($data=$query->fetchObject()){
                                    $districts[] = ['kode' => $data->kode, 'nama' => $data->nama];
                                }
                                $cache->set($dist_cache_key, $districts);
                            }
                            foreach ($districts as $dist) {
                                echo '<option value="'.$dist['kode'].'"'.($dist['kode']=='31.71'?' selected':'').'>'.$dist['nama'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div id="preload" style="display:none;"><img src="img/preload.svg"></div>

                <div id="adzan_box">
                    <div class="period-info">
                        <h2 id='periode'><?php echo $periode;?></h2>
                        <p id='rentang'><?php echo $rentang;?></p>
                    </div>

                    <div class="controls">
                        <button class="btn btn-primary" id='prevYear' title='previous Year'><i class='fa fa-angle-double-left'></i></button>
                        <button class="btn btn-primary" id='prevMonth' title='previous Month'><i class='fa fa-angle-left'></i></button>
                        <button class="btn btn-primary" id='nextMonth' title='next Month'><i class='fa fa-angle-right'></i></button>
                        <button class="btn btn-primary" id='nextYear' title='next Year'><i class='fa fa-angle-double-right'></i></button>
                    </div>

                    <input type='hidden' id='m' value='<?php echo date('n');?>'>
                    <input type='hidden' id='y' value='<?php echo date('Y');?>'>

                    <div class="location-info">
                        <b>
                        <span class='skab'>Kota Adm. Jakarta Pusat,</span> <span class='sprov'>Provinsi DKI Jakarta</span><br>
                        (<span class='slat'><?php echo $neoadzan->dms(6.17501);?> LS</span>, 
                         <span class='slng'><?php echo $neoadzan->dms(106.820497);?> BT</span>
                         GMT <span class='stz'>+7</span>)
                        </b>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tgl Masehi</th>
                                    <th>Tgl Hijriah</th>
                                    <th>Imsak</th>
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
                    </div>
                </div>
            </div>

            <div class="footer">
                source code : <a href='https://github.com/cahyadsn/neoadzan'>https://github.com/cahyadsn/neoadzan</a><br>
                NeoAdzan v<?php echo $version;?> copyright &copy; 2018<?php echo (date('Y')>2018?date('-Y'):'');?> by <a href='mailto:cahyadsn@gmail.com'>cahya dsn</a>
            </div>
        </div>

        <!-- Modals -->
        <div id="id01" class="modal-overlay" onclick="if(event.target==this)this.style.display='none'">
            <div class="modal">
                <span onclick="document.getElementById('id01').style.display='none'" class="modal-close">&times;</span>
                <div class="card-header">
                    <h3 class="card-title">Login</h3>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" placeholder="Enter Username" name="usrname" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" placeholder="Enter Password" name="psw" required autocomplete="off">
                </div>
                <div class="nav-buttons" style="justify-content: flex-end; margin-top: 1rem;">
                    <button onclick="document.getElementById('id01').style.display='none'" class="btn" style="background: #eee;">Cancel</button>
                    <button class="btn btn-primary" type="submit">Login</button>
                </div>
            </div>
        </div>

        <script src="inc/neoadzan_js.php?v=<?php echo filemtime('inc/neoadzan_js.php');?>"></script>
    </body>
</html>
