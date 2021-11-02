<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  
  if(bIsLoggedIn()) {
    header('location: /panel/');
    exit();
  }
?>

<title>Logowanie</title>

<div class="page_generic">
  <div class="content">
    <div class="login_container">
      <div class="info_container">
        <span class="title">Logowanie</span>
        <span class="desc">
          Zaloguj się do swojego konta aby zmienić ustawienia
        </span>
      </div>

      <div class="login_details">
        <input type="text" maxlength="32" id="dp_login-login" autofocus placeholder="Nazwa użytkownika lub adres email">
        <input type="password" maxlength="32" id="dp_login-password" placeholder="Hasło">
      </div>

      <div class="login_buttons">
        <button id="dp_login-cancel">Anuluj</button>
        <button id="dp_login-register">Załóż konto</button>
        <button id="dp_login-forgot_password">Nie pamiętam hasła</button>
        <button primary id="dp_login-submit">Zaloguj</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {

    BindKeyToElement(keyCodes.enter, $('.login_container'), function() {DoLoginCheck();});

    $('#dp_login-cancel').click(function() {
      location.assign('/panel/');
    });

    $('#dp_login-register').click(function() {
      location.assign('/panel/register');
    });

    $('#dp_login-forgot_password').click(function() {
      location.assign('/panel/password/');
    });

    $('#dp_login-submit').click(function() {
      DoLoginCheck();
    });

    function DoLoginCheck() {
      var login = $('#dp_login-login');
      var password = $('#dp_login-password');
      
      var localok = true;

      if(!ValidateInput(login)) localok = false;
      if(!ValidateInput(password)) localok = false;

      if(localok) {
      var logging = OpenBlockingDialog('Czekaj...');
        console.log("ok");
        var password_hash = MD5(password.val());
        $.post("/panel/modules/requests.php", {
          'action': 'ValidateLoginDetails',
          'login': login.val(),
          'password': password_hash
        }, function(data) {
          // console.log(data);
          if(data=='success' || data==null || data=='') {
            location.assign('/panel');
          }
          else {
            if(data=='error') {
              OpenGenericDialog("Błąd logowania", "Podano nieprawidłowe dane logowania", -1);
              SetInputError(login);
              SetInputError(password);
            }
            else if(data=='email-not-verified') {
              OpenGenericDialog(
                "Błąd logowania",
                "Adres e-mail podanego konta nie został jeszcze zweryfikowany.<br>"+
                "Na twój adres e-mail została wysłana wiadomość z linkiem aktywacyjnym.<br><br>"+
                "Jeżeli nie otrzymano wiadomości w ciągu 10 minut od rejestracji, należy <a href='/panel/activate/'>ponownie aktywować konto</a> lub skontaktować się z administratorem systemu.",
                -1              
              );
            }
            else if(data=='not-verified') {
              OpenGenericDialog("Błąd logowania", "Podane konto nie zostało jeszcze zweryfikowane przez administratora", -1);
            }
            else if(data=='blocked') {
              OpenGenericDialog("Błąd logowania", "Podane konto zostało zablokowane przez administratora systemu.<br>Jeżeli uważasz, że jest to błąd, skontaktuj się z administratorem systemu.", -1);
            }
            else if(data=='multi-user') {
              OpenGenericDialog("Błąd logowania", "Nie można się zalogować, ponieważ wykryto zbyt dużą ilość kont.<br>Skontaktuj się z administratorem systemu.", -1);
            }

            CloseDialog(logging);
          }
          
        });
      }
    }
  });
</script>