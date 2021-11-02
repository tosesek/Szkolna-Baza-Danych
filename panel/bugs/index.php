<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  ?>
<div class="page_generic">
  <div class="content">
  <h1>Zgłaszanie błędów</h1>
    Coś nie działa? Znalazłeś błąd? Napisz do nas o tym!<br><br>
    <br>
    <select id="bug_type">
      <option selected disabled>Czego dotyczy zgłoszenie?</option>
      <option value="ftp">Problem dotyczy FTP</option>
      <option value="mysql">Problem dotyczy MySQL lub phpMyAdmin</option>
      <option value="panel">Problem dotyczy strony internetowej</option>
      <option value="inne">Inny problem</option>
    </select><br><br><br>
    <input type="text" placeholder="Tytuł zgłoszenia" style="display: block; width:100%;" id="reportTitle"><br>
    <textarea placeholder="Opisz nam swój błąd. Ułatwi nam to zidentyfikowanie przyczyny." id="reportContent"></textarea>
    <br><br>
    <div align-right>
      <button onclick="location.assign('/panel/')">Powrót na stronę główną</button>
      <button primary onclick="reportBug()">Wyślij</button>
    </div>

  
    <script>
      function reportBug(){
        let select=$('#bug_type_select');
        let type=$('#bug_type');
        let title = $('#reportTitle');
        let content = $('#reportContent');
        let localok=true;
        if (!ValidateInput(type)) {localok = false; SetInputError(select)} else { SetInputOk(select); };
        if (!ValidateInput(title)) localok = false;
        if (!ValidateInput(content)) localok = false;
        if(localok) {
          var dialog = OpenGenericDialog(
          "Wysłać zgłoszenie?",
          "Masz zamiar wysłać zgłoszenie dotyczące <b>"+type.val()+"</b>!<br>"+
          "Twoje zgłoszenie zostanie wysłane do weryfikacji przez administrację.",
          [
            {
              "text": "Anuluj",
              "callback": function(did) {
                CloseDialog(did);
              }
            },
            {
              "text": "Wyślij",
              "primary": true,
              "callback": function(did) {
                var blocking = OpenBlockingDialog("Czekaj...");
                $.post("/panel/modules/requests.php", {
                  "action": "ReportBug",
                  "type":type.val(),
                  "title":title.val(),
                  "content":content.val()
                }, function(data) {
                  CloseDialog(blocking);
                  if(data=='success') {
                    CloseDialog(did);
                    OpenGenericDialog("Sukces", "Twoje zgłoszenie zostało przekazane do weryfikacji.<br>"+
                    "W zależności od decyzji, błędy zostaną poprawione w jednej z najbliższych aktualizacji.", [
                      {
                        "text": "Anuluj",
                        "callback": function(did) {
                          CloseDialog(did);
                        }
                      },
                      {
                        "text": "Ok",
                        "callback": function(did){
                          CloseDialog(did);
                          location.assign('/panel/')
                        }
                      }
                    ]);
                  }
                  else {
                    CloseDialog(did);
                    OpenGenericDialog("Wystąpił błąd", "Nie udało się wysłać twojego błędu.<br><br>Sprawdź czy zgłoszenie nie zawiera niepoprawnych znaków<br> lub spróbuj ponownie później.", -1);
                    title.attr('status', 'error')
                    content.attr('status', 'error')
                  }
                })
              }
            }
          ]
        )
      }
      else {
        OpenGenericDialog("Wystąpił błąd", "Musisz najpierw uzupełnić formularz zgłoszenia!", -1);
      }
    }
    </script>
  </div>
</div>
