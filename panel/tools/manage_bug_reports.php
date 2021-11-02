<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeLoggedIn();
  cMustBeAdmin();
?>
<div class="page_generic">
  <div class="content">
  <h1>Otrzymane zgłoszenia</h1>
  Oto lista błędów zgłoszonych przez użytkowników systemu.<br>
  Sprawdź, czy są one warte uwagi, zatwierdź i poinformuj w ten sposób programistów.<br><br>
  <table cellspacing="0" id="bug_reports_list">
      <thead>
        <tr>
          <td>Autor</td>
          <td style="width: 100px !important">Typ błędu</td>
          <td>Tytuł</td>
          <td>Treść</td>
          <td align-right style="width: 1px" align-right>Narzędzia</td>
        </tr>
      </thead>
      <tbody>
        <?php

          $query = mysqli_query($_DB, "SELECT * FROM bugs ORDER BY `status` DESC");
          while($r = mysqli_fetch_assoc($query)) {
            ?>
              <tr reportid="<?=$r['id']?>" <?=($r['status']==1 ? 'highlight': '')?>>
                <td><?=sGetName($r['user_id'])?></td>
                <td><?=$r['type']?></td>
                <td><?=$r['title']?></td>
                <td><?=$r['content']?></td>
                <td align-right>
                <?php $id=$r['id'];?>
                  <button nomargin dialog-button critical onclick="DeleteReport(<?=$r['id']?>)">Oznacz jako rozwiązane</button>
                  <?=($r['status']==0 ? "<button nomargin dialog-button primary id='accept$id' onclick='AddToTodoList($id)'>Zatwierdź</button>" : '')?>                  
                </td>
              </tr>
            <?php
          }
        ?>
      </tbody>
    </table>
    <script>
      function AddToTodoList(id){
        OpenGenericDialog("Uwaga!", "Masz zamiar zatwierdzić zgłoszenie <b>#"+id+"</b><br>"+
        "Jeżeli to zrobisz, przekażesz infomrację o istniejącym błędzie.<br>"+
        "Błąd zostanie poprawiony w jednej z najbliższych aktualizacji.",
        [
          {
            "text": "Anuluj",
            "callback": function(did) {
              CloseDialog(did);
            }
          },
          {
            "text": "Zatwierdź",
            "primary": true,
            "callback": function(did) {
              var blocking = OpenBlockingDialog("Czekaj...");
              $.post("/panel/modules/requests.php", {
                'action': 'AcceptReport',
                'id': id
              }, function(data) {
                CloseDialog(blocking);
                CloseDialog(did);
                if(data=='success') {
                  OpenGenericDialog("Sukces", "Zgłoszenie zostało zatwierdzone!", -1);
                  $('#bug_reports_list [reportid='+id+']').attr('highlight', '');
                  $('#bug_reports_list [reportid='+id+'] #accept'+id).remove();
                }
                else {
                  OpenGenericDialog("Wystąpił błąd", 'Nie udało się zatwierdzić zgłoszenia błędu.<br>Skontaktuj się z programistą');
                }
              });
            }
          }
        ]);
      }
      function DeleteReport(id) {
        OpenGenericDialog("Uwaga!", "Masz zamiar oznacz zgłoszenie <b>#"+id+"</b> jako rozwiązane<br>"+
        "Jeżeli to zrobisz, usuniesz z bazy danych wszystkie informacje dotyczących zgłoszenia", 
        [
          {
            "text": "Anuluj",
            "callback": function(did) {
              CloseDialog(did);
            }
          },
          {
            "text": "Oznacz jako ukończone",
            "primary": true,
            "callback": function(did) {
              var blocking = OpenBlockingDialog("Czekaj...");
              $.post("/panel/modules/requests.php", {
                'action': 'DeleteReport',
                'id': id
              }, function(data) {
                CloseDialog(blocking);
                CloseDialog(did);
                if(data=='success') {
                  OpenGenericDialog("Sukces", "Zgłoszenie <b>#"+id+"</b> zostało oznaczone jako rozwiązane!", -1);
                  $('#bug_reports_list [reportid='+id+']').remove();
                }
                else {
                  OpenGenericDialog("Wystąpił błąd", 'Nie udało się oznaczyć zgłoszenia jako rozwiązane.<br>Skontaktuj się z programistą');
                }
              });
            }
          }
        ])
      }
    </script>
  </div>
</div>