<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
?>

<title>Biblioteka kontrolek</title>

<div class="page_generic">
  <div class="content">
    <h1>Biblioteka kontrolek</h1>
    <hr>
    Ten tekst jest wyświetlany bez żadnego formatowania.<br>
    <br>
    <p>To jest paragraf</p>
    <br>
    <a href="">To jest link</a><br>
    <br>
    <hr>
    <br>
    <button nomargin onclick="OpenGenericDialog('Okno dialogowe', 'Treść okna dialogowego<br>Z kilkoma linijkami<br>I super długą treścią znajdującą się w jednej i tej samej linijce. Spróbujcie to pobić :) Powodzenia!')">Domyślne okno dialogowe</button>
    <button primary onclick="OpenGenericDialog('Okno dialogowe', 'Treść okna dialogowego<br>Z kilkoma linijkami<br>I super długą treścią znajdującą się w jednej i tej samej linijce. Spróbujcie to pobić :) Powodzenia!', [{'text':'Ten przycisk nic nie robi!'},{'text':'Zamknij','primary':true,'callback':function(did) {CloseDialog(did);}}])">Okno dialogowe z przyciskami</button>
    <br>
    <br>
    <hr>
    <br>
    <table cellspacing="0">
      <thead>
        <tr><td>Kolumna 1</td><td>Kolumna 2</td><td>Kolumna 3</td></tr>
      </thead>
      <tbody>
        <tr><td>Komórka w wierszu 1</td><td>Komórka w wierszu 1</td><td><button dialog-button nomargin>1</button><button dialog-button primary>2</button><button dialog-button critical>3</button></td></tr>
        <tr><td align-right>Komórka w wierszu 2 od prawej</td><td align-right>Komórka w wierszu 2 od prawej</td><td><button dialog-button nomargin>1</button><button dialog-button primary>2</button><button dialog-button critical>3</button></td></tr>
        <tr><td align-right>Komórka w wierszu 3 od prawej</td><td align-right>Komórka w wierszu 3 od prawej</td><td><button dialog-button nomargin>1</button><button dialog-button primary>2</button><button dialog-button critical>3</button></td></tr>
        <tr><td>Komórka w wierszu 4</td><td>Komórka w wierszu 4</td><td><button dialog-button nomargin>1</button><button dialog-button primary>2</button><button dialog-button critical>3</button></td></tr>
        <tr><td>Komórka w wierszu 5</td><td>Komórka w wierszu 5</td><td><button dialog-button nomargin>1</button><button dialog-button primary>2</button><button dialog-button critical>3</button></td></tr>
      </tbody>
    </table>
    <br>
    <hr>
    <h1>Nagłówek stopnia 1</h1>
    <h2>Nagłówek stopnia 2</h2>
    <h3>Nagłówek stopnia 3</h3>
    <h4>Nagłówek stopnia 4</h4>
    <h5>Nagłówek stopnia 5</h5>
    <h6>Nagłówek stopnia 6</h6>
    <br>
    <hr>
    <br>
    <input type="text" placeholder="Tekst">
    <input type="password" placeholder="Hasło">
    <input type="number" placeholder="Numer">
    <input type="datetime-local" placeholder="Data">
    <input type="time" placeholder="Czas">
    <br><br>
    <textarea placeholder="Pole tekstowe"></textarea>
    <br>
    <hr>
    <br>
    <button nomargin>Zwykły przycisk</button>
    <button primary>Główny przycisk</button>
    <button critical>Krytyczny przycisk</button>
  </div>
</div>