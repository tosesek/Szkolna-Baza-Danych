<?php
    include "sendmail.php";
    $email = "tomek121200@gmail.com";
    $subject = "testEmail";
    $content = "Wszystko dziala";
    $headers['MIME-Version']  = '1.0';
    $headers['Content-type']  = 'text/html; charset=iso-8859-1';
    $name       ="Tomasz Osesek";
    $email      = "tomek121200@gmail.com";
    $headers['To']  = $name." <".$email.">";
    $headers['From']  = "\"Usługi Hostingowe Tomasz Osesek\" <rp12332100@gmail.com>";
    $headers['Subject']  = $subject;
    sendMail($email, $subject, $content, $headers);
?>