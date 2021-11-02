<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/functions.php';

  echo sGenerateMailContent(
    "Tutaj można umieścić tytuł wiadomości (zmienna A)", 
    "Zmienna B", 
    "Jest to przykładowa treść wiadomości e-mail, która wysyłana jest do użytkowników systemu. 
    Nie jest zalecane używanie zaawansowanych stylów, ponieważ każda poczta inaczej odczytuje ich zawartość.<br><br>
    Zawartość tej strony pobierana jest z pliku <b>/panel/modules/mail_template.php</b>, a następnie odpowiednie zmienne podane w funkcji <b>sGenerateMailContent(a, b, c)</b> są zamieniane aby powstała poprawna struktura wiadomości.<br><br><br>
    <b>Zmienna A</b><br>Odpowiada za tytuł wiadomości (wyświetlany obok loga szkoły, pod nazwą systemu)<br><br>
    <b>Zmienna B</b><br>Odpowiada za imię użytkownika, do którego dana wiadomość jest wysyłana<br><br>
    <b>Zmienna C</b><br>Odpowiada za treść wiadomości<br><br><br>
    <a href='/panel/developer'>Przejdź na stronę główną narzędzi deweloperskich"
  );
?>