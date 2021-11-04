<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/mysql.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/functions.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/sendmail.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/logs.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/config/server_settings.php';
  
  $action = @$_REQUEST['action'];

  // dla wszystkich
  if($action=="resetPasswordMail") {
    $email=$_REQUEST['email'];
    if(!bIsEmailRegistered($email)) {
      logs($email, $action, "not_exists");
      echo 'not-extists';
      exit();
    }
    $reset_password_id=md5(sha1(uniqid()));
    $query="UPDATE users SET `reset_password_id` = '".$reset_password_id."' WHERE `email`='".$email."'";
    mysqli_query($_DB, $query) or die(mysqli_error($_DB));
    $query="SELECT * FROM users WHERE email = '$email' and reset_password_id = '$reset_password_id'";
    $result=mysqli_fetch_assoc(mysqli_query($_DB, $query));
    $subject    = "SBD: Przywracanie hasła";
    $content    = sGenerateMailContent("Resetowanie hasła", $result['first_name'], "
    Wygląda na to, że nie możesz się zalogować...<br> 
    Aby zresetować hasło kliknij w <a href='https://sbd.zst.pila.pl/panel/password/?email=".$email."&reset=".$reset_password_id."'>ten link</a>.
    ");
    $name = $result['first_name'].' '.$result['last_name'];
    
    $headers['To']            = $name." <".$email.">";
    $headers['Subject']       = $subject;
    sendMail($email, $subject, $content, $headers); //ta funkcja zwraca juz wyniki!
    logs($email, $action, "success");
  }
  else if($action=="resetPassword") {
    $email=$_REQUEST['email'];
    if(!bIsEmailRegistered($email)) {
      echo 'not-extists';
      logs($email, $action, "not_exists");
      exit();
    }
    $reset_password_id = $_REQUEST['hash'];
    $unencrypted=$_REQUEST['new1'];
    $new_password=sha1(md5($unencrypted));
    $query="SELECT * FROM users WHERE email = '$email' and reset_password_id = '$reset_password_id'";
    $result=mysqli_fetch_assoc(mysqli_query($_DB, $query));
    $login = $result['name'];
    $fname = $result['first_name'].' '.$result['last_name'];
    if(mysqli_num_rows(mysqli_query($_DB, $query))!=1){
      echo 'error';
      logs($email, $action, "mysql_error", array("details"=> mysqli_error($_DB)));
    }
    else{
      $query="UPDATE users SET `password` = '$new_password' WHERE `email`='$email' and reset_password_id = '$reset_password_id'";
      mysqli_query($_DB, $query) or die(mysqli_error($_DB));
      $query="UPDATE users SET `reset_password_id` = NULL WHERE `email`='$email'";
      mysqli_query($_DB, $query) or die(mysqli_error($_DB));
      $query="ALTER USER $login IDENTIFIED BY '$unencrypted'";
      echo exec('echo -e "'.$unencrypted.'\n'.$unencrypted.'" | sudo passwd '.strtolower($login));
      mysqli_query($_DB, $query);
      $subject    = "SBD: Zmieniono hasło";
      
      // Upośledzona ta wiadomość
      // Jak Ci się chce to napisz lepiej bo ja nie umiem xd
      $content = sGenerateMailContent("Nowe hasło zapisane", $result['first_name'], "
        Dane logowania do Twojego konta zostały zmienione!<br>
        Jeżeli nie podejmowano żadnych działań dotyczących zmiany hasła, może być to niechciana zmiana!<br>
        W tym przypadku niezwłocznie zmień swoje hasło!<br>
        Aby to zrobić, przejdź pod <a href=\"https://sbd.zst.pila.pl/panel/password/\">ten link</a>.
      ");
      $name = $fname;

      $headers['To']            = $name." <".$email.">";
      $headers['Subject']       = $subject;
      $id=$result['id'];
      $query="UPDATE users SET stLogin=0 WHERE id=$id";
      if(mysqli_query($_DB, $query)){
        sendMail($email, $subject, $content, $headers);
        logs($email, $action, "success");
      }
      else{
        echo 'error';
        logs($email, $action, "mysql_error", array("details"=>mysqli_error($_DB)));
      }
    }
  }
  else if($action=="resendCode") {
    $email=$_REQUEST['email'];
    if(!bIsEmailRegistered($email)) {
      echo 'not-extists';
      logs($email, $action, "not_exists");
      exit();
    }
    // sprawdzamy czy nie jest już aktywowane

    $query="SELECT * FROM users WHERE email='$email'";
    if(mysqli_num_rows(mysqli_query($_DB, $query))==1){
      $query="SELECT * FROM users WHERE email='$email'";
      $result2=mysqli_fetch_assoc(mysqli_query($_DB, $query));
      if($result2['verified']==0) {
        $code = $result2['activation_code'];
        $fname = $result2['first_name'];
        $lname = $result2['last_name'];

        $fake_secure_code = sha1(MD5($email.$code.date("U")));
        $fake_secure_code2 = sha1(MD5($email.date("U")));
        
        $activate_url = "https://sbd.zst.pila.pl/panel/activate/?em=$email&code=$fake_secure_code&compilation=$code&password=$fake_secure_code2";
        
        $content = sGenerateMailContent("Ponowna aktywacja konta", $fname, "
        Otrzymaliśmy Twoje zgłoszenie dotyczące weryfikacji adresu email twojego konta w systemie.<br><br>
        Aby aktywować konto, przejdź pod <a href=\"$activate_url\">ten adres</a><br><br>
        Jeżeli powyższy link nie działa, spróbuj skopiować tekst poniżej, i wkleić go w pasek adresu:<br>
        <a href=\"$activate_url\">$activate_url</a>
        ");
        $headers['To']            = $fname.' '.$lname." <".$email.">";
        $headers['Subject']       = "SBD: Aktywacja konta";
        sendMail($email, "SBD: Aktywacja konta", $content, $headers);
        logs($email, $action, "success");
      }
      else{
        echo 'activated';
        logs($email, $action, "already_actvated");
      }
    }
    else{
      echo 'no-user';
      logs($email, $action, "not_exists");
    }
  }
  
  if(!bIsLoggedIn()) {
    // dla niezalogowanych
    if($action=="ValidateLoginDetails") {
      $login = $_REQUEST['login'];
      $password = $_REQUEST['password'];
      $password = sha1($password);

      $search = mysqli_query($_DB, "SELECT * FROM users WHERE (`name`='$login' AND `password`='$password') OR (`email`='$login' AND `password`='$password')");
      // echo mysqli_num_rows($search);
      if(mysqli_num_rows($search)==0) {
        echo 'error';
        logs(getUserIpAddr(), $action, "error");
        exit();
      } 
      elseif(mysqli_num_rows($search)==1) {
        // sprawdzanie
        $f = mysqli_fetch_assoc($search);
        if($f['blocked']==1) { echo 'blocked'; logs($f['id'], $action, "blocked"); exit();}
        if($f['verified']==0) { echo 'email-not-verified'; logs($f['id'], $action, "email_not_verified"); exit(); }
        if($f['verified']==1) { echo 'not-verified'; logs($f['id'], $action, "not_activated"); exit(); }

        $_SESSION['zalogowany'] = true;
        $_SESSION['uuid'] = $f['id'];
        $_SESSION['hash'] = $f['password'];

        logs($f['id'], $action, "success");
        echo 'success';
        exit();
      }
      else {
        // więcej niż jedno konto to... lama
        echo 'multi-user';
        logs($email, $action, "more_than_one_account");
        exit();
      }
    } 
    else if($action=="CreateAccount"){
      if($_SETTINGS['open_register_users']) {
        $login=$_REQUEST['login'];
        $email=$_REQUEST['email'];
        $restricted_names = [
          'panel', 'root', 'sudo', 'zst', 'phpmyadmin', 'daemon', 'bin', 'sys', 'sync', 'games', 'eml', 'man', 'lp', 'mail', 'news', 'uucp', 'lightdm', 'cups-pk-helper', 'whoopsie', 'kernoops', 'caned', 'pulse', 'avahi', 'colord',
          'proxy', 'www-data', 'backup', 'list', 'irc', 'gnats', 'nobody', 'messagebus', '_apt', 'uuidd', 'avahi-autoipd', 'usbmux', 'dnsmasq', 'rtkit', 'litedm', 'speech-dispatcher', 'hplip', 'ftp', 'sshd', 'mysql', 'esportmechanlan', 'szkolna'
        ];
        if(strpos(strtolower($login), 'admin') !== false || strpos(strtolower($login), 'sys') !== false || in_array(strtolower($login), $restricted_names)) {
          echo 'reserved';
          logs($email, $action, "reserved_name", array("name" => $login));
          exit();
        }
        $class=$_REQUEST['class'];
        $class_group=$_REQUEST['class_group'];
        $fname=$_REQUEST['firstname'];
        $lname=$_REQUEST['lastname'];
        // $password=$_REQUEST['password'];
        // $password_hash = sha1(MD5($password));
        $year=$_REQUEST['year'];
        
        $query="SELECT * FROM users WHERE `email` LIKE '%$email%' OR `name` LIKE '%$login%'";
        // $fetch = mysqli_fetch_assoc(mysqli_query($_DB, $query));

        if(mysqli_num_rows(mysqli_query($_DB, $query))) {
          echo "user-exists";
          logs($email, $action, "user_exists");
        }
        else {
          $code = random_int(100000, 999999);
          $query="INSERT INTO users (`name`, `first_name`, `last_name`, `email`, `year`, `class`, `class_group`, `activation_code`) VALUES ('$login', '$fname', '$lname', '$email', '$year', '$class', '$class_group', '$code')";
          mysqli_query($_DB, $query) or die("error");

          $fake_secure_code = sha1(MD5($email.$code.date("U")));
          $fake_secure_code2 = sha1(MD5($email.date("U")));
          
          $activate_url = "https://sbd.zst.pila.pl/panel/activate/?em=$email&code=$fake_secure_code&compilation=$code&password=$fake_secure_code2";
          
          $content = sGenerateMailContent("Aktywacja konta", $fname, "
            Otrzymaliśmy Twoje zgłoszenie dotyczące rejestracji nowego konta w systemie.<br>
            Zanim zapytanie zostanie przekierowane do administratora systemu, musisz potwierdzić założenie konta.<br><br>
            Aby to zrobić, wystarczy przejść pod <a href=\"$activate_url\">ten adres</a><br><br>
            Jeżeli powyższy link nie działa, spróbuj skopiować tekst poniżej, i wkleić go w pasek adresu:<br>
            <a href=\"$activate_url\">$activate_url</a>
          ");
          
          
          $headers['To']            = $fname.' '.$lname." <".$email.">";
          $headers['Subject']       = "SBD: Aktywacja konta";
          sendMail($email, "SBD: Aktywacja konta", $content, $headers);
          // echo 'success';
          // $_COOKIE['logout_type']="activate_email_first";
          logs($email, $action, "success");

        }
      }
      else{
        echo "closed_register";
        logs(getUserIpAddr(), $action, "open_register_user");
      }
    }
  }
  else {
    if(bIsAdmin()) {
      if($action=="AcceptAccount") {
        error_reporting(E_ALL);
        $id=$_REQUEST['id'];
        $result = mysqli_query($_DB, "UPDATE users SET `verified` = '2' WHERE `id`='$id'");
        if($result) {
          // Pobieranie informacji o użytkowniku
          $query="SELECT `name`, CONCAT(`first_name`, ' ', `last_name`) AS `fname`, email, first_name FROM users WHERE id=$id";
          $user=mysqli_fetch_assoc(mysqli_query($_DB, $query)) or die(mysqli_error($_DB));
          $email = $user['email'];
          $fname = $user['fname'];
          $first_name=$user['first_name'];
          $password=$one_time_pass=generateRandomPassword();
          $user=$user['name'];
          
          
          // Tworzenie katalogu
          echo exec("mkdir /var/www/html/".strtolower($user));

          // Tworzenie konta linux i ftp
          exec('echo "'.$password.'\n'.$password.'\n\n\n\n\n\nt\n" |sudo adduser '.strtolower($user));
          echo exec('echo -e "'.$password.'\n'.$password.'" | sudo passwd '.strtolower($user));

          // Towrzenie domyślnej strony użytkownika
          $defaultpagecontent = sGenerateDefaultPage($fname, $first_name, $user);
          // echo exec("touch /var/www/html/".strtolower($user)."/index.html");
          file_put_contents("/var/www/html/".strtolower($user)."/index.html", $defaultpagecontent);

          // Nadanie praw właściciela do katalogu
          echo exec("sudo chown ".strtolower($user)." /var/www/html/".strtolower($user), $test);

          // Stworzenie użytkownika w bazie
          $query="CREATE USER '$user'@'%' IDENTIFIED BY '$password'";
          mysqli_query($_DB, $query);

          // Stworzenie domyślnej bazy użytkownika 
          $query="CREATE DATABASE IF NOT EXISTS $user";
          mysqli_query($_DB, $query);

          // Nadanie uprawnień
          $query = "GRANT ALL PRIVILEGES ON $user.* TO '$user'@'%' WITH GRANT OPTION";
          mysqli_query($_DB, $query);

          //ustawienie hasla do panelu
          $panel_password=sha1(MD5($one_time_pass));
          $query="UPDATE `users` SET `password` = '$panel_password' WHERE `id`='$id'";
          mysqli_query($_DB, $query);
          
          $subject    = "SBD: Konto aktywowane";
          $content    = sGenerateMailContent("Konto zatwierdzone", sGetFirstName($id), "
            Twoje konto zostało zatwierdzone przez administratora systemu.<br>
            Aby zacząć korzystać z bazy danych i serwera FTP, zaloguj się <a href=\"https://sbd.zst.pila.pl/panel/login/\">do systemu bazy danych</a>, a następnie przejdź do ustawień aby pobrać swoje dane logowania.<br>
            Twoje jednorazowe hasło to: <b>$one_time_pass</b><br>
            ");
            // INFORMACJA AWARYJNA - po testach zdecydowac czy dodac do tresci maila czy nie
            
            // UWAGA! <br>
            // Czasami przy kopiowaniu zaznaczony zostaje dodatkowo znak spacji.<br>
            // Jeżeli masz problem z logowaniem, prawdopodobnie wklejone hasło zawiera spację.    
          $name = $fname;
          $headers['To']            = $name." <".$email.">";
          $headers['Subject']       = $subject;
          sendMail($email, $subject, $content, $headers);
          // exec("sudo service vsftpd restart");
          logs($_SESSION['uuid'], $action, "success", array("id" => $id));
        }
        else{
          echo "error";
          logs($_SESSION['uuid'], $action, "mysql_error", array("details" => mysqli_error($_DB), "id" => $id));
        }
      }
      elseif($action=="UpdateAccountData") {
        $id=$_REQUEST['uuid'];
        $data=$_REQUEST['data'];
        $priv = $data['privileges'];
        $blocked = $data['blocked'];
        $fn = $data['first_name'];
        $ln = $data['last_name'];
        $email = $data['email'];
        $year = $data['year'];
        $classname = $data['class'];
        $classgroup = $data['class_group'];
        $query="SELECT `name`, privileges, blocked FROM users WHERE id=$id";
        $name=mysqli_fetch_assoc(mysqli_query($_DB, $query));
        $privileges=$name['privileges'];
        $block=$name['blocked'];
        $name=$name['name'];
        if($block!=$blocked){
          if($blocked==1){
            $priv=0;
            $query = "REVOKE ALL PRIVILEGES ON *.* FROM '$name'@'%'";
            mysqli_query($_DB, $query);
            $query = "ALTER USER '$name'@'%' ACCOUNT LOCK"; 
            mysqli_query($_DB, $query);
            exec("echo \"".strtolower($name)."\" | sudo tee -a /etc/vsftpd.userlist");
            exec("sudo usermod -L ".strtolower($name));
          }else{
            $query = "GRANT ALL PRIVILEGES ON $name.* TO '$name'@'%' WITH GRANT OPTION";
            mysqli_query($_DB, $query);
            $query = "ALTER USER '$name'@'%' ACCOUNT UNLOCK"; 
            mysqli_query($_DB, $query);
            exec("sed '/".strtolower($name)."/d' /etc/vsftpd.userlist | sudo tee /etc/vsftpd.userlist");
            exec("sudo usermod -U ".strtolower($name));
          }
          exec("sudo service vsftpd restart");
        }
        if($privileges!=$priv){
          if($priv == 1){
            $query = "GRANT ALL PRIVILEGES ON *.* TO '$name'@'%' WITH GRANT OPTION";
            mysqli_query($_DB, $query);
            exec("sudo usermod -a -G sudo ".strtolower($name));
            exec("echo \"".strtolower($name)."\" | sudo tee -a /etc/vsftpd.chroot_list");
          }else{
            exec("sed '/".strtolower($name)."/d' /etc/vsftpd.chroot_list | sudo tee /etc/vsftpd.chroot_list");
            exec("sudo deluser ".strtolower($name)." sudo");
            $query = "REVOKE ALL PRIVILEGES ON *.* FROM '$name'@'%'";
            mysqli_query($_DB, $query);
            $query = "GRANT ALL PRIVILEGES ON $name.* TO '$name'@'%' WITH GRANT OPTION";
            mysqli_query($_DB, $query);
          }
          exec("sudo service vsftpd restart");
        }
        mysqli_query($_DB, "UPDATE users SET `first_name`='$fn', `last_name`='$ln', `email`='$email', `privileges` = '$priv',  `blocked` = '$blocked',`year` = '$year', `class`='$classname', `class_group`=$classgroup WHERE `id`='$id'") or die('error');
        echo 'success';
        logs($_SESSION['uuid'], $action, "success", array("id" => $id));
      }
      elseif($action=="RemoveAccount") {
        $id=$_REQUEST['id'];
        $name = mysqli_fetch_assoc(mysqli_query($_DB, "SELECT `name` FROM users WHERE id='$id'"));
        $name=$name['name'];
        mysqli_query($_DB, "DELETE FROM users WHERE `id`='$id'");
        mysqli_query($_DB, "DROP USER '$name'@'%'");
        exec("sudo rm /var/www/html/".$name.' -r');
        exec("sudo deluser ".$name);
        $query = "DROP DATABASE $name";
        mysqli_query($_DB, $query);
        echo 'success';
        logs($_SESSION['uuid'], $action, "success", array("uuid" => $id, "name" => $name));
      }
      elseif($action=="GetAccountData") {
        $uuid = $_REQUEST['uuid'];
        $data = array();
        $r = mysqli_fetch_assoc(mysqli_query($_DB, "SELECT * FROM users WHERE `id`=$uuid"));
        $data = array(
          "login" => $r['name'],
          "firstname" => $r['first_name'],
          "lastname" => $r['last_name'],
          "email" => $r['email'],
          "privileges" => $r['privileges'],
          "verified" => $r['verified'],
          "blocked" => $r['blocked'],
          "year" => $r['year'],
          "class" => array(
            "name" => $r['class'],
            "group" => $r['class_group']
          )
        );

        echo json_encode($data);
      }
      elseif($action=='FindUsersByFilters') {
        $query = "WHERE `verified`>0";
        $filters = @$_REQUEST['filters'];
        $response = array();

        if(isset($filters['verified']) && $filters['verified']!="") {
          $query .= " AND `verified`=".$filters['verified'];
        }
        if(isset($filters['class']) && $filters['class']!="") {
          $query .= " AND `class`='{$filters['class']}'";
        }
        if(isset($filters['group']) && $filters['group']!="") {
          $query .= " AND `class_group`='{$filters['group']}'";
        }
        if(isset($filters['search']) && $filters['search']!="") {
          $query .= " AND (`name` LIKE '%{$filters['search']}%' OR `first_name` LIKE '%{$filters['search']}%' OR `last_name` LIKE '%{$filters['search']}%' OR `email` LIKE '%{$filters['search']}%' OR CONCAT(`first_name`, ' ', `last_name`) LIKE '%{$filters['search']}%' OR CONCAT(`last_name`, ' ', `first_name`) LIKE '%{$filters['search']}%')";
        }

        $newquery = "SELECT * FROM users $query ORDER BY `last_name` ASC";

        $q = mysqli_query($_DB, $newquery);
        while($r = mysqli_fetch_assoc($q)) {
          $response[] = array(
            "id" => $r['id'],
            "user_name" => $r['name'],
            "first_name" => $r['first_name'],
            "last_name" => $r['last_name'],
            "email" => $r['email'],
            "verified" => $r['verified'],
            "year" => $r['year'],
            "blocked" => $r['blocked'],
            "class" => array(
              "name" => $r['class'],
              "group" => $r['class_group']
            )
          );
        }

        echo json_encode($response);
      }
      else if($action=="sendCustomEmail") {
        $title = $_REQUEST['title'];
        $content = nl2br($_REQUEST['content']);
        $email = $_REQUEST['email'];
        $fname = $_REQUEST['fname'];
        $lname = $_REQUEST['lname'];
        $content = sGenerateMailContent($title, $fname, $content);
        $headers['To']            = "$fname $lname <$email>";
        $headers['Subject']       = "SBD: $title";
        sendMail($email, $title, $content, $headers);
        logs($_SESSION['uuid'], $action, "success", array("title" => $title, "email" => $email));
      }
      else if($action=="DeleteReport") {
        $id=$_REQUEST['id'];
        $query="DELETE FROM `bugs` WHERE `bugs`.`id` = $id";
        if(mysqli_query($_DB, $query)){
          logs($_SESSION['uuid'], $action, "success", array("id" => $id));
          echo "success";
        }
        else {
          logs($_SESSION['uuid'], $action, "mysql_error", array("id" => $id, "details" => mysqli_error($_DB)));
          echo "error";
        }
      }
      else if($action=="AcceptReport") {
        $id=$_REQUEST['id'];
        $query="UPDATE `bugs`SET `status`=1 WHERE `bugs`.`id` = $id";
        if(mysqli_query($_DB, $query)) {
          $query="SELECT * FROM users WHERE `developer`=1";
          $result=mysqli_query($_DB, $query);

          $recipients = "";
          $emails="";
          while($r=mysqli_fetch_assoc($result)) {
            $recipients .= "{$r['first_name']} {$r['last_name']} <{$r['email']}>, ";
            $emails .=$r['email'].", ";
          }
          
          $recipients = substr($recipients, 0, -2);

          $content=sGenerateMailContent("Pojawiło się nowe zgłoszenie", "Programisto", "Jeden z administratorów potwierdził właśnie istnienie jakiegoś błędu.<br>".
          "Sprawdź proszę stronę <a href='https://sbd.zst.pila.pl/panel'>Szkolnej Bazy Danych</a> i sprawdź czego dotyczy zgłoszenie.");
          
          $headers['To'] = $recipients;
          $headers['Subject']       = "SBD: Pojawiło się nowe zgłoszenie";
          
          logs($_SESSION['uuid'], $action, "success", array("id" => $id));
          sendMail($emails, "SBD: Pojawiło się nowe zgłoszenie", $content, $headers, false);
          echo "success";
          
        }
        else {
          logs($_SESSION['uuid'], $action, "mysql_error", array("id" => $id, "reason" => mysqli_error($_DB)));
          echo "error";
        }
      }
      else if($action=="ChangeServerSettings") {
        $setting_name = $_REQUEST['setting'];
        $setting_type = $_REQUEST['type'];
        $setting_value = $_REQUEST['value'];

        mysqli_query($_DB, "UPDATE settings SET `value`='$setting_value' WHERE `name`='$setting_name'");

        if(mysqli_affected_rows($_DB)) {
          echo 'success';
          logs($_SESSION['uuid'], $action, "success", array('name'=>$setting_name, "value"=>$setting_value));
        }
        else {
          echo 'nothing_changed';
          logs($_SESSION['uuid'], $action, "nothing_changed", array('name'=>$setting_name, "value"=>$setting_value));
        }

        // $user_db=$_REQUEST['user_db'];
        // $admin_db=$_REQUEST['admin_db'];
        // $open_reg=$_REQUEST['open_reg'];
        // $autoremove=$_REQUEST['autoremove'];
        // $autoremovedate=$_REQUEST['autoremovedate'];
        // $localok=0;
        // if($user_db=='' || !isset($user_db)){}
        // else {
        //   $query = "UPDATE settings SET `value` = '$user_db' WHERE `name`='user_max_db'";
        //   if(mysqli_query($_DB, $query)){
        //     $localok+=1;
        //   };
        // }
        // if($admin_db=='' || !isset($admin_db)){}
        // else {
        //   $query = "UPDATE settings SET `value` = '$admin_db' WHERE `name`='admin_max_db'";
        //   if(mysqli_query($_DB, $query)){
        //     $localok+=1;
        //   };
        // }
        // if($open_reg=='' || !isset($open_reg)){}
        // else {
        //   $query = "UPDATE `settings` SET `value` = '$open_reg' WHERE `settings`.`name` = 'open_register_users';";
        //   if(mysqli_query($_DB, $query)){
        //     $localok+=1;
        //   };
        // }
        // if($autoremove=='' || !isset($autoremove)){}
        // else {
        //   if($autoremove!=$_SETTINGS['auto_remove_data'] && $autoremove==true){
        //     $id=$_SESSION['uuid'];
        //     $query = "UPDATE `settings` SET `value` = '$id' WHERE `settings`.`name` = 'user_id_enabled_auto_remove_data';";
        //     mysqli_query($_DB, $query);
        //     logs($_SESSION['uuid'], $action, "enabled", array('details'=>$id));
        //   }
        //   $query = "UPDATE `settings` SET `value` = '$autoremove' WHERE `settings`.`name` = 'auto_remove_data';";
        //   if(mysqli_query($_DB, $query)){
        //     $localok+=1;
        //   };
        // }
        // if($autoremovedate=='' || !isset($autoremovedate)){}
        // else {
        //   $query = "UPDATE `settings` SET `value` = '$autoremovedate' WHERE `settings`.`name` = 'date_of_auto_remove_data';";
        //   if(mysqli_query($_DB, $query)){
        //     $localok+=1;
        //   };
        // }
        // if($localok>0){
        //   echo "success";
        //   logs($_SESSION['uuid'], $action, "success");
        // }
        // else{
        //   echo "nothing-changed";
        //   logs($_SESSION['uuid'], $action, "no_changes");
        // }
      }
      elseif($action=="GetAllClasses") {
        $data = array();
        $q = mysqli_query($_DB, "SELECT * FROM classes");
        while($r = mysqli_fetch_assoc($q)) {
          $data[$r['id']] = array(
            "name" => $r['name'],
            "number" => $r['klasa'],
            "letter" => $r['oddzial']
          );
        }

        echo json_encode($data);
      }
      elseif($action=='GetUserMainDirectory') {
        $username = strtolower($_REQUEST['username']);
        $files = array(
          "files" => array(),
          "indexes" => array()
        );

        // echo "Trying to get ".$_SERVER['DOCUMENT_ROOT'].'/'.$username."<br>";

        $tempfiles = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].$username), array('..', '.'));

        foreach ($tempfiles as $key => $value) {
          $fullpath = $_SERVER['DOCUMENT_ROOT'].$username.'/'.$value;
          $files['files'][] = array(
            "name" => $value,
            "bytes" => sFormatSizeUnits(filesize($fullpath)),
            "type" => (is_dir($fullpath) ? 'directory' : 'file')
          );
        }

        // $ite = new RecursiveDirectoryIterator($_SERVER['DOCUMENT_ROOT'].$username);
        // foreach (new RecursiveIteratorIterator($ite) as $filename => $cur) {
        //   if(substr($filename, -1, 1) == ".") continue;
          
        //   $filename_exploded = explode("/", $filename);
        //   $onlyfilename = $filename_exploded[sizeof($filename_exploded)-1];

        //   $files['files'][] = array(
        //     "name" => $onlyfilename,
        //     "bytes" => $cur->getSize()
        //   );
        // }

        echo json_encode($files);
      }
      elseif($action=='WipeAllSystemData') {
        // exit("BETA TESTY");

        $documentroot = $_SERVER['DOCUMENT_ROOT'];
        // 1 Backup bazy danych // 
        exec("mysqldump panel --add-drop-table --user phpmyadmin --password=mdf4pdty9wq1 --single-transaction > $documentroot/panel/logs/database_backup.sql");

        // 2 Zanim przejdziemy dalej, sprawdzić, czy istnieje plik backup'a bazy danych!!! Inaczej przerywamy działania
        if(!file_exists("$documentroot/panel/logs/database_backup.sql")) exit("Plik bazy danych nie istnieje! Przerywamy działanie...");

        // 3 Informacja do dziennika o wyczyszczeniu systemu
        logs($_SESSION['uuid'], $action, 'success');
        
        // 4 Backup dziennika systemu (opisane niżej)
        $backup_date = date("d_m_Y_U");
        exec("sudo tar -czvf /var/www/backup/wipedata_$backup_date.tar.gz $documentroot/panel/logs");

        // 5 Sprawdzamy, czy plik .tar.gz istnieje, inaczej przerywamy działanie
        if(!file_exists("/var/www/backup/wipedata_$backup_date.tar.gz")) exit("Plik backupu dziennika nie istnieje! Przerywamy działanie...");

        $users = mysqli_query($_DB, "SELECT * FROM users WHERE `privileges`=0 AND `developer`=0");
        while($r = mysqli_fetch_assoc($users)) {
          $user = $r['name'];

          // 6 Usuwanie katalogów 
          exec("sudo rm -R $documentroot/$user");

          // 7 Usuwanie kont FTP 
          exec("sudo deluser $user");

          // 8 Usuwanie kont MySQL ("")
          
          // 9 Usuwanie baz danych użytkowników
          $db = mysqli_query($_DB, "SHOW DATABASES LIKE '$user%'");
          $r = mysqli_fetch_all($db);
          foreach($r as $key => $value) { 
            $dbname = $value[0];
            mysqli_query($_DB, "DROP DATABASE '$dbname'");
          }

          // 10 Usuwanie użytkowników z tabeli 
          mysqli_query($_DB, "DELETE FROM users WHERE `name`='$user'");
        }

        // 11 Usuwamy zawartość folderu $documentroot/panel/logs/ (wykluczając .htaccess i logs_here);
        $files_to_remove = scandir("$documentroot/panel/logs/");
        $files_to_remove = array_diff($files_to_remove, array("..", ".", ".htaccess", "logs_here"));
        foreach ($files_to_remove as $key => $value) {
          exec("sudo rm $documentroot/panel/logs/$value");
        }
        echo "success";
      }
    }
    else {
      // dla użytkowników

      // a co tu niby chcesz dać?
    }
    //dla kazdego zalogowanego
    if($action=="changePassword") {
      $id=$_SESSION['uuid'];
      $current=sha1($_REQUEST['current']);
      $new=$_REQUEST['new'];
      $encrypted=sha1(MD5($new));
      $query="SELECT * FROM users WHERE `id`=$id and `password`='$current'";
      $result=mysqli_fetch_assoc(mysqli_query($_DB, $query));
      if(mysqli_num_rows(mysqli_query($_DB, $query))==1) {
        $query="UPDATE users SET `password` = '$encrypted' WHERE `id`=$id";
        mysqli_query($_DB, $query);
        $login=$result['name'];
        $query="ALTER USER $login IDENTIFIED BY '$new'";
        mysqli_query($_DB, $query);
        echo exec('echo -e "'.$new.'\n'.$new.'" | sudo passwd '.strtolower($login));
        $fname = $result['first_name'].' '.$result['last_name'];
        $email = $result['email'];
        $subject    = "SBD: Zmieniono hasło";
        // $c="Twoje dane logowania zostały zmienione.<br>Jeżeli to Ty, nie musisz nic robić. Jeżeli to nie Ty, najlepiej zmień hasło! W tym celu kliknij <a href='https://sbd.zst.pila.pl/panel/password/'>ten link</a>.";
        $content    = sGenerateMailContent("Zmiana hasła", sGetFirstName($id), "
        Hasło do Twojego konta na serwerze szkolnej bazy danych zostało zmienione.<br>
        Jeżeli to działanie nie zostało podjęce przez Ciebie, niezwłocznie zmień hasło.<br>
        <br>
        Aby zmienić hasło, przejdź pod <a href='https://sbd.zst.pila.pl/panel/password/'>ten link</a>.
        ");
        $name = $fname;
        
        
        $headers['To']            = $name." <".$email.">";
        $headers['Subject']       = $subject;
        $_SESSION['hash']=$encrypted;
        $query="SELECT * FROM users WHERE `id`=$id";
        $res=mysqli_query($_DB, $query);
        if(mysqli_num_rows($res)){
          $query="UPDATE users SET `stLogin`=0 WHERE `id`=$id";
          if(mysqli_query($_DB, $query)){
            sendMail($email, $subject, $content, $headers);
            logs($_SESSION['uuid'], $action, "success");
          }
          else{
            echo 'error';
            logs($_SESSION['uuid'], $action, "mysql_error", array("details" => mysqli_error($_DB)));
          }
        }
      }
      else {
        echo 'error';
        logs($_SESSION['uuid'], $action, "error");
      }
    }
    else if($action=='newDatabase') {
      $dbname=$_REQUEST['dbname'];
      $id=$_SESSION['uuid'];
      $name=sGetName($id);
      $db = mysqli_query($_DB, "SHOW DATABASES LIKE '$name%'");
      $i = mysqli_num_rows($db);
      if($i<(bIsAdmin() ? $_SETTINGS['admin_max_db'] : $_SETTINGS['user_max_db'])){
        $query="CREATE DATABASE ".$name."_".$dbname;
        if(mysqli_query($_DB, $query)) {
          $query = "GRANT ALL PRIVILEGES ON ".$name."_".$dbname.".* TO '$name'@'%' WITH GRANT OPTION";
          if(mysqli_query($_DB, $query)) {
            echo "success";
            logs($_SESSION['uuid'], $action, "success", array("name" => $name."_".$dbname));
          }
          else{
            echo 'error';
            logs($_SESSION['uuid'], $action, "mysql_error", array("details" => mysqli_error($_DB), "name" => $name."_".$dbname));
          }
        }
        else {
          echo 'exist';
          logs($_SESSION['uuid'], $action, "database_exists", array("name" => $name."_".$dbname));
        }
      }
      else {
        echo "db_limit";
        logs($_SESSION['uuid'], $action, "reached_databases_limit", array("name" => $name."_".$dbname));
      }
    }
    else if($action=="ReportBug"){
      $type=$_REQUEST['type'];
      $title=$_REQUEST['title'];
      $content=$_REQUEST['content'];
      $id=$_SESSION['uuid'];
      $query="INSERT INTO `bugs` (`user_id`, `type`, `title`, `content`) VALUES ('$id', '$type', '$title', '$content')";
      if(mysqli_query($_DB, $query)){
        logs($_SESSION['uuid'], $action, "success", array("type" => $type));
        echo "success";
      }
      else {
        logs($_SESSION['uuid'], $action, "mysql_error", array("type" => $type, "details" => mysqli_error($_DB)));
        echo "error";
      }
    }
    else if($action=="RemoveDatabase"){
      $dbname = sGetName()."_".$_REQUEST['dbname'];
      $localok = mysqli_query($_DB, "DROP DATABASE $dbname");
      if(!$localok) {  
        echo mysqli_error($_DB);
        logs($_SESSION['uuid'], $action, "mysql_error", array("details" => mysqli_error($_DB), "name" => $dbname));
        exit();
      }
      logs($_SESSION['uuid'], $action, "success", array("name" => $dbname));
      echo "success";
    }
  }
?>
