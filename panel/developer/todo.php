<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
?>

<title>Lista rzeczy do zrobienia</title>

<div class="page_generic">
  <div class="content">
    <h1>Lista TODO</h1>
    <h5>Ukończone zadania zostały przyciemnione</h5>
    <hr>
    <pre>
      <?php
        $todo_source = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/todo');
        $todo_lines = explode("\n", $todo_source);
        foreach ($todo_lines as $key => $value) {
          if(strpos($value, "@done") !== false) {     
            $value = str_replace("@done", "", $value);
            $value = str_replace("//FIXME", "<b>[POPRAWIONE]</b>", $value);
            echo "<p style='opacity: 0.3'>".$value."</p>";
          }
          else {
            $value = str_replace("//FIXME", "<b>[DO POPRAWY]</b>", $value);
            echo $value;
          }
        }
      ?>
    </pre>
  </div>
</div>