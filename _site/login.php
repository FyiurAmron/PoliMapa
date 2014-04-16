<?php
include 'php/func.php';

safe_session_start();

if ( filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === 'POST' ) {
  $user = filter_input( INPUT_POST, 'user' );
  setcookie( 'user', $user, time() + 30 * 24 * 60 * 60 );
  $pass = filter_input( INPUT_POST, 'pass' );
  $json_shadow = json_decode_file( 'data/shadow/passwd.json' );
  if ( isset( $json_shadow[$user] ) ) {
    if ( hash( 'sha256', $pass ) === $json_shadow[$user] ) {
      unset( $_SESSION['login-error'] );
      $_SESSION['user'] = $user;
      $json_user_settings = json_decode_file( 'data/shadow/users.json' );
      $_SESSION['user-settings'] = $json_user_settings[$user];
    } else
      $_SESSION['login-error'] = 'zle haslo';
  } else
    $_SESSION['login-error'] = 'brak uzytkownika';
}

redirect();
