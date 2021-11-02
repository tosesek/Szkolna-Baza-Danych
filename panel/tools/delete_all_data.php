<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
cMustBeAdmin();
?>

<div class="page_generic">
  <div class="content">
    <h1>Czyszczenie systemu</h1>
    <br>
    <b>To narzędzie spowoduje:</b><br>
    <ul>
      <li>Utworzenie kopii zapasowej bazy danych</li>
      <li>Utworzenie kopii zapasowej dziennika systemu</li>
      <li>Usunięcie wszystkich użytkowników, którzy nie są oznaczeni jako administratorzy, nauczyciele i twórcy</li>
      <li>Usunięcie wszystkich plików i folderów użytkowników</li>
      <li>Usunięcie wszystkich kont MySQL</li>
      <li>Usunięcie wszystkich kont FTP</li>
      <li>Usunięcie wszystkich baz danych, które były przypisane do usuniętych użytkowników</li>
    </ul>
    <br>
    <h3>Ważna informacja!</h3>
    <b>Następujące rzeczy <u>NIE ZOSTANĄ</u> wykonane:</b>
    <ul>
      <li>Nie zostanie utworzona kopia zapasowa plików użytkowników</li>
      <li>Nie zostanie utworzona kopia zapasowa baz danych użytkowników</li>
    </ul>
    <br>
    <h5>Jeżeli nadal nie masz pewności co do działania tego narzędzia, skontaktuj się z <a href="javascript:ShowSystemAuthors()">twórcami systemu</a>.</h5>
    <br>
    <hr>
    <h3>Potwierdzenie usunięcia danych</h3>
    Aby wyczyścić system, wpisz w poniższe pole tekst <b>"Rozumiem, usuń dane"</b>:<br><br>
    <input id="wipe_confirmation" type="text" placeholder="Potwierdź usunięcie..." disabled><button title="Opcja nie jest jeszcze dostępna" disabled critical onclick="WipeAllData()">Usuń dane</button>
  </div>
</div>

<script>
  function WipeAllData() {
    var confirmation = $('#wipe_confirmation');
    var localok = true;

    if(!ValidateInput(confirmation)) {localok = false;}
    if(confirmation.val().toLowerCase() !== "rozumiem, usuń dane") {localok = false; SetInputError(confirmation);}

    if(localok) {
      var blocking = OpenBlockingDialog('Czyszczenie systemu');
      // wypierdolić wszystko w chuj
      $.post("/panel/modules/requests.php", {
        'action': 'WipeAllSystemData'
      }, function(data) {
        if(data=='success') {
          OpenGenericDialog('Czyszczenie systemu', 'Wszystkie dane zapisane na serwerze zostały wyczyszczone pomyślnie');
        }
        else {
          OpenGenericDialog('Czyszczenie systemu', 'Nie udało się wyczyścić danych zapisanych na serwerze. Skontaktuj się z twórcą systemu');
        }
        CloseDialog(blocking);
      });
    }
  }
</script>