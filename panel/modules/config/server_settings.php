<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/mysql.php';
  $q="SELECT * FROM settings";
  $r=mysqli_query($_DB, $q);
  while($s=mysqli_fetch_assoc($r)){
    $val = ($s['value']=='true' ? true : ($s['value']=='false' ? false : $s['value']));
    $_SETTINGS[$s['name']]=$val;
  }
?>