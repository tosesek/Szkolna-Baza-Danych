<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  cMustBeAdmin();
?>

<div class="page_generic">
  <div class="content">
    <h1>Narzędzia administratora</h1>
    Są to narzędzia, które pozwalają na łatwiejsze zarządzanie systemem.<br>
    <hr>

    <div class="admin-tools-list">

      <div updated class="item" onclick="location.assign('/panel/tools/manage_users.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/group.svg"></svg-icon>
          <span class="label">Konta użytkowników</span>
        </span>
        <span class="description">
          To narzędzie pozwala Ci zarządzać wszystkimi kontami użytkowników.<br>
          Możesz zatwierdzać oraz blokować konta.
        </span>
      </div>

      <div class="item" onclick="location.assign('/panel/tools/create_message.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/mailmessages.svg"></svg-icon>
          <span class="label">Wiadomości</span>
        </span>
        <span class="description">
          Tutaj możesz wysyłać wiadomości do poszczególnych użytkowników.<br>
          Wiadomości będą przekształcane do <a href="/panel/developer/email_test.php">domyślnego wyglądu wiadomości e-mail</a>!
        </span>
      </div>

      <div beta class="item" onclick="location.assign('/panel/tools/manage_server_settings.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/settings.svg"></svg-icon>
          <span class="label">Ustawienia serwera</span>
        </span>
        <span class="description">
          Dzięki temu narzędziu, możesz ustawić ogólne ustawienia systemu baz danych.<br>
          Chcesz ograniczyć ilość baz danych? To narzędzie jest do tego idealne.
        </span>
      </div>

      <div class="item" onclick="location.assign('/panel/tools/manage_bug_reports.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/bug.svg"></svg-icon>
          <span class="label">Zgłoszenia błędów</span>
        </span>
        <span class="description">
          Nawet na idealnych stronach zdarzają się błędy.<br>
          Dlatego powstało narzędzie do ich zgłaszania. Administracja weryfikuje, czy zgłoszenie jest odpowiednie, a następnie przekierowuje je do programistów.<br>
        </span>
      </div>

      <div class="item" onclick="ShowSystemStats()">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/stats.svg"></svg-icon>
          <span class="label">Statystyki systemu</span>
        </span>
        <span class="description">
          Aktualne informacje dotyczące wykorzystania plików i przestrzeni serwerowej.<br>
          Dane zostały pogrupowane, aby ich odczytywanie stało się prostsze
        </span>
      </div>

      <div new class="item" onclick="location.assign('/panel/tools/logs.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/logs.svg"></svg-icon>
          <span class="label">Dziennik systemu</span>
        </span>
        <span class="description">
          Podgląd wydarzeń, które odbyły się w systemie w określonym czasie.<br>
          Po wybraniu dnia, wyświetlone zostaną wszystkie wydarzenia, które zostały zarejestrowane.
        </span>
      </div>

      <div new class="item" onclick="location.assign('/panel/tools/file_browser.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/folder_multiple.svg"></svg-icon>
          <span class="label">Przeglądarka plików</span>
        </span>
        <span class="description">
          Zobacz zawartość głównego folderu na serwerze.<br>
          Możesz przeglądać pliki użytkowników, ale nie możesz ich modyfikować
        </span>
      </div>
      <div warning class="item" onclick="location.assign('/panel/tools/delete_all_data.php')">
        <span class="name">
          <svg-icon src="/panel/modules/images/icons/delete_sweep.svg"></svg-icon>
          <span class="label">Wyczyść dane systemu</span>
        </span>
        <span class="description">
          Usuń wszystkich użytkowników, klasy, bazy danych, konta FTP oraz pliki opublikowane na serwerze.
          Do działanie jest nieodrwacalne.
        </span>
      </div>

    </div>
  </div>
</div>

<script>

  $(document).ready(function() {
    $('[beta]').each(function() { $(this).attr('title', "To narzędzie już działa, ale może zawierać dużo błędów w funkcjonalności"); });
    $('[alpha]').each(function() { $(this).attr('title', "To narzędzie jest we wczesnej fazie tworzenia.\nNie oczekuj, że będzie ono działało prawidłowo"); });
    $('[new]').each(function() { $(this).attr('title', "Jest to nowe narzędzie, które wyszło z fazy Beta i nie powinno posiadać żadnych błędów"); });
    $('[updated]').each(function() { $(this).attr('title', "Nowa wersja tego narzędzia jest już dostępna"); });
  })

  function ShowSystemStats() {
    location.assign('/panel/tools/system_stats.php');
    OpenBlockingDialog('Obliczanie statystyk');
  }
</script>