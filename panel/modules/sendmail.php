<?php
  require_once "Mail.php";
  function sendMail($email, $subject, $content, $headers, $echo=true){
    $headers['MIME-Version']  = '1.0';
    $headers['Content-type']  = 'text/html; charset=utf-8';
    $headers['From']          = "\"Szkolna Baza Danych\" <noreply@zst.pila.pl>";

    $host = "";
    $username = "";
    $password = "";
    $smtp = Mail::factory('smtp', array(
      'host' => 'ssl://'.$host,
      'port' => '465',
      'auth' => true,
      'debug' => false,
      'pipelining' => false,
      'username' => $username,
      'password' => $password
    ));
    $mail = $smtp->send($email, $headers, $content);
    if($echo){
      if (PEAR::isError($mail)) {
        echo 'mail_send_error';
        logs("SERVER", "SENDMAIL", "error", array("email" => $email, "subject" => $subject));
      } else {
        logs("SERVER", "SENDMAIL", "success", array("email" => $email, "subject" => $subject));
        echo 'success';
      }
    }
    else{
      if (PEAR::isError($mail)) {
        logs("SERVER", "SENDMAIL", "error", array("email" => $email, "subject" => $subject, "error-code" => $mail->getMessage()));
      } else {
        logs("SERVER", "SENDMAIL", "success", array("email" => $email, "subject" => $subject));
      }
    }
  }
?>