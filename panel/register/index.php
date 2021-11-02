<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';

  if(bIsLoggedIn()) {
    header('location: /panel');
    exit();
  }
  
?>

<title>Rejestracja</title>

<div class="page_generic">
  <div class="content">
    <div class="login_container">
      <div class="info_container">
        <span class="title">Rejestracja</span>
        <span class="desc">
          Aby móc korzystać ze wszystkich możliwości systemu, musisz założyć konto.<br>
        </span>
      </div>

      <div class="login_details">

        <div style="display: grid; grid-template-columns: 1fr 0.5fr; grid-gap: 10px">
          <input type="text" style="width: 100%" maxlength="32" id="dp_register-login" autocomplete="username" autofocus placeholder="Nazwa użytkownika">
          <input type="number" style="width: 100%" maxlength="32" id="dp_register-year" placeholder="Rok przyjścia do szkoły" min="<?=date('Y')-5?>" max="<?=date('Y')?>">
        </div>
        <br>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; grid-gap: 10px">
          <input type="text" style="width: 100%" maxlength="32" id="dp_register-firstname" autocomplete="given-name" placeholder="Imię">
          <input type="text" style="width: 100%" maxlength="32" id="dp_register-lastname" autocomplete="family-name" placeholder="Nazwisko">
        </div>
        <input type="text" style="width: 100%" maxlength="32" id="dp_register-email" autocomplete="email" placeholder="Adres e-mail">
        <br>
