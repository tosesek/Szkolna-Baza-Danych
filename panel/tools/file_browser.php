<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeAdmin();
  $directory = ($_GET['dir'] ?? "/");
  $directory = ($directory == "" ? "/" : $directory);
  $directory = (strpos($directory, "..")!==false ? "/" : $directory);
  $directory = ($directory=="." ? "/" : $directory);
  $fulldir = $_SERVER['DOCUMENT_ROOT'].$directory;
?>

<div class="page_generic">
  <div class="content">
    <h1>Przeglądarka plików</h1>
    <h5>
      Ta strona pozwala na przeglądanie plików, które zostały opublikowane przez innych użytkowników systemu.<br>
      Nie masz możliwości przenoszenia, kopiowania ani usuwania tych plików.
    </h5>
    <br>

    <?php
      $curpath = substr($fulldir, 14, strlen($fulldir)-14);
    ?>
    <b>Aktualna ścieżka:</b> <?=$curpath?>

    <div class="admin-tools-file-browser">      
      <div class="header">
        <span class="item"></span>
        <span class="item">Nazwa</span>
        <span class="item">Rozmiar</span>
        <span class="item" align-right>Data utworzenia</span>
        <span class="item" align-right>Data modyfikacji</span>
      </div>
      <div class="list">
        <?php

          $pd_explode = explode("/", $fulldir);
          $pd_toremove = $pd_explode[sizeof($pd_explode)-2]."/";
          $pd_string = substr($fulldir, 0, -strlen($pd_toremove));

          // $pd_string = str_replace("/".$pd_explode[sizeof($pd_explode)-2], "", $fulldir);
          if($pd_string!="/var/www/" && $directory!="/") {
            // $new_dir = str_replace("/var/www/html/", "", $pd_string);
            $new_dir = substr($pd_string, 14, strlen($pd_string)-14);
            
            ?>
              <div class="entry" parentdirectory onclick="location.assign('?dir=<?=$new_dir?>')">                
                <span class="item"><svg-icon src="/panel/modules/images/icons/parent_directory.svg"></svg-icon></span>
                <span class="item">FOLDER NADRZĘDNY</span>
                <span class="item"></span>
                <span class="item"></span>
                <span class="item"></span>
              </div>
            <?php
          }
        ?>

        <?php
          $scndir = scandir($fulldir);
          $scndir = array_diff($scndir, array(".", "..", "panel"));

          $directories = array();
          $files = array();

          foreach ($scndir as $key => $value) {
            if(is_dir($fulldir.'/'.$value)) {
              if (substr($value, 0, 1) == ".") continue;
              $directories[] = $value;
            }
            else {
              if($directory=="/") continue;
              $files[] = $value;
            }
          }

          foreach ($directories as $key => $value) {
            ?>
              <div class="entry" directory onclick="location.assign('?dir=<?=$directory.$value?>/')">
              <span class="item"><svg-icon src="/panel/modules/images/icons/directory.svg"></svg-icon></span>
                <span class="item"><?=$value?></span>
                <span class="item"></span>
                <span class="item" align-right><?=sFormatDate(filemtime($fulldir.'/'.$value), true)?></span>
                <span class="item" align-right><?=sFormatDate(filectime($fulldir.'/'.$value), true)?></span>
              </div>
            <?php
          }
          foreach ($files as $key => $value) {
            $extension_arr = explode(".", $value);
            $extension = $extension_arr[sizeof($extension_arr)-1];
            $iconname = ""; $allowview = false;
            switch($extension) {
              case "txt": $iconname = "text_file"; $allowview = true; break;
              case "json": $iconname = "file_json"; $allowview = true; break;
              case "php": $iconname = "file_php"; $allowview = false; break;
              case "xml": $iconname = "file_xml"; $allowview = true; break;
              case "sql": $iconname = "database"; $allowview = false; break;
              case "ttf": $iconname = "font"; $allowview = false; break;
              case "eot": $iconname = "font"; $allowview = false; break;
              case "woff": $iconname = "font"; $allowview = false; break;
              case "woff2": $iconname = "font"; $allowview = false; break;
              case "html": $iconname = "file_code"; $allowview = true; break;
              case "js": $iconname = "javascript"; $allowview = true; break;
              case "css": $iconname = "file_css"; $allowview = true; break;
              case "scss": $iconname = "file_css"; $allowview = true; break;
              case "png": $iconname = "image_file"; $allowview = true; break;
              case "gif": $iconname = "image_file"; $allowview = true; break;
              case "jpg": $iconname = "image_file"; $allowview = true; break;
              case "jpeg": $iconname = "image_file"; $allowview = true; break;
              case "ico": $iconname = "image_file"; $allowview = true; break;
              case "svg": $iconname = "file_svg"; $allowview = true; break;
              case "mp4": $iconname = "video_file"; $allowview = true; break;
              case "mov": $iconname = "video_file"; $allowview = false; break;
              case "zip": $iconname = "file_archive"; $allowview = false; break;
              case "rar": $iconname = "file_archive"; $allowview = false; break;
              case "7z": $iconname = "file_archive"; $allowview = false; break;
              case "gz": $iconname = "file_archive"; $allowview = false; break;
              case "tar": $iconname = "file_archive"; $allowview = false; break;
              case "htaccess": $iconname = "hidden_file"; $allowview = false; break;
              case "htpasswd": $iconname = "hidden_file"; $allowview = false; break;
              default: $iconname = "file"; $allowview = false; break;
            }

            ?>
              <div class="entry" file <?=($allowview ? 'onclick="window.open(\'https://sbd.zst.pila.pl'.$directory.$value.'\')"' : '')?>>
              <span class="item"><svg-icon src="/panel/modules/images/icons/<?=$iconname?>.svg"></svg-icon></span>
                <span class="item"><?=$value?></span>
                <span class="item"><?=sFormatSizeUnits(filesize($fulldir.'/'.$value))?></span>
                <span class="item" align-right><?=sFormatDate(filectime($fulldir.'/'.$value), true)?></span>
                <span class="item" align-right><?=sFormatDate(filemtime($fulldir.'/'.$value), true)?></span>
              </div>
            <?php
          }

          if(!sizeof($directories) && !sizeof($files)) {
            ?>
              <div class="centerinfo">
                <div class="icon"><svg-icon src="/panel/modules/images/icons/folder_open.svg"></svg-icon></div>
                <span class="title">Brak plików w tym folderze</span>
              </div>
            <?php
          }
        ?>
      </div>
    </div>
  </div>
</div>