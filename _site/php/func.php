<?php

function json_decode_file( $name ) {
  return json_decode( file_get_contents( $name ), TRUE );
}

function json_encode_file( $name, $data ) {
  return file_put_contents( $name, json_encode( $data, JSON_PRETTY_PRINT ) );
}

function redirect( $target = 'index.php', $status = 303 ) {
  header( 'Location: '.$target, true, $status );
  exit();
}

function safe_session_start() {
  $client_ip = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP );
  $json_ip_bans = json_decode_file( __DIR__.'/../data/shadow/ip-ban.json' );
  if ( isset( $json_ip_bans[$client_ip] ) ) {
    echo 'You have been banned, IP <b>', $client_ip, '</b>, because of <b>', $json_ip_bans[$client_ip], '</b>.<br /> Have a nice day!', "\n";
    return;
  }
  if ( session_id() == '' )
    return session_start();
}

function has_access_class( $ac ) {
  return isset( $_SESSION['user-settings']['AC'] ) && $_SESSION['user-settings']['AC'] >= $ac;
}

function assert_access_class( $ac ) {
  if ( !has_access_class( $ac ) ) {
    echo 'you wish.';
    exit();
  }
}
