<?php

function is_request_GET() {
  return filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === 'GET';
}

function is_request_POST() {
  return filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === 'POST';
}

function filter_GET( $var ) {
  return filter_input( INPUT_GET, $var );
}

function filter_POST( $var ) {
  return filter_input( INPUT_POST, $var );
}

function filter_cookie( $var ) {
  return filter_input( INPUT_COOKIE, $var );
}

function json_decode_file( $filename ) {
  return json_decode( file_get_contents( $filename ), true );
}

function json_encode_file( $filename, $data ) {
  return file_put_contents( $filename, json_encode( $data, JSON_PRETTY_PRINT ) );
}

function json_encode_public_file( $filename, $data ) {
  $tmp_name = $filename.mt_rand().'tmp';
  json_encode_file( $tmp_name, $data );
  return system( 'mv '.$tmp_name.' '.$filename );
}

function json_public_set( $filename, $key, $value ) {
  $json_data = json_decode_file( $filename );
  $json_data[$key] = $value;
  return json_encode_public_file( $filename, $json_data );
}

function redirect( $target = 'index.php', $status = 303 ) {
  header( 'Location: '.$target, true, $status );
  exit();
}

function form_error( $form_name, $error_name ) {
  $_SESSION[$form_name.'-error'] = $error_name;  
  redirect();  
}

function safe_session_start() {
  $client_ip = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP );
  $json_ip_bans = json_decode_file( __DIR__.'/../data/shadow/ip-ban.json' );
  if ( isset( $json_ip_bans[$client_ip] ) )
    exit_msg( 'You have been banned, IP <b>'.$client_ip.'</b>, because of <b>'.$json_ip_bans[$client_ip].'</b>.<br /> Have a nice day!'."\n" );
  if ( session_id() == '' )
    return session_start();
}

function has_access_class( $ac ) {
  return isset( $_SESSION['user-settings']['AC'] ) && $_SESSION['user-settings']['AC'] >= $ac;
}

function exit_msg( $msg ) {
  echo $msg;
  exit();
}

function html5_begin_body( $title = 'PoliMapa PG' ) {
  if ( isset( $_SESSION['user'] ) )
    $title .= ' - '.$_SESSION['user'];
    
  echo '<!DOCTYPE html>'."\n"
      .'<html>'."\n"
      .'  <head>'."\n"
      .'  <meta charset="utf-8" />'."\n"
      .'  <link rel="stylesheet" href="css/main.css" />'."\n"
      .'  <link rel="icon" href="img/favicon.png" />'."\n"
      .'  <title>', $title, '</title></head>'."\n"
      .'<body>'."\n";
}

function html5_end_body() {
  echo '</body>'."\n"
      .'</html>'."\n";
} 

function html5_msg( $msg, $title = 'PoliMapa PG' ) {
  html5_begin_body( $title );    
  echo '<div class="info-txt">'.$msg.'</div>';
  html5_end_body();
  exit();
}

function assert_access_class( $ac ) {
  if ( !has_access_class( $ac ) )
    exit_msg( 'you wish.' );
}
