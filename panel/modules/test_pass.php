<?php
function generateRandomPassword($mode= array(true,false,true,true), $length=12){
                        // Liczby, Znaki Specjalne, Małe litery, Duże litery
  // $config['mode'] = array(  true,     false,          true,         true); 
  $letters = 'abcdefghijklmnopqrstuvwxyz';
  // Liczby
  if($mode[0])
  {
    $values = '0123456789';
  }
  // Znaki specjalne
  if($mode[1])
  {
    $values .= '`~!@#$%^&*()_-=+<>?,.|\/\'";:[]{}';
  }
  // Małe litery
  if($mode[2])
  {
    $values .= $letters;
  }
  // Duże litery
  if($mode[3])
  {
    $values .= strtoupper($letters);
  }
  for($h = 0, $length = (strlen($values) - 1); $h < $length; ++$h)
  {
    $random_symbols .= substr($values, mt_rand(0, $length), 1);
  }
  return htmlspecialchars($random_symbols);
}
?>