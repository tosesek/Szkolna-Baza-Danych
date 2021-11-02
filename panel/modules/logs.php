<?php
function logs($user, $action, $status, $cdata=NULL){
  $date = date('d-F-Y');
  $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/panel/logs/'.$date.'.txt', 'a') or die ($fp = fopen($_SERVER['DOCUMENT_ROOT'].'/panel/logs/'.$date.'.txt', 'w'));
  $time = date('G:i:s');
  if($user=="SERVER"){
    fwrite($fp, '{"time": "'.$time.'", "user": "'.$user.'", "ip": "127.0.0.1", "action": "'.$action.'", "status": "'.$status.'", "data": '.json_encode($cdata).'}');
  }
  else{
    fwrite($fp, '{"time": "'.$time.'", "user": "'.$user.'", "ip": "'.getUserIpAddr().'", "action": "'.$action.'", "status": "'.$status.'", "data": '.json_encode($cdata).'}');
  }
  fwrite($fp, "\r\n");
  fclose($fp);  
}

function getUserIpAddr(){
  if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      //ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      //ip pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }else{
      $ip = $_SERVER['REMOTE_ADDR'];
  }
  if($ip == '::1'){
    $ip='127.0.0.1';
  }
  return $ip;
}


/*
      ValidateLogin

      logs(1, 'ValidateLogin', 'error', array('reason'=>'not-exists'))
*/