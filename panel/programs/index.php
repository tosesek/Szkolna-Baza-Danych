<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
?>
<div class="page_generic">
  <div class="content">
    <h1>Programy</h1>
    Oto lista przydatnych według nas programów, których możesz używać podczas pisania stron internetowych.<br>
    <hr>
    <br><br>
    <h3>FileZilla</h3>
    Program, którego zalecamy używać podczas korzystania z naszego serwera FTP.<br>
    Wszystkie dane niezbędne do zalogowania się do serwera oraz link do programu podane są w <a href="/panel/settings/">Ustawieniach</a>
    <br><br>
    <h3>Visual Studio Code</h3>
    OpenSourc'owy edytor graficzny. Obsługuje wiele różnych języków programowania. Jeżeli nie wspiera danego języka domyślnie, zawsze można pobrać dodatek!<br>
    W edytor jest wbudowany menadżer dodatków, dzięki któremu dużo łatwiej zainstaować interesujące nas dodatki.<br>
    <a href="https://code.visualstudio.com/download">Link do programu</a><br>
    <br>
    <h4 style="padding-left:0.7em">Dodatki do VS Code, które mogą ci się przydać</h4>
    <div style="padding-left:2em">
    <br>
      <h5>Live Share</h5>
      <a target="_blank" href="https://marketplace.visualstudio.com/items?itemName=MS-vsliveshare.vsliveshare">Live Share</a> to dodatek do współpracy w czasie rzeczywistym na plikach w kilka osób!<br>
      Jeżeli pracujesz nad jakimś projektem grupowym, może okazać się niezbędny!<br>
      <br><h5>SFTP</h5>
      <a target="_blank" href="https://marketplace.visualstudio.com/items?itemName=liximomo.sftp">SFTP</a> Pozwoli Ci na automatyczne wysyłanie plików na serwer zaraz po zapisie!<br>
      Wszystko co musisz zrobić to wcisnąć <pre act>Ctrl+Shift+P</pre> wybrać opcję <pre act>SFTP: Config</pre>, zedytować utworzony konfig i gotowe!<br>
      Katalog: <pre act>"remotePath": "/var/www/html/nazwa_użytkownika_z_małej_litery"</pre><br>
      Jeżeli nie chcesz co chwilę wpisywać swojego hasła do FTP, wystarczy, że dodasz w konfiguracji tą liniję: <br>
      <pre act>"password": "Tutaj twoje hasło"</pre><br>
      <a href="/panel/programs/example_sftp.json">Przykładowy konfig do pobrania</a><br>
      <br><h5>PHP IntelliSense</h5>
      <a target="_blank" href="https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-intellisense">PHP IntelliSense</a>
      to prosty dodatek do prawidłowego rozpoznawania plików php i jeszcze lepszych podpowiedzi składni.
    </div>
  </div>
</div>
