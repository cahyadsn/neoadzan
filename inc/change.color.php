<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : change.color.php
PURPOSE      : handle theme color & mode changes, persist via session + cookie
AUTHOR       : CAHYA DSN
CREATED DATE : 2018-01-25
UPDATED DATE : 2026-07-02 10:30:00
DEMO SITE    : http://neoadzan.cahyadsn.com
SOURCE CODE  : https://github.com/cahyadsn/neoadzan
================================================================================*/
session_start();

$allowed_colors = ['black','brown','pink','orange','amber','lime','green','teal','purple','indigo','blue','cyan'];
$allowed_modes  = ['light','dark'];
$cookie_opts    = [
    'expires'  => time() + 60*60*24*365, // 1 year
    'path'     => '/',
    'secure'   => false,
    'httponly'  => false,
    'samesite'  => 'Lax'
];

// Handle theme color change
if (isset($_POST['color'])) {
    $color = $_POST['color'];
    if (in_array($color, $allowed_colors)) {
        $_SESSION['c'] = $color;
        setcookie('neoadzan_theme', $color, $cookie_opts);
    }
}

// Handle dark/light mode change
if (isset($_POST['mode'])) {
    $mode = $_POST['mode'];
    if (in_array($mode, $allowed_modes)) {
        $_SESSION['mode'] = $mode;
        setcookie('neoadzan_mode', $mode, $cookie_opts);
    }
}