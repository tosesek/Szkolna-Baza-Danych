<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  cMustBeAdmin();
?>
<div class="page_generic">
  <div class="content">
    <h1>Konta użytkowników</h1>
    Zarządzaj kontami innych użytkowników.<br><br>
    <button nomargin onclick="location.assign('/panel/tools')">Wróć do narzędzi administratora</button>
    <br>
    <br>
    <hr>
    <br>
    <h5>Wybierz klasę oraz grupę lub wyszukaj, aby wyświetlić użytkowników</h5>
    <br>
    <div class="admin-user-selection">

      <div class="user_class">
        <select id="msg_user-class">
          <option disabled>Wybierz klasę</option>
          <option value="-1" selected>Niezweryfikowani</option>
          <option value="-2">Zweryfikowani (tylko wyszukiwania)</option>
          <?php
            $classes = mysqli_query($_DB, "SELECT * FROM classes WHERE `id`!=2");
            while($r = mysqli_fetch_assoc($classes)) {
              ?>
                <option value="<?=$r['name']?>"><?=$r['name']?></option>
                <?php
            }              
          ?>
        </select>
      </div>

      <div class="user_class_group">
        <select id="msg_user-group">
          <option disabled selected>Wybierz grupę</option>
          <option value="0">Wszyscy</option>
          <option value="1">Grupa 1</option>
          <option value="2">Grupa 2</option>
        </select>
      </div>

      <div class="user_search">
        <input id="msg_user-search" nomargin type="search" placeholder="Wyszukaj użytkownika">
      </div>

      <div class="user_search">
        <button id="msg_user-apply" nomargin primary>Szukaj</button>
      </div>
    </div>

    <div class="admin-users-list" id="accounts_list">
      <?php
        $users = mysqli_query($_DB, "SELECT * FROM users WHERE `verified`=1 ORDER BY `last_name` ASC");
        if(mysqli_num_rows($users)) {
          while($r = mysqli_fetch_assoc($users)) {
            ?>
              <div class="item" uuid="<?=$r['id']?>">
                <span class='fullname'><?=$r['last_name']?> <?=$r['first_name']?></span>
                <span class='details'><?=$r['class']?>  | <?=$r['year']?> | <?=$r['email']?></span>
                <div class="buttons">
                  <button dialog-button nomargin onclick="AcceptAccount(<?=$r['id']?>, '<?=$r['first_name']?>', '<?=$r['last_name']?>')">Zatwierdź</button>
                </div>
              </div>
            <?php
          }
        }
      ?>
      
    </div>
    
  </div>
</div>

