<?php
  require_once($_SERVER["DOCUMENT_ROOT"].'/panel/modules/functions.php');
?>

<div class="container">
  <span class="title">Podgląd wiadomości</span>
  <span class="content">
    <br>
    <div style="background: #fff; padding: 35px; overflow: hidden; overflow-y: auto; max-height: 470px; box-shadow: 0px 0px 15px #00000060">
      <?=sGenerateMailContent($_REQUEST['title'], $_REQUEST['fname'], nl2br($_REQUEST['content']))?>
    </div>
  </span>
  <div class="buttons"><button onclick="CloseDialog(<?=$_REQUEST['dialogid']?>)">Zamknij</button></div>
</div>