<!--         
        

        <br> -->
        
        <div style="display: grid; grid-template-columns: 1fr 0.5fr; grid-gap: 10px">          
          <select id="dp_register-class">
            <option selected disabled>Wybierz klasę</option>
            <?php
              $classes = mysqli_query($_DB, "SELECT * FROM classes WHERE `id`>10");
              while($r = mysqli_fetch_assoc($classes)) {
                ?>
                  <option value="<?=$r['name']?>"><?=$r['name']?></option>
                  <?php
              }              
              ?>
          </select>          
          <select id="dp_register-class_group">
            <option selected disabled>Wybierz grupę</option>
            <option value="1">Grupa 1</option>
            <option value="2">Grupa 2</option>
          </select>          
        </div>

      </div>
      <br>
      <div align-center style="display: grid; grid-template-columns: 1fr; grid-gap: 10px">
          <h4>UWAGA!</h4>
          Hasło zostanie wygenerowane automatycznie i zostanie ono przesłane<br>na Twoją skrzynkę pocztową po zweryfikowaniu konta przez administratora systemu!<br>
          <!-- <input type="password" style="width: 100%" maxlength="32" id="dp_register-password" autocomplete="new-password" placeholder="Hasło">
          <input type="password" style="width: 100%" maxlength="32" id="dp_register-password2" autocomplete="new-password" placeholder="Powtórz hasło"> -->
        </div>
        <span style="font-size: 13px; padding: 20px; display:block; margin-top: 20px">
          <b>Zakładając konto:</b>
          <ul>
            <li>Zgadzasz się na przesyłanie na Twoją skrzynkę pocztową wiadomości email dotyczących krytycznych informacji o Twoim koncie (np: informacja o zmianie hasła).</li>
            <li>Zgadzasz się, aby administratorzy systemu mogli przesyłać na Twoją skrzynkę pocztową wiadomości dotyczące twojego konta i poczynań w Systemie Szkolnej Bazy Danych.</li>
            <li>Potwierdzasz, że zapoznałeś się ze <a href="http://zst.pila.pl/strona/public/pdf/StatutZespo%C5%82uSzko%C5%82Technicznych.pdf">Statutem Zespołu Szkół Technicznych w Pile</a> oraz z innymi zasadami i regulaminami panującymi w szkole.</li>
            <li>Potwierdzasz, że administratorzy i twórcy systemu mogą w każdym momencie, bez poinformowania o tym użytkownika, przeglądać zawartość opublikowaną przez Ciebie w Systemie Szkolnej Bazy Dnaych.</li>
          </ul>
          <br>
          Po zatwierdzeniu Twojego konta przez administratora systemu, utworzone zostanie konto <b>FTP</b> oraz <b>MySQL</b>, do którego otrzymasz natychmiastowy dostęp.
          <!-- Strona automatycznie wysyła maile (np. aktywacyjny, reset hasła, informacje o zmianach na koncie), dlatego twój mail powinien być poprawny.<br>
          Dodatkowo, po aktywacji konta przez administrację do konta zostaną przypisane ograniczone uprawnienia do bazy danych oraz serwera FTP.<br> -->
          <!-- <br> -->
          <!-- Rejestrując się w systemie <b>Szkolnej Bazy Danych</b> potwierdzasz znajomość statutu <a href="http://zst.pila.pl/strona/public/pdf/StatutZespo%C5%82uSzko%C5%82Technicznych.pdf">Zespołu Szkół Technicznych w Pile</a>, oraz innych zasad i regulaminów obowiązujących w szkole.<br> -->
          <br>
          <br>
          <span align-right style="display:block; font: 14px YouTube; color: #fff"><i>Ignorantia legis non excusat</i> - łac. "Nieznajomość prawa nie jest usprawiedliwieniem"</span>
        </span>
      <br>
      <br>

      <div class="login_buttons">
        <button id="dp_register-cancel">Anuluj</button>
        <button primary id="dp_register-submit">Utwórz konto</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {

    BindKeyToElement(keyCodes.enter, $('.login_container'), function() {DoRegister();});

    $('#dp_register-cancel').click(function() {
      location.assign('/panel/');
    });

    $('#dp_register-submit').click(function() {
      DoRegister();
    });

    function DoRegister() {
      var login = $('#dp_register-login');
      var firstname = $('#dp_register-firstname');
      var lastname = $('#dp_register-lastname');
      var email = $('#dp_register-email');
      // var password = $('#dp_register-password');
      // var password2 = $('#dp_register-password2');
      var year = $('#dp_register-year');
      var classs = $('#dp_register-class');
      var class_group = $('#dp_register-class_group');
      
      var localok = true;

      if(!ValidateInput(login)) localok = false;
      if(!ValidateValue(login.val(), true)) {localok = false; SetInputError(login); }
      if(!ValidateInput(firstname)) localok = false;
      if(!ValidateInput(lastname)) localok = false;
      if(HasSpaces(login.val())) {localok = false; SetInputError(login); }
      if(HasSpaces(firstname.val()) || HasNumbers(firstname.val())) {localok = false; SetInputError(firstname); }
      if(HasSpaces(lastname.val()) || HasNumbers(lastname.val())) {localok = false; SetInputError(lastname); }
      if(!ValidateInput(email)) localok = false;
      if(!ValidateEmail(email.val())) {localok = false; SetInputError(email);}
      // if(!ValidateInput(password)) localok = false;
      // if(!ValidateInput(password2)) localok = false;
      if(year.val() > <?=date('Y')?> || year.val() < <?=date('Y')-5?>) { localok = false; SetInputError(year); }
      if(!ValidateInput(classs)) {localok = false; SetInputError($('#dp_register-class_select'))}
      if(!ValidateInput(class_group)) {localok = false; SetInputError($('#dp_register-class_group_select'))}
      if(class_group.val()>2||class_group.val()<1) {localok = false; SetInputError(class_group);}

      // Sprawdzanie hasła
      // if(!ValidatePassword(password.val())) { 
      //   localok = false; 
      //   SetInputError(password); 
      //   SetInputError(password2); 
      //   OpenGenericDialog(
      //     "Błędne hasło", 
      //     "Twoje hasło musi spełniać podane wymagania:<br>"+
      //     "- Musi zawierać przynajmniej jedną cyfrę<br>"+
      //     "- Musi zawierać przynajmniej jedną dużą literę<br>"+
      //     "- Musi zawierać przynajmniej jedną małą literę<br>"+
      //     "- Musi zawierać przynajmniej 8 znaków<br>"+
      //     "- Nie może zawierać znaków specjalnych (włącznie ze znakami diakrytycznymi)<br>",
      //     -1
      //   );
      //   return false;
      // }

      // if(password.val() != password2.val()) localok = false;

      if(localok) {
        var logging = OpenBlockingDialog('Czekaj...');

        $.post("/panel/modules/requests.php", {
          'action': 'CreateAccount',
          'login': login.val(),
          'firstname': firstname.val(),
          'lastname': lastname.val(),
          'email': email.val(),
          // 'password': password.val(),
          'year': year.val(),
          'class': classs.val(),
          'class_group': class_group.val()
        }, function(data) {
          if(data=='success') {
            // document.cookie ="logout_type=activate_email_first";
            location.assign('/panel/logout?type=activate_email_first');
          }
          else {
            if(data=='error') {
              OpenGenericDialog("Błąd rejestracji", "Wystąpił nieznany błąd<br>Skontatkuj się z administratorem systemu", -1);
            }
            else if(data=='user-exists') {
              SetInputError(login);
              SetInputError(email);
              OpenGenericDialog(
                "Błąd rejestracji",
                "Nie udało się założyć nowego konta, ponieważ użytkownik o podanych danych już istnieje.<br>"+
                "Jeżeli uważasz że to błąd, niezwłocznie skontaktuj się z administratorem systemu.<br><br>"+
                "Jeżeli nie pamiętasz hasła do swojego konta, możesz je zresetować przechodząc pod <a href=\"/panel/resetpassword.php\">ten link</a>.",
                -1
              );
            }
            else if(data="closed_register"){
              OpenGenericDialog(
                "Błąd rejestracji",
                "Nie udało się założyć nowego konta, ponieważ rejestracja została wyłączona przez administratora.<br>"+
                "Jeżeli uważasz że to błąd, niezwłocznie skontaktuj się z administratorem systemu.<br><br>",
                -1
              );
            }
            CloseDialog(logging);
          }
        });
      }
      else {
        OpenGenericDialog(
          "Błąd rejestracji", 
          "Podane dane nie mogą zostać przesłane na serwer.<br>"+
          "Sprawdź poprawność swoich danych i spróbuj ponownie.<br><br>"+
          "Jeżeli uważasz, że jest to błąd, skontaktuj się z administratorem systemu.",
          -1
        );
      }
    }
  });
</script>
