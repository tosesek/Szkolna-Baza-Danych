<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  ?>
<div class="page_generic">
  <div class="content">
    
    <h1>Ustawienia</h1>
    <h4>ZMIANA HASŁA</h4>
    <input autocomplete="current-password" placeholder="Aktualne hasło" type="password" id="current"><br><br>
    <input autocomplete="new-password" placeholder="Nowe hasło" type="password" id="new1"><br>
    <input autocomplete="new-password" placeholder="Potwierdź hasło" type="password" id="new2"><br><br>
    <button nomargin type="button" primary onclick="savePassword(<?=$_SESSION['uuid']?>)">Zatwierdź</button>
    <br>
    <br>
    <h4>Twoja strona</h4>
    <span>Podczas tworzenia konta w systemie, utworzono również własny katalog WWW.<br>
    Oto adres do Twojej strony:</span><br>
    <a href="https://sbd.zst.pila.pl/<?=strtolower(sGetName())?>">https://sbd.zst.pila.pl/<?=strtolower(sGetName())?></a>
    <br>
    <br>
    <h4>Konto FTP</h4>
    <h5>UWAGA!</h5>
    <span>Zachęcamy do używania programu <a href="https://filezilla-project.org/download.php">FileZilla</a> do bezproblemowego korzystania z serwera FTP.<br>
    Poniżej wyświetlono dane wymagane do połączenia się z serwerem FTP:<br><br>
    </span>
  
    <b>Adres IP:</b> 
    <div class="tooltip">
      <span class="tooltiptext" id="Tooltip1">Kliknij aby skopiować</span>
      <span selectable onclick="CopyToClipboard(1)" id="copy1">sbd.zst.pila.pl</span>
    </div><br>
  
    <b>Nazwa użytkownika:</b> 
    <div class="tooltip">
      <span class="tooltiptext" id="Tooltip2">Kliknij aby skopiować</span>
      <span selectable onclick="CopyToClipboard(2)" id="copy2"><?=strtolower(sGetName())?></span>
    </div><br>
  
    <b>Hasło</b> jest takie samo jak hasło to Twojego konta w systemie<br>
    
    <b>Port:</b> 
    <div class="tooltip">
      <span class="tooltiptext" id="Tooltip3">Kliknij aby skopiować</span>
      <span selectable onclick="CopyToClipboard(3)" id="copy3">203</span>
    </div><br>
    
    <br>
    
    <span>Jeżeli chcesz korzystać z przeglądarki lub systemowego eksploratora plików do połączenia się z serwerem, otwórz ten link:<br>
      <div class="tooltip">
        <span class="tooltiptext" id="Tooltip4">Kliknij aby skopiować</span>
        <span selectable onclick="CopyToClipboard(4)" id="copy4">ftp://sbd.zst.pila.pl:203</span>
      </div><br>
    </span>

    <br>
    <br>
    <div style="width:50%;margin:0;float:left">
      <h4>MySQL</h4>
      Kliknij <a href="/phpmyadmin">w ten odnośnik</a>, aby przejść do phpMyAdmin<br>
      <b>Nazwa użytkownika:</b> 
      <div class="tooltip">
        <span class="tooltiptext" id="Tooltip5">Kliknij aby skopiować</span>
        <span selectable onclick="CopyToClipboard(5)" id="copy5"><?=sGetName()?></span>
      </div><br>

      <b>Host:</b> 
      <div class="tooltip">
        <span class="tooltiptext" id="Tooltip5">Kliknij aby skopiować</span>
        <span selectable onclick="CopyToClipboard(5)" id="copy5">localhost</span>
      </div> 
      lub 
      <div class="tooltip">
        <span class="tooltiptext" id="Tooltip5">Kliknij aby skopiować</span>
        <span selectable onclick="CopyToClipboard(5)" id="copy5">sdb.zst.pila.pl</span>
      </div>
      <br>
      <b>Hasło</b> jest takie samo jak hasło to Twojego konta w systemie<br>
      <br><br>

      
      <button primary nomargin onclick="GiveMeNewDatabase()">Utwórz kolejną bazę danych</button>
      <br><br>
    </div>
    <div style="width:49%;margin:0;float:right;">
      <h3>Twoje bazy danych</h3>
      <br>
      <table cellspacing="0" style="width: 100%" id="databases_list">
        <thead>
          <tr>
            <td style="width: 1px">LP</td>
            <td>Nazwa bazy danych</td>
            <td align-right></td>
          </tr>
        </thead>
        <tbody>
          <?php
            $username = sGetName();
            $db = mysqli_query($_DB, "SHOW DATABASES LIKE '$username%'");
            $r = mysqli_fetch_all($db);
            
            $i = 1;
            
            foreach ($r as $key => $value) {
              ?> <tr dbname="<?=str_replace(sGetName()."_", "", $value[0])?>"><td><?=$i?></td><td><?=$value[0]?></td><td align-right><button dialog-button <?=($i>1 ? 'onclick="RemoveDatabase(\''.str_replace(sGetName()."_", "", $value[0]).'\')" critical' : 'disabled')?>>Usuń bazę</button></td></tr> <?php
              $i++;
            } 
          ?>
        </tbody>
      </table>
    </div>
    <br><br><br><br><br><br><br><br><br><br><br>
  </div>
</div>

