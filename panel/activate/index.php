<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';

  $email = $_REQUEST['em'];
  $fakecode = $_REQUEST['code']; // tak na prawdę to to nie jest potrzebne, ale nikt o tym nie wie
  $code = $_REQUEST['compilation'];
  $fakecode2 = $_REQUEST['password']; // to tak samo jak dwie linijki wyżej
  
  if(!isset($email)) {
    // następnym razem nie hakuj pojebańcu bo cie zmiecie z planszy
    ?>
      <div class="page_generic">
        <div class="content">
          <h1>Aktywacja konta</h1>
          Wygląda na to, że wysłano nieprawidłowe żądanie.<br>
          Sprawdź swoją pocztę email i spróbuj ponownie aktywować konto.<br>
          Jeżeli aktywacja ponownie się nie powiedzie, prosimy o skontaktowanie się z administratorem systemu.<br><br>
          <button nomargin critical name="noemail">Nie dotarł do mnie email</button>
          <button onclick="location.assign('/panel')">Strona główna</button>
        </div>
      </div>
    <?php
  }
  elseif((!isset($fakecode) && !isset($fakecode)) || ($fakecode=="" || $fakecode2=="")) {
    // jakiś przebrzydły chuj próbował przechytrzyć system i nie podać moich fejkowych kodów (pies brudny)
    // w tym przypadku wyjebiemy mu błąd, ponieważ nie tolerujemy takiego zachowania

    // oj nie nie sandomierzu -1iq

    ?>
      <div class="page_generic">
        <div class="content">
          <h1>Aktywacja konta</h1>
          Nie udało się pobrać informacji niezbędnych do aktywacji konta.<br>
          Sprawdź swoją pocztę email, i wejdź w link wysłany podczas rejestracji konta.<br><br>
          <button nomargin critical name="noemail">Nie dotarł do mnie email</button>
          <button onclick="location.assign('/panel')">Strona główna</button>
        </div>
      </div>
    <?php
  }
  else {
    $check = mysqli_query($_DB, "SELECT * FROM users WHERE `email`='$email'");
    if(mysqli_num_rows($check)) {
      $f = mysqli_fetch_assoc($check);
      if($f['verified']==0) {
        if($f['activation_code']==$code) {
          // aktywuj teraz
          $activate_completed = mysqli_query($_DB, "UPDATE users SET `verified`=1 WHERE `email`='$email'");
          if($activate_completed) {
            ?>
              <div class="page_generic">
                <div class="content">
                  <h1>Aktywacja konta</h1>
                  Aktywacja konta powiodła się!<br>
                  Kolejnym etapem jest zatwierdzenie twojego konta przez administratora systemu.<br>
                  <br>
                  <button nomargin primary onclick="location.assign('/panel')">Strona główna</a>
                </div>
              </div>
            <?php
          }
          else {
            ?>
              <div class="page_generic">
                <div class="content">
                  <h1>Aktywacja konta</h1>
                  Niestety nie udało się aktywować konta.<br>
                  Wystąpił nieznany błąd.<br>
                  <br>
                  <button nomargin primary onclick="location.assign('/panel')">Strona główna</a>
                </div>
              </div>
            <?php
          }
        }
        else {
          ?>
            <div class="page_generic">
              <div class="content">
                <h1>Aktywacja konta</h1>
                Aktywacja konta nie powiodła się.<br>
                Niestety podane w zapytaniu danie nie zgadzają się.<br>
                Sprawdź swoją pocztę email ponownie i spróbuj ponownie aktywować konto.<br><br>
                <button nomargin critical name="noemail">Nie dotarł do mnie email</button>
                <button onclick="location.assign('/panel')">Strona główna</button>
              </div>
            </div>
          <?php
        }        
      }
      else {
        ?>
          <div class="page_generic">
            <div class="content">
              <h1>Aktywacja konta</h1>
              Niestety nie udało się aktywować konta.<br>
              Konto o podanym adresie e-mail zostało już aktywowane.<br>
              <br>
              <button nomargin primary onclick="location.assign('/panel')">Strona główna</a>
            </div>
          </div>
        <?php
      }
    }
    else {
      ?>
        <div class="page_generic">
          <div class="content">
            <h1>Aktywacja konta</h1>
            Niestety nie udało się aktywować konta.<br>
            Konto o podanym adresie e-mail nie istnieje.<br>
            <br>
            Skontaktuj się z administratorem systemu.
            <br>
            <button nomargin primary onclick="location.assign('/panel')">Strona główna</a>
          </div>
        </div>
      <?php
    }
  }
?>

<script>
  $(document).ready(function() {
    $("[name=noemail]").click(function() {
      OpenGenericDialog(
        "Ponowna aktywacja",
        "Podaj swój adres email, podany podczas rejestracji konta, abyśmy mogli ponownie przesłać link aktywacyjny.<br><br>"+
        "<input type='email' id='dp_account-email' placeholder='Adres e-mail'>",
        [
          {
            "text": "Anuluj",
            "callback": function(did) {
              CloseDialog(did);
            }
          },
          {
            "text": "Wyślij ponownie",
            "primary": true,
            "callback": function(did) {
              var blocking = OpenBlockingDialog("Wysyłanie wiadomości...");
              var email = $('[dialogid='+did+'] #dp_account-email');
              var localok = true;

              if (!ValidateInput(email)) localok = false;

              if(localok) {
                $.post("/panel/modules/requests.php", {
                  'action': 'resendCode',
                  'email': email.val()
                }, function(data) {
                  CloseDialog(blocking);
                  if(data=='success') {
                    OpenGenericDialog("Sukces", "Wiadomość z linkiem aktywacyjnym została wysłana na Twój adres e-mail!<br>Jeżeli nie otrzymasz wiadomości w ciągu 10 minut, spróbuj ponownie lub skontaktuj się z administratorem systemu.", -1);
                  }
                  else if(data=="mail_send_error") {
                    OpenGenericDialog("Wystąpił błąd", 'Nie udało się wysłać wiadomości.<br>Spróbuj ponownie później, a jeżeli błąd nie ustępuje, skontaktuj się z administratorem systemu.');
                  }
                  else if(data=="activated"){
                    OpenGenericDialog("Wystąpił błąd", 'Konto, do którego przypisany jest dany adres e-mail zostało już wcześniej aktywowane.');
                  }
                  else if(data=='not-exist') {
                    OpenGenericDialog("Wystąpił błąd", "Podany adres e-mail nie został zarejestrowany w naszej bazie danych", -1); // No chyba, że nie?
                  }
                  else {
                    OpenGenericDialog("Wystąpił błąd", 'Nie udało się wysłać wiadomości z nieznanych przyczyn.<br>Jeżeli błąd będzie się powtarzał, skontaktuj się z administratorem systemu.');                  
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

</script>