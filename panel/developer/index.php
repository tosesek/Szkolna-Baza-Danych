<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeDeveloper();
?>

<div class="page_generic">
  <div class="content">
    <h1>Narzędzia deweloperskie</h1>
    <h5>Ta strona nie jest dostępna dla zwykłego użytkownika</h5>
    <hr>

    <div class="admin-tools-list">

      <div class="item" onclick="location.assign('/panel/developer/controls.php')">
        <span class="name"><span class="label">Środowisko paneli kontrolnych</span></span>
        <span class="description">
          To narzędzie pozwala na podgląd wszystkich aktualnie dostępnych kontrolek w systemie.
        </span>
      </div>

      <div class="item" onclick="location.assign('/panel/developer/todo.php')">
        <span class="name"><span class="label">Lista rzeczy do zrobienia</span></span>
        <span class="description">
          Lista rzeczy, które oczekują na zrobienie/naprawienie, oraz rzeczy, które zostały już ukończone.
        </span>
      </div>

      <div class="item" onclick="location.assign('/panel/developer/email_test.php')">
        <span class="name"><span class="label">Podgląd wiadomości email</span></span>
        <span class="description">
          To narzędzie przekieruje Cię na stronę wyświetlającą szablon wiadomości email, która wysyłana jest do użytkowników ze zmienioną treścią.
        </span>
      </div>

      <div class="item" onclick="location.assign('/panel/developer/default_page_view.php')">
        <span class="name"><span class="label">Podgląd strony domyślnej</span></span>
        <span class="description">
          To narzędzie przekieruje Cię na stronę wyświetlającą szablon domyślnej strony startowej w folderze użytkownika, która jest tworzona automatycznie po zatwierdzeniu użytkownika.
        </span>
      </div>

    </div>
  </div>
</div>