<script>

  $(document).ready(function() {
    BindKeyToElement(keyCodes.enter, $('.admin-user-selection'), function() { DoSearch(); });

    $("#msg_user-apply").click(function() {
      DoSearch();
    });
  });

  function DoSearch() {
    var uclass = $('#msg_user-class');
    var ugroup = $('#msg_user-group');
    var search = $('#msg_user-search');
    
    var localok = true;

    // Jeżeli wybrano klasę 'Niezweryfikowani' (-1)
      // Pokazujemy wszyskich niezweryfikowanych (pozostałe filtry działają normalnie i są opcjonalne)
    
    // Jeżeli wybrano klasę 'Zweryfikowani' (-2)
      // Wymagane jest wypełnienie pola wyszukiwania (inaczej nie zostaną wyświetlone żadne wyniki)
      // Pozostałe filtry działają normalnie i są opcjonalne

    // Jeżeli wybrano inną klasę
      // Pokazujemy wszystkich użytkowników z wybranej klasy
      // Filtry są opcjonalne
      // Wyszukiwarka jest opcjonalna
    
    if(!ValidateInput(uclass) && !ValidateInput(search)) localok = false;
    if(uclass.val()==-2 && !ValidateInput(search)) localok = false;

    if(localok) {
      $.getJSON('/panel/modules/requests.php', {
        'action': 'FindUsersByFilters',
        'filters': {
          'class': (uclass.val()<0 ? '' : uclass.val()),
          'group': (ugroup.val()==0 ? '' : ugroup.val()),
          'search': search.val(),
          'verified': (uclass.val()==-1 ? 1 : 2)
        }
      }, function(data) {

        $('.admin-users-list .item').each(function() {
          $(this).slideUp(300, function() {
            $(this).remove();
          });
        });

        if(data.length) {
          $.each(data, function(k, v) {
            var newelement = $('<div></div>');
            newelement.attr('class', 'item');
            newelement.attr('uuid', v['id']);
            if(v['blocked']) newelement.attr('blocked', '');
            newelement.slideUp(1);
            newelement.html(
              "<span class='fullname'>"+v['last_name']+" "+v['first_name']+"</span>"+
              "<span class='details'>"+v['class']['name']+" | "+v['year']+" | "+v['email']+"</span>"+
              "<div class='buttons'>"+
                (v['verified']==2 ?
                  "<button onclick='EditAccount("+v['id']+")' dialog-button>Edytuj dane</button>"+
                  "<button onclick=\"window.open(\'/panel/tools/file_browser.php?dir=/"+v["user_name"]+"/\')\" dialog-button>Przeglądaj pliki</button>"+
                  "<button critical onclick='RemoveAccount("+v['id']+", \""+v['first_name']+"\", \""+v['last_name']+"\")' dialog-button>Usuń</button>" 
                  :
                  "<button nomargin onclick='AcceptAccount("+v['id']+", \""+v['first_name']+"\", \""+v['last_name']+"\")' dialog-button>Zatwierdź</button>")+
              "</div>"
            );
  
            $('.admin-users-list').append(newelement);
            newelement.slideDown(300);
  
          });       
        }
        else {
          SetInputError(search);
        }
        
      });
    }
  }

  function GetRowElementByUuid(uuid) {
    return $('#accounts_list [uuid='+uuid+']');
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

      $.getJSON("/panel/modules/requests.php", {
        'action': 'GetAllClasses'
      }, function(data2) {

        var classes = $('<select id="dp_account-class"></select>'); 

        $.each(data2, function(k, v) {
          if(data['class']['name']==v['name']) {
            classes.append(
              "<option selected value='"+v['name']+"'>"+v['name']+"</option>"
            );
          }
          else {
            classes.append(
              "<option value='"+v['name']+"'>"+v['name']+"</option>"
            );
          }
          
        });


        OpenGenericDialog(
          "Edytuj dane konta",
          "<label><input type='checkbox' id='dp_account-isadmin' "+(data['privileges']==1 ? 'checked' : '')+">Użytkownik jest administratorem</label><br>"+
          "<label><input type='checkbox' id='dp_account-isblocked' "+(data['blocked']==1 ? 'checked' : '')+">Użytkownik jest zablokowany</label><br><br>"+
          "<input type='text' id='dp_account-firstname' value="+data['firstname']+" placeholder='Imię'>"+
          "<input type='text' id='dp_account-lastname' value="+data['lastname']+" placeholder='Nazwisko'>"+
          "<input type='email' id='dp_account-email' value="+data['email']+" placeholder='Adres e-mail'>"+
          "<input type='number' id='dp_account-year' value="+data['year']+" placeholder='Rocznik'>"+
          "<div style='display: grid; grid-template-columns: 1fr 0.5fr; grid-gap: 10px'>"+
            classes[0].outerHTML+
            "<select id='dp_account-class_group'>"+
              "<option disabled>Wybierz grupę</option>"+
              "<option value='1' "+(data['class']['group']==1 ? 'selected' : '')+">Grupa 1</option>"+
              "<option value='2' "+(data['class']['group']==2 ? 'selected' : '')+">Grupa 2</option>"+
            "</select>"+
          "</div>",
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
                var classname = $('[dialogid='+did+'] #dp_account-class');
                var classgroup = $('[dialogid='+did+'] #dp_account-class_group');

                var localok = true;

                // if (!isadmin.is(':checked')) localok = false;
                // if (!blocked.is(':checked')) localok = false;
                if (!ValidateInput(firstname)) localok = false;
                if (!ValidateInput(lastname)) localok = false;
                if (!ValidateInput(email)) localok = false;
                if (!ValidateInput(year)) localok = false;
                if (!ValidateInput(classname)) {localok = false;}
                if (!ValidateInput(classgroup)) {localok = false;}

                if(localok) {
                  $.post("/panel/modules/requests.php", {
                    'action': 'UpdateAccountData',
                    'uuid': uuid,
                    'data': {
                      'privileges': (isadmin.is(':checked') ? 1 : 0),
                      'blocked': (blocked.is(':checked') ? 1 : 0),
                      'first_name': firstname.val(),
                      'last_name': lastname.val(),
                      'class': classname.val(),
                      'class_group': classgroup.val(),
                      'email': email.val(),
                      'year' : year.val()
                    }
                  }, function(data) {
                    CloseDialog(blocking);
                    if(data=='success') {
                      OpenGenericDialog("Sukces", "Dane użytkownika zostały zapisane!", -1);
                      GetRowElementByUuid(uuid).find('.fullname').text(lastname.val() + " " + firstname.val());
                      GetRowElementByUuid(uuid).find('.details').text(classname.val() + " | " + year.val() + " | " + email.val());

                      if(isadmin.is(':checked')) { GetRowElementByUuid(uuid).remove(); }
                      if(blocked.is(':checked')) { GetRowElementByUuid(uuid).attr('blocked', ''); } else { GetRowElementByUuid(uuid).removeAttr('blocked'); }
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

      
    });
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
                var tools = GetRowElementByUuid(uuid).remove();
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
</script>
