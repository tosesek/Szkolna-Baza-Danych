<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeAdmin();
?>

<?php
  if(!isset($_GET['date']) || $_GET['date']=="") {
    ?>
      <div class="page_generic">
        <div class="content">
          <h1>Dziennik serwera</h1>
          Wybierz dzień, z którego chcesz wyświetlić wydarzenia<br>
          <br>
          <button dialog-button nomargin primary onclick="location.assign('/panel/tools/')">Wróć do narzędzi administratora</button>
          <hr>
          <br>
          <div style="position: relative; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); grid-gap: 10px;">
            <?php
              $logs = scandir($_SERVER['DOCUMENT_ROOT'].'/panel/logs', 1);
              $logs = array_diff($logs, array("..", ".", "logs_here", ".htaccess", "database_backup.sql"));

              foreach ($logs as $key => $value) {
                $date_string = str_replace(".txt", "", $value);
                $date = strtotime($date_string);
                ?>
                  <div style="position: relative; display: block; padding: 10px; background: #ffffff0a; text-align: center; font: 18px YouTube; border-radius: 3px; box-shadow: 0px 1px 3px #000000a0" onclick="location.assign('?date=<?=$date_string?>')">
                    <span><?=sFormatDate($date, false, true)?></span>
                  </div>
                <?php
              }
            ?>
          </div>
        </div>
      </div>
    <?php
  }
  else {
    $date = strtotime($_GET['date']);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ?>
      <div class="page_generic">
        <div class="content">
          <h1>Dziennik serwera</h1>
          Oto wydarzenia, które odbyły się w systemie w dniu <b><?=sFormatDate($date)?></b><br>
          <br>
          <button dialog-button nomargin primary onclick="location.assign('/panel/tools/logs.php')">Wybierz inny dzień</button>
          <hr>
          <br>
          <div class="admin-tools-logs-list">
            <?php
              $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'panel/logs/'.$_GET['date'].'.txt');
              $lines = explode("\n", $content);
              foreach ($lines as $key => $line) {
                if( !isset($line) || $line=="" ) continue;
                $data = json_decode($line, true);
                $time = explode(":", $data['time']);
                if(@$data['time']) {
                  $time[0] = ($time[0] < 10 ? "0".intval($time[0]) : $time[0]);
                  $time[1] = ($time[1] < 10 ? "0".intval($time[1]) : $time[1]);
                  $time[2] = ($time[2] < 10 ? "0".intval($time[2]) : $time[2]);
                  $newtime = "{$time[0]}:{$time[1]}:{$time[2]}<br>";
                }
                else {
                  $newtime = $data['time'];
                }                

                $user_string = "";
                if(is_numeric($data['user'])) {
                  // ID
                  $user_string = sGetFullName($data['user']);
                }
                elseif($data['user']=="SERVER") {
                  // SERVER
                  $user_string = "Serwer";
                }
                else {
                  // INNE
                  $user_string = $data['user'];
                  $query = mysqli_query($_DB, "SELECT * FROM users WHERE `email`='$user_string'");
                  if(mysqli_num_rows($query)) {
                    $f = mysqli_fetch_assoc($query);
                    $user_string = $f['first_name']." ".$f['last_name'];
                  }
                }

                $action = Translate("server_action_".$data['action'].'_'.$data['status'], @$data['data']);

                ?>
                  <div class="admin-tools-logs-list-item">
                    <div class="firstline">
                      <span class="time"><?=$newtime?></span>
                      <span class="user"><?=$user_string?></span>
                      <span class="action"><?=$action?></span>
                    </div>
                  </div>
                <?php
              }
            ?>
          </div>

          
        </div>
      </div>
    <?php
  }
?>

