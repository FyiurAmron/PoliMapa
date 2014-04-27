<?php
include 'php/const.php';
include 'php/func.php';

safe_session_start();

$json_shadow = json_decode_file( 'data/shadow/passwd.json' );
if ( is_request_POST() ) {
  $user = filter_POST( 'new-user' );
  $pass = filter_POST( 'new-pass' );
  if ( !ctype_alnum( $user ) )
    form_error( 'new-user', 'uzyj tylko liter i cyfr w nazwie' );  
  else if ( strlen( $user ) < 4 )
    form_error( 'new-user', 'nazwa za krotka (< 4 znaki)' );
  else if ( strlen( $pass ) < 8 )
    form_error( 'new-user', 'haslo za krotkie (< 8 znakow)' );
  else if ( isset( $json_shadow[$user] ) )
    form_error( 'new-user', 'uzytkownik istnieje' );
  else {
    $hash = hash( 'sha256', $pass );
    $json_shadow[$user] = $hash;
    json_encode_public_file( 'data/shadow/passwd.json', $json_shadow ); 
    unset( $_SESSION['login-error'] );
    $_SESSION['user'] = $user;
    setcookie( 'user', $user );
    $email = filter_POST( 'new-email' );
    json_public_set( 'data/shadow/users.json',
      $user, ['AC' => USER_AC::GUEST, 'email' => $email ] );
    mail( $email, 'aktywacja konta na PoliMapa',
      'Aby aktywowac konto uzytkownika "'.$user.'" na PoliMapa, kliknij link: '."\n\n"
      .filter_input( INPUT_SERVER, 'HTTP_HOST' ).filter_input( INPUT_SERVER, 'PHP_SELF' )
      .'?new-user='.$user.'&key='.strrev($hash)
      /*, 'From: "PoliMapa" <'..'>'*/ );
    html5_msg( 'wyslano link aktywacyjny na '.$email.' - <a href=".">Przejdz do strony glownej</a>'."\n" );
  }
} else if ( is_request_GET() ) {
  $user = filter_GET( 'new-user' );
  if ( isset( $json_shadow[$user] ) && strrev( filter_GET( 'key' ) ) === $json_shadow[$user] ) {
    $json_data = json_decode_file( 'data/shadow/users.json' );
    $ac = $json_data[$user]['AC'];
    if ( $ac > USER_AC::GUEST )
      exit_msg( '"'.$user.'" juz jest aktywny.' );
    $json_data[$user]['AC'] = USER_AC::USER;    
    json_encode_public_file( 'data/shadow/users.json', $json_data );
    html5_msg( 'Uzytkownik "'.$user.'" aktywny. <a href=".">Przejdz do strony glownej</a>'."\n" );
  } else
    exit_msg( 'Invalid key for user "'.$user.'".' );    
} else
  redirect();
