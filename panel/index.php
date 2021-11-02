<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  // cMustBeLoggedIn();

  if(isset($_SESSION['logout_type'])) {
    $type = $_SESSION['logout_type'];
    $message = "";
    switch($type) {
      case 'password_changed': $message = "Twoje hasło zostało zmienione z innej lokalizacji<br>Spróbuj zalogować się ponownie."; break;
      case 'account_blocked': $message = "Twoje konto zostało zablokowane<br>Jeżeli uważasz, że jest to błąd, skontaktuj się z administratorem systemu."; break;
      case 'activate_email_first': $message = "Zanim się zalogujesz, musisz najpierw aktywować adres email.<br>Jeżeli email nie przyjdzie do Ciebie w ciągu 10 minut od rejestracji, <a href='/panel/activate/'>spróbuj wysłać go jeszcze raz</a>."; break;
    }

    ?>
      <script>
        OpenGenericDialog("Wylogowano", "<?=$message?>", -1);
      </script>
    <?php
    // setcookie("logout_type", "", time() - 360000);
    unset($_SESSION['logout_type']);
  }

  if(bIsLoggedIn()) {
    ?>
      <div class="page_generic">
        <div class="content">
          <h1>Witaj, <?=sGetFirstName()?></h1>
          Instrukcje dotyczące połączenia z serwerem <b>FTP</b> lub <b>MySQL</b> znajdziejsz na stronie <a href="/panel/settings">ustawień konta</a>.<br>
          <br>
          <iframe id="ytplayer" type="text/html" width="600" height="300" src="https://www.youtube.com/embed/WU0cGmmL374" frameborder="0" allowfullscreen>
        </div>
      </div>
    <?php
  }
  else {
    ?>
      <div class="page_generic">
        <div class="content">
          <h1><a href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a></h1>
          <!-- <marquee style="font: 20px YouTube" direction="left" scrollamount="5" loop="infinite">
            <a style="color: #f0f" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐍
            <a style="color: #0af" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐀
            <a style="color: #f00" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🕷️
            <a style="color: #ff0" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🦇
            <a style="color: #f00" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🕷️
            <a style="color: #f0f" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐍
            <a style="color: #fa0" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐀
            <a style="color: #0af" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🦇
            <a style="color: #f0f" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐍
            <a style="color: #fa0" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐀
            <a style="color: #f00" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🕷️
            <a style="color: #ff0" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🦇
            <a style="color: #f0f" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐍
            <a style="color: #0af" href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a> 🐀
          </marquee> -->
          <br>
          <button nomargin onclick="window.open('https://zrzutka.pl/z/DLAGRAFIKA')" critical>Wesprzyj zbiórkę na odzyskanie danych z dysku naszego grafika!</button>
        </div>
      </div>

      <div class="page_generic" style="margin-top: 30px">
        <div class="content">
          <h1>Witaj na stronie głównej!</h1>
          <h4>Aby korzystać ze wszystkch funkcji, musisz się zalogować</h4>
          <marquee style="font: 20px YouTube" direction="right" scrollamount="10" loop="infinite">
            <font color="#f0f">Testuj Kamil! TESTUJ!!!!</font> 🐍
            <font color="#0af">Jak zwykle połowa kodu nie działa!</font> 🐀
            <font color="#f00">Zweryfikuj mi e-maila!</font> 🕷️
            <font color="#ff0">Nie mogę się zalogować!!!!!1!</font> 🦇
            <font color="#f00">I tak wiemy, że Twoje hasło to: bardzosilnehaslo123</font> 🕷️
            <font color="#f0f">Najnowsza aktualizacja zepsuła logowanie!!!</font> 🐍
            <font color="#fa0">FTP nie działa!!!</font> 🐀
            <font color="#0af">"A internet działa?"</font> 🦇
            <font color="#f0f">Szukaj RJ-tki w podłodze</font> 🐍
            <font color="#fa0">"A co to switch?"</font> 🐀
            <font color="#f00">"Informatyk powinien umieć wszystko"</font> 🕷️
            <font color="#ff0">"Masz klucze do serwerowni?"</font> 🦇
          </marquee>
          <br><br>
          <button nomargin primary onclick="location.assign('/panel/login')">Przejdź do logowania</button>
          <!-- <button style="font-size: 70px; padding: 40px 90px; height: auto" nomargin onclick="location.assign('/panel/login')" primary>
            <font color="#ff0">Z</font><font color="#0ff">a</font><font color="#00f">l</font><font color="#f00">o</font><font color="#0f0">g</font><font color="#0af">u</font><font color="#fa0">j</font>
            <font color="#f0f">s</font><font color="#a0f">i</font><font color="#faf">ę</font>
          </button> -->
          <br>
          <br>
          <br>
          <br>
          <br>
          <h1>Chcesz unknąć tego typu treści?</h1>
          <h3 style="font-family: YouTube Light; color: #aaa">Wykup <font color="#ff0">pakiet premium</font> za jedynie <font color="#f0f">€99.99</font>!</h3>
          <h4 style="font-family: YouTube Light; color: #aaa">To zawsze mniej jak <font color="#f0f">€100.00</font>!</h4>
          <br>
          <h5 style="font-family: YouTube Light; color: #f00">Płatność tylko w nieoznakowanych banknotach!</h5>
          <h5 style="font-family: YouTube Light; color: #aaa">Adres do dokonania płatności: <a href="https://zrzutka.pl/z/DLAGRAFIKA">zrzutka.pl/z/DLAGRAFIKA</a></h5>
          <br>
          <h6 style="font: 10px YouTube Light; color: #aaa;">I tak cie nie stać</h6>
        </div>
      </div>
    <?php
  }
?>

