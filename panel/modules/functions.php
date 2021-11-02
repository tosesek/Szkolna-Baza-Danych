<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/logs.php';
  session_start();
  $load_once_translation = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/panel/modules/translations.json');
  $load_once_translation = json_decode($load_once_translation);


  function bIsLoggedIn() {
    if(!@$_SESSION['zalogowany']) return false;
    return true;
  }
  function bIsAdmin($userid=-1) {
    global $_DB;
    if(!bIsLoggedIn()) return false;

    $userid = ($userid==-1 ? $userid = @$_SESSION['uuid'] : $userid);
    $data = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$userid");
    $f = mysqli_fetch_assoc($data);
    $admin = ($f['privileges']==1 ? true : false);
    return $admin;
  }
  function bIsEmailRegistered($email) {
    global $_DB;
    $q = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users WHERE `email`='$email'"));
    if($q) return true;
    return false;
  }
  function bIsDeveloper($userid=-1) {
    global $_DB;
    if(!bIsLoggedIn()) return false;

    $userid = ($userid==-1 ? $userid = @$_SESSION['uuid'] : $userid);
    $data = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$userid AND `developer`=1");
    if(mysqli_num_rows($data)==1) return true;
    return false;
  }


  function cMustBeLoggedIn() {
    if(!bIsLoggedIn()) {
      header('location: /panel/login');
      exit();
    }
  }
  function cMustBeAdmin() {
    if(!bIsAdmin()) {
      header('location: /panel/');
      exit();
    }
  }
  function cPasswordMustBeValid() {
    global $_DB;
    if(bIsLoggedIn()) {
      $password_hash = $_SESSION['hash'];
      $id = $_SESSION['uuid'];
      $qr = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$id AND `password`='$password_hash'");
      if(mysqli_num_rows($qr)==0) {
        header('location: /panel/logout?type=password_changed');
        logs($_SESSION['uuid'], "logout_password_changed", "Password has changed. Hacker?");
        exit();
      }
    }
  }
  function cCantBeBlocked() {
    global $_DB;
    if(bIsLoggedIn()) {
      $id = $_SESSION['uuid'];
      $qr = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$id AND `blocked`=1");
      if(mysqli_num_rows($qr)!=0) {
        header('location: /panel/logout?type=account_blocked');
        exit();
      }
    }
  }
  function cMustBeDeveloper() {
    if(!bIsDeveloper()) {      
      header('location: /panel/');
      exit();
    }
  }
  
  function sGetFullName($userid=-1) {
    global $_DB;
    $userid = ($userid==-1 ? $userid = @$_SESSION['uuid'] : $userid);

    $data = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$userid");
    $f = mysqli_fetch_assoc($data);
    
    return $f['first_name'].' '.$f['last_name'];
  }
  function sGetFirstName($userid=-1) {
    global $_DB;
    $userid = ($userid==-1 ? $userid = @$_SESSION['uuid'] : $userid);

    $data = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$userid");
    $f = mysqli_fetch_assoc($data);
    
    return $f['first_name'];
  }
  function sGetName($userid=-1) {
    global $_DB;
    $userid = ($userid==-1 ? $userid = @$_SESSION['uuid'] : $userid);

    $data = mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$userid");
    $f = mysqli_fetch_assoc($data);
    
    return $f['name'];
  }

  function sGenerateMailContent($mail_title, $first_name, $mail_content) {
    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/panel/modules/mail_template.php');

    $body = str_replace("{s:mail_title}", $mail_title, $body);
    $body = str_replace("{s:firstname}", $first_name, $body);
    $body = str_replace("{s:mail_content}", $mail_content, $body);

    return $body;
  }
  function sGenerateDefaultPage($fullname, $firstname, $username) {
    $body = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/panel/modules/templates/default_page.php');

    $body = str_replace("{s:head_fullusername}", $fullname, $body);
    $body = str_replace("{s:firstname}", $firstname, $body);
    $body = str_replace("{s:username}", $username, $body);

    return $body;
  }
  function sFormatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1) {
      $bytes = $bytes . ' B';
    }
    elseif ($bytes == 1) {
      $bytes = $bytes . ' b';
    }
    else {
      $bytes = '0 B';
    }

    return $bytes;
  }
  function sGetUserDataByLogin($login) {
    global $_DB;
    $qr = mysqli_query($_DB, "SELECT * FROM users WHERE `name` LIKE '%$login%'");
    if(mysqli_num_rows($qr)) {
      $f = mysqli_fetch_array($qr);
      return $f;
    }
    else {
      return false;
    }
  }  
  function sGetMonthName($month) {
    switch($month) {
      case 1: return 'stycznia'; break;
      case 2: return 'lutego'; break;
      case 3: return 'marca'; break;
      case 4: return 'kwietnia'; break;
      case 5: return 'maja'; break;
      case 6: return 'czerwca'; break;
      case 7: return 'lipca'; break;
      case 8: return 'sierpnia'; break;
      case 9: return 'września'; break;
      case 10: return 'października'; break;
      case 11: return 'listopada'; break;
      case 12: return 'grudnia'; break;
      default: return '-unknown-';
    }
  }
  function sGetDayName($day) {
    switch($day) {
      case 1: return 'Poniedziałek'; break;
      case 2: return 'Wtorek'; break;
      case 3: return 'Środa'; break;
      case 4: return 'Czwartek'; break;
      case 5: return 'Piątek'; break;
      case 6: return 'Sobota'; break;
      case 0: return 'Niedziela'; break;
      default: return '-unknown-';
    }
  }
  function sFormatDate($date, $showtime=false, $showday=false) {
    $format = "";
    if($showday==true) {
      $format .= sGetDayName(date("w", $date)).', ';
    }

    $format .= date("d", $date)." ".sGetMonthName(date("m", $date))." ".date("Y", $date);

    if($showtime==true) {
      $format .= ", ".date("H:i", $date);
    }

    return $format;
  }
  function sFormatTime($time) {
    $hours = floor($time / 3600);
    $minutes = floor(($time / 60) % 60);
    $seconds = $time % 60;
    return "$hours godz, $minutes min, $seconds sek";
  }
  function Translate($key, $data=null) {
    global $load_once_translation;
    $key = strtolower($key);
    $key = str_replace("-", "_", $key);
    $key = str_replace(" ", "_", $key);
    $key = preg_replace("/[^A-Za-z0-1_]/", "", $key);
    if($data==null) {
      if(@$load_once_translation->$key) {
        return $load_once_translation->$key;
      }
      else {
        return $key;
      }
    }
    else {
      if(!@$load_once_translation->$key) return $key;
      preg_match_all('#\{(.*?)\}#', $load_once_translation->$key, $match);
      $translation = $load_once_translation->$key;
      foreach ($match[1] as $key => $value) {
        $fullkeyname_int = $match[0][$key];
        $fullkeyname = $match[1][$key];
        $translate_data = explode(":", $fullkeyname);

        $type = $translate_data[0]; $arg = $translate_data[1];
        $retval = "";
        $arg_set = GetArrayValueByKey($data, $arg);
        $forcebold = true;
        switch($type) {
          case 'fullname': $retval = sGetFullName($arg_set); break;
          case 'i': $retval = $arg_set; break;
          case 's': $retval = $arg_set; break;
          case 'date': $retval = FormatDate($arg_set, true, true); break;
          case 'translate': {
            $test = str_replace("{".$fullkeyname."}", "", $translation);
            if($test=="") $forcebold = false; else $forcebold = true;
            $retval = Translate((@$translate_data[2] ? $translate_data[2].'_' : '').$arg_set.(@$translate_data[3] ? '_'.$translate_data[3] : ''), $data);
            break;
          }
          default: $retval = $key; break;
        }
        // echo (array_key_exists($arg, $data) ? 'tak' : 'nie').'<br>';
        if($forcebold) $translation = str_replace($fullkeyname_int, '<b>'.$retval.'</b>', $translation);
        else $translation = str_replace($fullkeyname_int, $retval, $translation);

      }
      return $translation;
    }
  }
  function GetArrayValueByKey($array, $find) {
    if(array_key_exists($find, $array)) {
      return $array[$find];
    }
    else {
      foreach ($array as $key => $val) {
        if(is_array($val)) {
          return GetArrayValueByKey($val, $find);
        }
        // GetArrayValueByKey($val, $find);
      }
    }
  }


  function generateRandomPassword($mode= array(true,false,true,true), $passlength=12){
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
    $random_symbols='';
    for($h = 0, $length = (strlen($values) - 1); $h < $passlength; ++$h)
    {
      $random_symbols .= substr($values, mt_rand(0, $length), 1);
    }
    return $random_symbols;
  }
  function stLogin(){
    global $_DB;
    if(!bIsLoggedIn()) return false;

    $userid = @$_SESSION['uuid'];
    $data = mysqli_query($_DB, "SELECT * FROM `users` WHERE `id`=$userid AND `stLogin`=1");
    if(mysqli_num_rows($data)==1) return true;
    return false;
  }
?>