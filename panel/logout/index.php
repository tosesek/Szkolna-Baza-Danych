<?php
  session_start();
  session_unset();

  $type = $_REQUEST['type'];

  $_SESSION['logout_type'] = $type;

  header('location: /panel');
?>