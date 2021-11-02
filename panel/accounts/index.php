<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  cMustBeAdmin();
?>
<div class="page_generic">
  <div class="content">
    <h1>Konta użytkowników</h1>
    Zarządzaj kontami innych użytkowników.<br>
    <br>
    <table cellspacing="0" id="accounts_list">
      <thead>
        <tr>
          <td style="width: 1px">Rocznik</td>
          <td>Nazwa</td>
          <td>Imię</td>
          <td>Nazwisko</td>
          <td style="width: 1px" align-right>Uprawnienia</td>
          <td style="width: 1px" align-right>Narzędzia</td>
        </tr>
      </thead>
      <tbody>
        <?php
          $users = mysqli_query($_DB, "SELECT * FROM users WHERE `verified`>0 ORDER BY `privileges` DESC, `year` ASC");
          while($r = mysqli_fetch_assoc($users)) {
            ?>  
              <tr uuid="<?=$r['id']?>" <?=($r['privileges']==1 ? 'highlight' : '')?> <?=($r['blocked']==1 ? 'blocked' : '')?>>
                <td name="year"><?=$r['year']?></td>
                <td name="username"><?=$r['name']?></td>
                <td name="firstname"><?=$r['first_name']?></td>
                <td name="lastname"><?=$r['last_name']?></td>
                <td name="privileges" align-right><?=($r['privileges']==1 ? 'Administrator' : 'Użytkownik')?></td>
                <td align-right class="tools">
                  <?php
                    if($r['verified']==2) {
                      // zatwierdzone
                      ?> <button dialog-button tool-type="edit" onclick="EditAccount('<?=$r['id']?>')">Edytuj dane</button> <?php
                    }
                    else {
                      // oczekuje
                      ?> <button dialog-button tool-type="accept" onclick="AcceptAccount('<?=$r['id']?>', '<?=$r['first_name']?>', '<?=$r['last_name']?>')">Zatwierdź</button> <?php
                    }
                  ?>
                  <button dialog-button critical tool-type="remove" onclick="RemoveAccount('<?=$r['id']?>', '<?=$r['first_name']?>', '<?=$r['last_name']?>')">Usuń</button>
                </td>
              </tr>
            <?php
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>

  function GetRowElementByUuid(uuid) {
    return $('#accounts_list [uuid='+uuid+']');
  }

  function AcceptAccount(uuid, fn, ln) {
    var dialog = OpenGenericDialog(
      "Zatwierdź użytkownika",
      "Masz zamiar zatwierdzić użytkownika <b>"+fn+" "+ln+"</b>!<br>"+
      "Spowoduje to utworzenie konta dla serwera <b>FTP</b> oraz konta bazy danych <b>MySQL</b>",
      [
        {
          "text": "Anuluj",
          "callback": function(did) {
            CloseDialog(did);
          }
        },
        {
          "text": "Rozumiem",
          "primary": true,
          "callback": function(did) {
            var blocking = OpenBlockingDialog("Czekaj...");
            $.post("/panel/modules/requests.php", {
              "action": "AcceptAccount",
              "id": uuid
            }, function(data) {
              CloseDialog(blocking);
              if(data=='success') {
                CloseDialog(did);
                var tools = GetRowElementByUuid(uuid).find('.tools');
                tools.find('[tool-type=accept]').remove();
                tools.prepend('<button dialog-button tool-type="edit" onclick="EditAccount(\''+uuid+'\');">Edytuj dane</button>');
              }
              else {
                CloseDialog(did);
                OpenGenericDialog("Wystąpił błąd", "Nie udało się zatwierdzić konta użytkownika<br>Skontaktuj się z programistą", -1);
              }
            })
          }
        }
      ]
    )
  }

  function RemoveAccount(uuid, fn, ln) {
    var dialog = OpenGenericDialog(
      "Usuń użytkownika",
      "Zamierzasz usunąć użytkownika <b>"+fn+" "+ln+"</b>!<br>"+
      "Wszystkie dane w bazie danych, oraz wszystkie pliki strony zostaną usunięte!<br><br>"+
      "Czy na pewno chcesz to zrobić?",
      [
        {
          "text": "Anuluj",
          "callback": function(did) {
            CloseDialog(did);
          }
        },
        {
          "text": "Tak, usuń użytkownika",
          "primary": true,
          "callback": function(did) {
            var blocking = OpenBlockingDialog("Czekaj...");
            $.post("/panel/modules/requests.php", {
              'action': 'RemoveAccount',
              'id': uuid
            }, function(data) {
              CloseDialog(blocking);
              if(data=='success') {
                CloseDialog(did);
                OpenGenericDialog('Sukces', 'Konto użytkownika <b>'+fn+' '+ln+'</b> zostało usunięte!', -1);
                var row = GetRowElementByUuid(uuid);
                row.remove();
              }
              else {
                CloseDialog(did);
                OpenGenericDialog('Wystąpił błąd', 'Nie udało się usunąć konta użytkownika <b>'+fn+' '+ln+'</b>!<br>Skontaktuj się z programistą', -1);
              }
            })
          }
        }
      ]
    )
  }

  function EditAccount(uuid) {
    $.getJSON('/panel/modules/requests.php', {
      'action': 'GetAccountData',
      'uuid': uuid
    }, function(data) {
      OpenGenericDialog(
        "Edytuj dane konta",
        "<label><input type='checkbox' id='dp_account-isadmin' "+(data['privileges']==1 ? 'checked' : '')+">Użytkownik jest administratorem</label><br>"+
        "<label><input type='checkbox' id='dp_account-isblocked' "+(data['blocked']==1 ? 'checked' : '')+">Użytkownik jest zablokowany</label><br><br>"+
        "<input type='text' id='dp_account-firstname' value="+data['firstname']+" placeholder='Imię'>"+
        "<input type='text' id='dp_account-lastname' value="+data['lastname']+" placeholder='Nazwisko'>"+
        "<input type='email' id='dp_account-email' value="+data['email']+" placeholder='Adres e-mail'>"+
        "<input type='number' id='dp_account-year' value="+data['year']+" placeholder='Rocznik'>",
        [
          {
            "text": "Anuluj",
            "callback": function(did) {
              CloseDialog(did);
            }
          },
          {
            "text": "Zapisz zmiany",
            "primary": true,
            "callback": function(did) {
              var blocking = OpenBlockingDialog("Czekaj...");
              var isadmin = $('[dialogid='+did+'] #dp_account-isadmin');
              var blocked = $('[dialogid='+did+'] #dp_account-isblocked');
              var firstname = $('[dialogid='+did+'] #dp_account-firstname');
              var lastname = $('[dialogid='+did+'] #dp_account-lastname');
              var email = $('[dialogid='+did+'] #dp_account-email');
              var year = $('[dialogid='+did+'] #dp_account-year');

              var localok = true;

              // if (!isadmin.is(':checked')) localok = false;
              // if (!blocked.is(':checked')) localok = false;
              if (!ValidateInput(firstname)) localok = false;
              if (!ValidateInput(lastname)) localok = false;
              if (!ValidateInput(email)) localok = false;
              if (!ValidateInput(year)) localok = false;

              if(localok) {
                $.post("/panel/modules/requests.php", {
                  'action': 'UpdateAccountData',
                  'uuid': uuid,
                  'data': {
                    'privileges': (isadmin.is(':checked') ? 1 : 0),
                    'blocked': (blocked.is(':checked') ? 1 : 0),
                    'first_name': firstname.val(),
                    'last_name': lastname.val(),
                    'email': email.val(),
                    'year' : year.val()
                  }
                }, function(data) {
                  CloseDialog(blocking);
                  if(data=='success') {
                    OpenGenericDialog("Sukces", "Dane użytkownika zostały zapisane!", -1);
                    GetRowElementByUuid(uuid).find('td[name=year]').text(year.val());
                    GetRowElementByUuid(uuid).find('td[name=firstname]').text(firstname.val());
                    GetRowElementByUuid(uuid).find('td[name=lastname]').text(lastname.val());
                    GetRowElementByUuid(uuid).find('td[name=privileges]').text((isadmin.is(':checked') ? (blocked.is(':checked') ? 'Użytkownik' : 'Administrator') : 'Użytkownik'));
                    if(isadmin.is(':checked')) {GetRowElementByUuid(uuid).attr('highlight', '');} else {GetRowElementByUuid(uuid).removeAttr('highlight', '');}
                    if(blocked.is(':checked')) {GetRowElementByUuid(uuid).attr('blocked', '').removeAttr('highlight');} else {GetRowElementByUuid(uuid).removeAttr('blocked', '');}
                  }
                  else {
                    OpenGenericDialog("Wystąpił błąd", 'Nie udało się zapisać danych użytkownika<br>Skontaktuj się z programistą');
                  }
                  CloseDialog(did);
                });
              }
            }
          }
        ]
      );
    });
  }
</script>