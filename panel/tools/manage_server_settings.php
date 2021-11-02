<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/config/server_settings.php';
  cMustBeLoggedIn();
  cMustBeAdmin();
?>

<div class="page_generic">
  <div class="content">
    <h1>Zmień ustawienia serwera</h1>
    Zmiany zapisywane są automatycznie po zmianie wartości.<br>
    <hr>

    <div class="admin-tools-server-settings">

      <div class="item">
        <span class="name"><?=Translate("server_settings_user_max_db")?></span>
        <div class="input"><input class="changable" type="number" value="<?=$_SETTINGS['user_max_db']?>" id="setting-user_max_db"></div>
      </div>

      <div class="item">
        <span class="name"><?=Translate("server_settings_admin_max_db")?></span>
        <div class="input"><input class="changable" type="number" value="<?=$_SETTINGS['admin_max_db']?>" id="setting-admin_max_db"></div>
      </div>

      <div class="item">
        <span class="name"><?=Translate("server_settings_open_register_users")?></span>
        <div class="input">
          <select class="changable" id="setting-open_register_users">
            <option <?=($_SETTINGS['open_register_users']=='true' ? 'selected' : '')?> value="bool-true">Zezwalaj</option>
            <option <?=($_SETTINGS['open_register_users']=='false' ? 'selected' : '')?> value="bool-false">Odmów</option>
          </select>
        </div>
      </div>

      <div class="item">
        <span class="name"><?=Translate("server_settings_auto_remove_data")?></span>
        <div class="input">
          <select class="changable" id="setting-auto_remove_data">
            <option <?=($_SETTINGS['auto_remove_data']=='true' ? 'selected' : '')?> value="bool-true">Tak</option>
            <option <?=($_SETTINGS['auto_remove_data']=='false' ? 'selected' : '')?> value="bool-false">Nie</option>
          </select>
        </div>
      </div>

      <?php
        $current_month = date("m");
        $current_year = date("Y");

        if($current_month >= 9) $year = date("Y")+1; else $year = date("Y");

        $mindate = "$year-07-01";
        $maxdate = "$year-09-30";

        $currentsetting = $_SETTINGS['date_of_auto_remove_data'].'.'.$year;
        $currentsetting = explode(".", $currentsetting);
        $currentsetting = $currentsetting[2].'-'.$currentsetting[1].'-'.$currentsetting[0];
      ?>

      <div class="item">
        <span class="name"><?=Translate("server_settings_date_of_auto_remove_data")?></span>
        <div class="input"><input class="changable" type="date" min="<?=$mindate?>" max="<?=$maxdate?>" value="<?=$currentsetting?>" id="setting-date_of_auto_remove_data" title="Datę na przyszły rok można ustawiać od początku września"></div>
      </div>


    <?php
    /*
      $q="SELECT * FROM settings";
      $r=mysqli_query($_DB, $q);
      while($s=mysqli_fetch_assoc($r)){
        if($s['name']=="user_id_enabled_auto_remove_data") {
          continue;
        }
        ?>
          <div class="item">
            <span class="name"><?=Translate("server_settings_".$s['name'])?></span>
            <div class="input"><input type="text" value="<?=$s['value']?>" id="<?=$s['name']?>"></div>
          </div>
        <?php
      }
      */
    ?>
    </div>
    
    
    <!-- <div align-right>
      <button onclick="location.assign('/panel/tools')">Anuluj</button>
      <button primary onclick="SaveServerSettings()">Zatwierdź zmiany</button>
    </div> -->

    <script>

      $(document).ready(function() {
        $('.changable').each(function() {
          $(this).change(function() {
            var settingname = $(this).attr('id').replace('setting-', '');
            var settingvalue = ($(this).val()==='bool-true' ? String('true') : $(this).val()==='bool-false' ? String('false') : $(this).val());
            // console.log(settingname, settingvalue);
            $.post("/panel/modules/requests.php", {
              'action': 'ChangeServerSettings',
              'setting': settingname,
              'value': settingvalue,
              'type': $(this).attr('type') || 'text'
            });
          });
        });
      });

      // function SaveServerSettings() {
      //   let user_db = $('#user_max_db');
      //   let admin_db = $('#admin_max_db');
      //   let open_reg = $('#open_register_users');
      //   let autoremove = $('#auto_remove_data');
      //   let autoremovedate = $('#date_of_auto_remove_data');
      //   let localok=true;

      //   if(!ValidateInput(user_db)) localok = false;
      //   if(!ValidateInput(admin_db)) localok = false;
      //   if(!ValidateInput(open_reg)) localok = false;
      //   if(!ValidateInput(autoremove)) localok = false;

      //   if(!IsNumber(user_db.val())) {localok = false; SetInputError(user_db);} else { SetInputOk(user_db); }
      //   if(!IsNumber(admin_db.val())) {localok = false; SetInputError(admin_db);} else { SetInputOk(admin_db); }

      //   if(localok) {
      //     OpenGenericDialog("Uwaga!", "Masz zamiar zmienić najważniejsze ustawienia systemu Szkolnej Bazy Danych<br><br>"+
      //     "Zmiana tych ustawień może zakłucić pracę uczniom, przy ich projektach.<br><br>"+
      //     "Jesteś pewien, że chcesz to zrobić?",
      //     [
      //       {
      //         "text": "Anuluj",
      //         "callback": function(did) {
      //           CloseDialog(did);
      //         }
      //       },
      //       {
      //         "text": "Zatwierdź",
      //         "primary": true,
      //         "callback": function(did) {
      //           var blocking = OpenBlockingDialog("Czekaj...");
      //           $.post("/panel/modules/requests.php", {
      //             'action': 'ChangeServerSettings',
      //             'user_db':user_db.val(),
      //             'admin_db':admin_db.val(),
      //             'open_reg':open_reg.val(),
      //             'autoremove': autoremove.val(),
      //             'autoremovedate':autoremovedate.val()
      //           }, function(data) {
      //             CloseDialog(blocking);
      //             CloseDialog(did);
      //             if(data=='success') {
      //               OpenGenericDialog("Sukces", "Ustawienia zostały zapisane!", 
      //               [{
      //                 "text": "Ok",
      //                 "callback": function(did){
      //                   CloseDialog(did);
      //                   location.assign('/panel/tools');
      //                 }
      //               }]);
      //             }
      //             else {
      //               OpenGenericDialog("Wystąpił błąd", 'Nie udało się zapisać ustawień.<br> Skontaktuj się z programistą');
      //             }
      //           });
      //         }
      //       }
      //     ]);
      //   }

        
      // }
    </script>
  </div>
</div>