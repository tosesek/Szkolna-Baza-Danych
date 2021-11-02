<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
?>
<div class="page_generic">
  <div class="content">
    <?php
      if(!isset($_REQUEST['email']) || $_REQUEST['email']=='' || !isset($_REQUEST['reset']) || $_REQUEST['reset']=='') {
        ?>
          <h1>Resetowanie hasła</h1>
          Aby zresetować swoje hasło, musisz podać swój adres e-mail, abyśmy mogli przesłać Ci link do zmiany hasła:<br><br>
          <input id="email" placeholder="Adres e-mail" autocomplete="new-password">
          <button name="send">Wyślij</button>
          <script>
            $(document).ready(function() {
              $("[name=send]").click(function() {
                let email = $('#email').val();
                
                var localok = true;
                
                if(!ValidateInput($('#email'))) { localok = false; }
                if(!ValidateEmail(email)) { localok = false; SetInputError($('#email')); }
                
                if(localok) {
                  var blocking = OpenBlockingDialog("Wysyłanie...");
                  // 🦇 <- o nie! nietoperz!
                  $.post("/panel/modules/requests.php", {
                    'action': 'resetPasswordMail',
                    'email': email
                  }, function(data) {
                    CloseDialog(blocking);
                    if(data=='success'||data=="mail_send_error") {
                      OpenGenericDialog("Sukces", "Wiadomość z linkiem do zmiany hasła została przesłana na adres <b>"+email+"</b>", -1); // No chyba, że nie?
                    }
                    else if(data=='not-exist') {
                      OpenGenericDialog("Wystąpił błąd", "Nie znaleźliśmy adresu <b>"+email+"</b> w naszej bazie", -1);
                      SetInputError($('#email'));
                    }
                    else {
                      OpenGenericDialog("Wystąpił błąd", 'Nie udało się wysłać wiadomości na podany adres e-mail. Spróbuj ponownie później');
                      SetInputError($('#email'));
                    }
                  });
                }
              });
            });
          </script>
        <?php
      }
      else {
        $hash = $_REQUEST['reset'];
        $email = $_REQUEST['email'];
        $query="SELECT * FROM users WHERE `email` = '".$email."' and `reset_password_id`='$hash'";
        if(mysqli_num_rows(mysqli_query($_DB, $query)) == 1) {
          ?>
            <h1>Resetowanie hasła</h1>
            Wprowadź nowe hasło. Na Twój adres e-mail zostanie wysłana wiadomość zawierającą potwierdzenie zmiany hasła.<br><br>
            <input type="password" placeholder="Nowe hasło" autocomplete="new-password" id="new1">
            <input type="password" placeholder="Potwierdź hasło" autocomplete="new-password" id="new2"><br>
            <br>
            <button nomargin name="reset">Zresetuj hasło</button>
            <script>
              $(document).ready(function() {
                $("[name=reset]").click(function() {
                  let new1=$('#new1');
                  let new2=$('#new2');

                  let localok = true;

                  if(!ValidateInput(new1)) localok = false;
                  if(!ValidateInput(new2)) localok = false;

                  if(new1.val() != new2.val()) { localok = false; SetInputError(new2); }

                  // Sprawdzanie hasła
                  if(!ValidatePassword(new2.val())) { 
                    localok = false; 
                    SetInputError(new1); 
                    SetInputError(new2); 
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

                  if(localok) {
                    var blocking = OpenBlockingDialog("Zapisywanie...");
                    //🦇
                    $.post("/panel/modules/requests.php", {
                      'action': 'resetPassword',
                      'email': '<?=$email?>',
                      'hash': '<?=$hash?>',
                      'new1': new1.val()
                    }, function(data) {
                      CloseDialog(blocking);
                      if(data=='success' || data=="mail_send_error") {
                        OpenGenericDialog("Sukces", "Hasło do Twojego konta zostało zmienione!", -1); // No chyba, że nie?
                      }
                      else if(data=='not-exist') {
                        OpenGenericDialog("Wystąpił błąd", "Adres <b><?=$email?></b> nie został zarejestrowany w naszej bazie", -1); // No chyba, że nie?
                      }
                      else {
                        OpenGenericDialog("Wystąpił błąd", 'Nie udało się zmienić hasła. Spróbuj ponownie później');                  
                      }
                    });
                  }
                });
              });
            </script>
          <?php
        }
        else {
          echo "Twój link do zresetowania hasła wygasł.";
        }
      }
    ?>
  </div>
</div>