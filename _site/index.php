<?php
include 'php/func.php';
include 'php/html_func.php';

safe_session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/main.css" />
    <title>PoliMapa PG<?php if ( isset( $_SESSION['user'] ) ) echo ' - ', $_SESSION['user']; ?></title>
  </head>
  <body>
    <div id="cover"></div>
    <div id="sidebar">
      <div id="logo">
        PoliMapa<br />
        <img class="button" src="img/logo.png" alt="Politechnika Gdanska" />
      </div>
      <?php
      toolbar_begin();
      nav_button( "sci" );
      //nav_button( "pub" );
      //nav_button( "atm" );
      if ( has_access_class( USER_AC::USER ) ) {
        nav_separator();
        nav_button( "add-feature" );
      }
      nav_separator();
      nav_button( "login", !isset( $_SESSION['user'] ) );
      nav_button( "new-user", !isset( $_SESSION['user'] ) );
      nav_button( "logout", isset( $_SESSION['user'] ) );
      toolbar_end();

      dialog_form_begin( 'new-user' );
      labelled_input( 'User:', 'new-user' );
      labelled_input( 'Pass:', 'new-pass', 'password' );
      dialog_form_end( 'new-user', 'Rejestruj' );

      dialog_form_begin( 'login' );
      labelled_input( 'User:', 'user', 'text', filter_input( INPUT_COOKIE, 'user' ) );
      labelled_input( 'Pass:', 'pass', 'password' );
      dialog_form_end( 'login', 'Login' );

      dialog_form_begin( 'add-feature' );
      labelled_input( 'Id:', 'feature-id' );
      labelled_input( 'Nazwa:', 'feature-name' );
      labelled_input( 'Opis:', 'feature-desc' );
      labelled_input( 'Centrum:', 'feature-center', 'text', '', FORM_INPUT::MULTI_LINE );
      labelled_input( 'Wierzcholek:', 'feature-coords', 'text', '', FORM_INPUT::MULTI_LINE );
      dialog_form_end( 'add-feature', has_access_class( USER_AC::MOD ) ? 'Dodaj' : 'Zaproponuj'  );
      ?>
      <div id="feature-caption">
      </div>
      <div id="selection-list">
      </div>
    </div>
    <div id="map-canvas">
    </div>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxTDDVTUbi5bwVFJnifWYH9N4fRtptbeY&amp;sensor=true"></script>
    <script type="text/javascript" src="script/ui.js"></script>
    <script type="text/javascript" src="script/map.js"></script>
  </body>
</html>
