<?php

function send($data) {
  $token = file_get_contents('.token');
  $url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$token;  
  $content = json_encode($data);

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER,
          array("Content-type: application/json"));
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

  $json_response = curl_exec($curl);

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  if ( $status != 201 ) {
      echo ("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
  }
  curl_close($curl);

  return json_decode($json_response, true);
}

function post() {
  $json = file_get_contents('php://input');
  $post = json_decode($json);

  // 接收訊息
  if ($post->object === 'page') {
    foreach ($post->entry as $entry) {
      foreach ($entry->messaging as $messaging) {
        $id = $messaging->sender->id;
        $text = $messaging->message->text;
        $data = Array(
          'recipient' => Array('id' => $id),
          'message' => Array('text' => $text)
         );
        send($data);
      }
    }
  }
}

function get() {
  if (!empty($_GET)) {
    $get = new stdClass();
    foreach ($_GET as $key => $value) {
      $get->$key = $value;
    }

    // Webhook 驗證權杖
    if ($get->hub_mode === 'subscribe')
      $token = file_get_contents('.verify_token');
      if($get->hub_verify_token === $token) {
      echo $get->hub_challenge;
    }
  }
}

switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST': 
    post();
    break;
    
  case 'GET': 
    get();
    break;
    
  default:
    break;
}

?>