<script>
  function RemoveDatabase(dbname) {
    var localok = true;
    if(!ValidateValue(dbname)) localok = false;

    OpenGenericDialog(
      'Usuwanie bazy danych',
      'Czy na pewno chcesz usunąć bazę "<b><?=sGetName()?>_'+dbname+'</b>"?<br>'+
      'To działanie jest nieodwracalne!',
      [
        {
          "text": "Anuluj",
          "callback": function(did) {
            CloseDialog(did);
          }
        },
        {
          "text": "Tak, usuń",
          "primary": true,
          "callback": function(did) {
            var blocking = OpenBlockingDialog('Usuwanie bazy danych');
            CloseDialog(did);
            if(localok) {
              $.post('/panel/modules/requests.php', {
                'action': 'RemoveDatabase',
                'dbname': dbname
              }, function(data) {
                CloseDialog(blocking);
                if(data=='success') {
                  OpenGenericDialog('Usuwanie bazy danych', 'Baza danych "<b><?=sGetName()?>_'+dbname+'</b>" została usunięta pomyślnie!');
                  $('[dbname='+dbname+']').remove();
                }
                else {
                  OpenGenericDialog('Usuwanie bazy danych', 'Nie udało się usunąć bazy danych "<b><?=sGetName()?>_'+dbname+'</b>". Skontaktuj się z administratorem systemu, lub usuń tę bazę poprzez phpMyAdmin');
                }
              });
            }
            else {
              CloseDialog(blocking);
              OpenGenericDialog('Wystąpił błąd', 'Nie można usunąć podanej wybranej bazy danych.<br>Zgłoś ten błąd poprzez <a target="_blank" href="/panel/bugs">narzędzie zgłaszania błędów</a>');
            }
          }
        }
      ]
    );

    
  }

  function savePassword(uuid) {
    let current = MD5($('#current').val());
    let new1 = $('#new1');
    let new2 = $('#new2');
    
    var localok = true;
    
    if(!ValidateInput(new1)) localok = false;
    if(!ValidateInput(new2)) localok = false;
    if(new1.val() != new2.val()) {
      localok = false;
      SetInputError(new2);
      OpenGenericDialog("Nieprawidłowe hasło", "Nowe hasła nie są takie same!", -1);
      return false;
    }

    // Sprawdzanie hasła
    if(!ValidatePassword(new2.val())) { 
      localok = false; 
      SetInputError(new1);
      OpenGenericDialog(
        "Błędne hasło", 
        "Twoje hasło musi spełniać podane wymagania:<br>"+
        "- Musi zawierać przynajmniej jedną cyfrę<br>"+
        "- Musi zawierać przynajmniej jedną dużą literę<br>"+
        "- Musi zawierać przynajmniej jedną małą literę<br>"+
        "- Musi zawierać przynajmniej 8 znaków<br>"+
        "- Nie może zawierać znaków specjalnych (włącznie ze znakami diakrytycznymi)<br>",
        -1
      );
      return false;
    }
    if(!localok) {
      OpenGenericDialog("Błąd", "Nie podano nowego hasła", -1);
      return false;
    }
    else {
      var blocking = OpenBlockingDialog("Ładowanie");
      $.post("/panel/modules/requests.php", {
        'action': 'changePassword',
        'current': current,
        'new': new1.val()
      }, function(data) {
        CloseDialog(blocking);
        if(data=='success'|| data=='mail_send_error') {
          OpenGenericDialog("Sukces", "Hasło do Twojego konta zostało zmienione!", -1);
          // location.assign('/panel');
        }
        else {
          OpenGenericDialog("Błąd", "Nie udało się zmienić hasła!<br>Podane stare hasło nie zgadza się z hasłem aktualnie zapisanym w bazie danych.", -1);
        }
      });
    }
  }
  function GiveMeNewDatabase() {
    OpenGenericDialog(
      "Nowa baza danych",
      "Podaj nazwę dla nowej bazy danych:<br><br>"+
      "<input id='namedb' value='MojaBazaDanych' autofocus autocomplete='nofill' placeholder='Tutaj nazwa nowej bazy danych'><br>"+
      "Twoja nowa baza danych będzie utworzona według poniższego schematu:<br>"+
      "<b><?=sGetName()?>_</b>nazwaNowejBazy",
      [
        {
          "text": "Anuluj",
          "callback": function(did) {
            CloseDialog(did);
          }
        },
        {
          "text": "Utwórz",
          "primary": true,
          "callback": function(did) {
            // wysłanie wiadomości
            var dbname = $('#namedb');

            var localok = true;

            if(!ValidateValue(dbname.val(), true)) localok = false;

            if(localok) {
              var blocking = OpenBlockingDialog("Wysyłanie...");
              $.post("/panel/modules/requests.php", {
                'action': 'newDatabase',
                'dbname': dbname.val()
              }, function(data) {
                CloseDialog(did);
                CloseDialog(blocking);
                if(data=='success') {
                  OpenGenericDialog("Sukces", "Baza o nazwie <b><?=sGetName()?>_"+dbname.val()+"</b> została utworzona!", -1);
                  var current_databases_count = $('#databases_list tbody tr').length;
                  $('#databases_list tbody').append('<tr dbname="'+dbname.val()+'"><td>'+(current_databases_count+1)+'</td><td><?=sGetName()?>_'+dbname.val()+'</td><td align-right><button critical onclick="RemoveDatabase(\''+dbname.val()+'\')">Usuń bazę</button></td></tr>');
                }
                else if(data=="db_limit") {
                  OpenGenericDialog("Błąd", "Wykorzystałeś już maksymalną ilość baz danych, jakie mogłeś utworzyć.", -1);
                }
                else if(data=="exist") {
                  OpenGenericDialog("Błąd", "Baza danych o nazwie <b><?=sGetName()?>_"+dbname.val()+"</b> już istnieje", -1);
                }
                else {
                  OpenGenericDialog("Błąd", "Nie udało się utworzyć bazy danych. <br>Spróbuj ponownie później.", -1);
                }
                
              });
            }
          }
        }
      ]
    );
  }
</script>