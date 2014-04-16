<?php
include 'php/func.php';

safe_session_start();

if ( filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === 'POST' ) {
  $user = filter_input( INPUT_POST, 'new-user' );
  $pass = filter_input( INPUT_POST, 'new-pass' );
  $json_shadow = json_decode_file( 'data/shadow/passwd.json' );
  if ( isset( $json_shadow[$user] ) ) {
    $_SESSION['login-error'] = 'uzytkownik istnieje';
  } else {
    $json_shadow[$user] = hash( 'sha256', $pass );
    json_encode_file( 'data/shadow/passwd2.json', $json_shadow );
    system( 'mv data/shadow/passwd2.json data/shadow/passwd.json' );
    unset( $_SESSION['login-error'] );
    $_SESSION['user'] = $user;
    setcookie( 'user', $user );
  }
}

redirect();
