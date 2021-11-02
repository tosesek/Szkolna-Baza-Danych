<?php
  $user=""
  $password=""
  $_DB = mysqli_connect("localhost", $user, $password, "panel");
  mysqli_query($_DB, "SET NAMES utf8");
?>