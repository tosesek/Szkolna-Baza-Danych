<?php
  require_once $_SERVER['DOCUMENT_ROOT']."/panel/modules/mysql.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/panel/modules/functions.php";

  cPasswordMustBeValid();
  cCantBeBlocked();
?>
<head>
  <meta charset="utf-8">
  <title>Szkolna Baza Danych</title>

  <meta name="description" content="System hostowania stron internetowych oraz baz danych dla uczniów ZST w Pile">
  <meta name="keywords" content="database, system, ftp, zst, sbd, sdb, phpmyadmin, linux">
  <meta name="author" content="Paweł Depta, Tomasz Osesek, Kamil Rogalski">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="/panel/modules/css/style.css">
  <link rel="stylesheet" href="/panel/modules/css/dialogs.css">
  <link rel="stylesheet" href="/panel/modules/css/select.css">
  <?=(bIsDeveloper() ? '<link rel="stylesheet" href="/panel/modules/css/theme_green.css">' : '')?>
  <link rel="stylesheet" href="//static.wteam.pl/public/fonts/youtube/import.css">
  <link rel="stylesheet" href="//static.wteam.pl/public/fonts/stratum/import.css">

  <!-- Open Graph (np: linki na Facebook'u lub Messengerze) -->
  <meta property="og:url" content="https://sbd.zst.pila.pl/panel">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="pl_PL">
  <meta property="og:title" content="Szkolna Baza Danych">
  <meta property="og:description" content="System hostowania stron internetowych oraz baz danych dla uczniów ZST w Pile">
  <meta property="og:image" content="https://sbd.zst.pila.pl/panel/modules/images/og_main.png">

  <link rel="apple-touch-icon" sizes="180x180" href="/panel/modules/images/icon180x180.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/panel/modules/images/icon32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/panel/modules/images/icon16x16.png">

  <script src="/panel/modules/js/MD5.min.js"></script>
  <script src="/panel/modules/js/jquery.js"></script>
  <script src="/panel/modules/js/script.js"></script>
  <script src="/panel/modules/js/dialogs.js"></script>
  <script src="/panel/modules/js/select.js"></script>
  <script src="/panel/modules/js/chart.js"></script>

  <?php
    if(isset($_COOKIE['cookiesinfo_disabled']) && $_COOKIE['cookiesinfo_disabled']=='true') {
      @setcookie('cookiesinfo_disabled', 'true', strtotime('+30 days'), '/panel');
    }
  ?>
</head>

<div class="global_header">
  <div class="header_content">
    <div class="logo"><img src="/panel/modules/images/logo.svg" draggable="false"></div>
    <label onclick="location.assign('/panel')">SBD</label>
    <div class="links">
      <a href="/panel/">Strona główna</a>
      <?=(!bIsLoggedIn() ? '<a href="/panel/login">Zaloguj się</a>' : '')?>
      <?=(bIsLoggedIn() ? '<a href="/panel/programs">Programy</a>' : '')?>
      <?=(bIsAdmin() ? '<a href="/panel/tools">Narzędzia</a>' : '')?>
      <?=(bIsDeveloper() ? '<a dev href="/panel/developer">[Środowisko Deweloperskie]</a>' : '')?>
      <!-- <?=(bIsLoggedIn() ? '<a href="/panel/settings">Ustawienia</a>' : '')?> -->
      <a important target="_blank" href="https://zrzutka.pl/z/DLAGRAFIKA">Wesprzyj</a>
    </div>
    <?php
      if(bIsLoggedIn()) {
        ?>
          <a class="account_panel" href="javascript:ToggleAccountPanel()">
            <div class="account_details">
              <span class="fullname"><?=sGetFullName()?></span>
              <span class="username"><?=sGetName()?></span>
            </div>
          </a>
        <?php
      }      
    ?>
  </div>
</div>

<?php
  if(bIsLoggedIn()) {
    ?>
      <div class="account_panel_container">
        <div class="content">
          <div class="container">
            <div class="details">
              <span class="fullname"><?=sGetFullName()?></span>
              <span class="username"><?=sGetName()?></span>
            </div>
            <hr>
            <div class="actions">

              <div class="item" onclick="location.assign('/<?=strtolower(sGetName())?>')">
                <div class="icon">
                  <svg viewBox="0 0 24 24">
                    <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14M14.59,19.56C15.19,18.45 15.65,17.25 15.97,16H18.92C17.96,17.65 16.43,18.93 14.59,19.56M14.34,14H9.66C9.56,13.34 9.5,12.68 9.5,12C9.5,11.32 9.56,10.65 9.66,10H14.34C14.43,10.65 14.5,11.32 14.5,12C14.5,12.68 14.43,13.34 14.34,14M12,19.96C11.17,18.76 10.5,17.43 10.09,16H13.91C13.5,17.43 12.83,18.76 12,19.96M8,8H5.08C6.03,6.34 7.57,5.06 9.4,4.44C8.8,5.55 8.35,6.75 8,8M5.08,16H8C8.35,17.25 8.8,18.45 9.4,19.56C7.57,18.93 6.03,17.65 5.08,16M4.26,14C4.1,13.36 4,12.69 4,12C4,11.31 4.1,10.64 4.26,10H7.64C7.56,10.66 7.5,11.32 7.5,12C7.5,12.68 7.56,13.34 7.64,14M12,4.03C12.83,5.23 13.5,6.57 13.91,8H10.09C10.5,6.57 11.17,5.23 12,4.03M18.92,8H15.97C15.65,6.75 15.19,5.55 14.59,4.44C16.43,5.07 17.96,6.34 18.92,8M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" />
                  </svg>
                </div>
                <span class="label">Pokaż moją stronę</span>
              </div>

              <div class="item" onclick="location.assign('/panel/settings')">
                <div class="icon">
                  <svg viewBox="0 0 24 24">
                    <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
                  </svg>
                </div>
                <span class="label">Ustawienia konta</span>
              </div>

              <hr>

              <div class="item" onclick="location.assign('/panel/bugs')">
                <div class="icon">
                  <svg viewBox="0 0 24 24">
                    <path d="M14,12H10V10H14M14,16H10V14H14M20,8H17.19C16.74,7.22 16.12,6.55 15.37,6.04L17,4.41L15.59,3L13.42,5.17C12.96,5.06 12.5,5 12,5C11.5,5 11.04,5.06 10.59,5.17L8.41,3L7,4.41L8.62,6.04C7.88,6.55 7.26,7.22 6.81,8H4V10H6.09C6.04,10.33 6,10.66 6,11V12H4V14H6V15C6,15.34 6.04,15.67 6.09,16H4V18H6.81C7.85,19.79 9.78,21 12,21C14.22,21 16.15,19.79 17.19,18H20V16H17.91C17.96,15.67 18,15.34 18,15V14H20V12H18V11C18,10.66 17.96,10.33 17.91,10H20V8Z" />
                  </svg>
                </div>
                <span class="label">Zgłoś błąd</span>
              </div>

              <div class="item" onclick="ShowSystemAuthors()">
                <div class="icon">
                  <svg viewBox="0 0 24 24">
                    <path d="M14.6,16.6L19.2,12L14.6,7.4L16,6L22,12L16,18L14.6,16.6M9.4,16.6L4.8,12L9.4,7.4L8,6L2,12L8,18L9.4,16.6Z" />
                  </svg>
                </div>
                <span class="label">Pokaż twórców systemu</span>
              </div>

              <hr>
              
              <div class="item" onclick="LogOut()">
                <div class="icon">
                  <svg viewBox="0 0 24 24">
                    <path d="M13.34,8.17C12.41,8.17 11.65,7.4 11.65,6.47A1.69,1.69 0 0,1 13.34,4.78C14.28,4.78 15.04,5.54 15.04,6.47C15.04,7.4 14.28,8.17 13.34,8.17M10.3,19.93L4.37,18.75L4.71,17.05L8.86,17.9L10.21,11.04L8.69,11.64V14.5H7V10.54L11.4,8.67L12.07,8.59C12.67,8.59 13.17,8.93 13.5,9.44L14.36,10.79C15.04,12 16.39,12.82 18,12.82V14.5C16.14,14.5 14.44,13.67 13.34,12.4L12.84,14.94L14.61,16.63V23H12.92V17.9L11.14,16.21L10.3,19.93M21,23H19V3H6V16.11L4,15.69V1H21V23M6,23H4V19.78L6,20.2V23Z" />
                  </svg>
                </div>
                <span class="label">Wyloguj</span>
              </div>

            </div>
          </div>
        </div>
      </div>
    <?php
  }
?>

<div class="app_authors">
  
  <div class="item" onclick="ShowSystemAuthors()" title="Pokaż twórców systemu">
    <svg viewBox="0 0 24 24">
      <path fill="#fff" d="M14.6,16.6L19.2,12L14.6,7.4L16,6L22,12L16,18L14.6,16.6M9.4,16.6L4.8,12L9.4,7.4L8,6L2,12L8,18L9.4,16.6Z" />
    </svg>
  </div>

  <div class="item" onclick="location.assign('/panel/bugs');" title="Zgłoś błąd">
    <svg viewBox="0 0 24 24">
      <path fill="#fff" d="M14,12H10V10H14M14,16H10V14H14M20,8H17.19C16.74,7.22 16.12,6.55 15.37,6.04L17,4.41L15.59,3L13.42,5.17C12.96,5.06 12.5,5 12,5C11.5,5 11.04,5.06 10.59,5.17L8.41,3L7,4.41L8.62,6.04C7.88,6.55 7.26,7.22 6.81,8H4V10H6.09C6.04,10.33 6,10.66 6,11V12H4V14H6V15C6,15.34 6.04,15.67 6.09,16H4V18H6.81C7.85,19.79 9.78,21 12,21C14.22,21 16.15,19.79 17.19,18H20V16H17.91C17.96,15.67 18,15.34 18,15V14H20V12H18V11C18,10.66 17.96,10.33 17.91,10H20V8Z" />
    </svg>
  </div>

</div>

<?php
  if(!isset($_COOKIE['cookiesinfo_disabled']) || $_COOKIE['cookiesinfo_disabled']=='false') {
    ?>
      <div class="cookiesinfo_container">
        <div class="content">
          <div class="info">
            <span class="title">Informacja o ciasteczkach</span>
            <span class="desc">
              System <b>Szkolnej Bazy Danych</b> korzysta z plików cookies (tzw. ciasteczka) w celach funkcjonalnych.<br>
              Dzięki nim możemy indywidualnie dostosować zawartość strony do Twoich potrzeb. Każdy może zaakceptować pliki Cookies, lub może je wyłączyć w ustawieniach przeglądarki, przez co na Twoim urządzeniu nie będą zapisywane żadne dane.<br>
            </span>
          </div>
          <div class="buttons">
            <button nomargin onclick="DisableCookies();">Chcę wyłączyć pliki Cookies</button>
            <button primary onclick="AcceptCookies();">Akceptuję pliki Cookies</button>
          </div>
        </div>
      </div>
    <?php
  }
?>

<div class="global_dialogs_holder">
  <div class="background_blur"></div>
  <div class="background"></div>
</div>

<script>
  <?php
    if(isset($_REQUEST['a']) && $_REQUEST['a']==1){

    }
    else {
      if(stLogin()) {
        ?>
            OpenGenericDialog('Wymagana akcja!', 'Zalogowałeś się po raz pierwszy. Zmień hasło! <br>'+
            'Aby to zrobić, przejdź do zakładki <a href="/panel/settings/?a=1">ustawień</a>.');
        <?php
      }
    }
  ?>
</script>