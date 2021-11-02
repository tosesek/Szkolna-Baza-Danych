<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  cMustBeAdmin();
?>
<div class="page_generic">
  <div class="content">
    <h1>Nowa wiadomość e-mail</h1>
    Wyślij niestandardową wiadomość do wybranego użytkownika<br><br>
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
          <option disabled selected>Wybierz klasę</option>
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

    <div class="admin-messages-users-list">
      
    </div>
    
  </div>
</div>

<script>
  $(document).ready(function() {
    BindKeyToElement(keyCodes.enter, $('.admin-user-selection'), function() { DoSearchUser(); });

    $('#msg_user-apply').click(function() {
      DoSearchUser();
    });

    function DoSearchUser() {
      
      var uclass = $('#msg_user-class');
      var ugroup = $('#msg_user-group');
      var search = $('#msg_user-search');
      
      var localok = true;
      
      if(!ValidateInput(uclass) && !ValidateInput(search)) localok = false;
      
      if(localok) {

        $.getJSON('/panel/modules/requests.php', {
          'action': 'FindUsersByFilters',
          'filters': {
            'class': uclass.val(),
            'group': (ugroup.val()==0 ? '' : ugroup.val()),
            'search': search.val()
          }
        }, function(data) {
          $('.admin-messages-users-list .item').each(function() {
            $(this).slideUp(300, function() {
              $(this).remove();
            });
          });
          $.each(data, function(k, v) {
            var newelement = $('<div></div>');
            newelement.attr('class', 'item');
            if(v['blocked']) newelement.attr('blocked', '');
            newelement.slideUp(1);

            newelement.click(function() {
              OpenGenericDialog(
                "Nowa wiadomość",
                "<b>Uwaga!</b> Wiadomość musi być napisana w HTML'u do prawidłowego wyświetlania!<br><br><textarea id='admin-custom-mail-content' placeholder='Wporwadź treść swojej wiadomości do użytkownika \""+v['first_name']+" "+v['last_name']+"\"'></textarea>",
                [
                  {
                    "text": "Anuluj",
                    "callback": function(did) {
                      CloseDialog(did);
                    }
                  },
                  {
                    "text": "Pokaż podgląd",
                    "callback": function(did) {
                      // pokaż podgląd (w nowym oknie dialogowym, albo w nowej karcie)
                      var mailcontent = $('#admin-custom-mail-content');

                      var localok = true;

                      if(!ValidateInput(mailcontent)) localok = false;

                      if(localok) {
                        OpenCustomDialog("/panel/modules/templates/mail_preview.php", {
                          "title": "Wiadomość do administratora",
                          "content": mailcontent.val(),
                          'fname': v['first_name']
                        });
                      }
                    }
                  },
                  {
                    "text": "Wyślij wiadomość",
                    "primary": true,
                    "callback": function(did) {
                      // wysłanie wiadomości
                      var mailcontent = $('#admin-custom-mail-content');

                      var localok = true;

                      if(!ValidateInput(mailcontent)) localok = false;

                      if(localok) {
                        var blocking = OpenBlockingDialog("Wysyłanie...");
                        $.post('/panel/modules/requests.php', {
                          'action': 'sendCustomEmail',
                          'title': 'Wiadomość od administratora',
                          'content': mailcontent.val(),
                          'fname': v['first_name'],
                          'lname': v['last_name'],
                          'email': v['email']
                        }, function(data) {
                          if(data=='success') {
                            CloseDialog(did);
                            OpenGenericDialog('Wiadomość wysłana', '<b>'+v['first_name']+' '+v['last_name']+'</b> otrzyma Twoją wiadomość za maksymalnie 10 minut');
                          }
                          else {
                            CloseDialog(did);
                            OpenGenericDialog('Wystąpił błąd', 'Niestety nie udało się wysłać wiadomości e-mail do użytkownika <b>'+v['first_name']+' '+v['last_name']+'</b>');
                          }
                          CloseDialog(blocking);
                        });
                      }
                    }
                  }
                ]
              );
            });

            newelement.html(
              "<span class='fullname'>"+v['last_name']+" "+v['first_name']+"</span>"+
              "<span class='details'>"+
                v['class']['name'].replace(" ", "")+ // Nazwa klasy
                (v['class']['group']=='0' ? '' : '/'+v['class']['group'])+
                // " | eml@zst.pila.pl"+
                " | "+v['email']+
              "</span>"
            );

            $('.admin-messages-users-list').append(newelement);
            newelement.slideDown(300);
          });
        });
      }
      
    }
  })
</script>