<?php
if(isset($_POST['color'])){
 session_start();
 $_SESSION['c']=$_POST['color'];
}