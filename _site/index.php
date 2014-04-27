<?php
include 'php/func.php';
include 'php/html5_func.php';

safe_session_start();

html5_begin_body();
?>
    <div id="cover"></div>
    <div id="sidebar">
      <div id="sidebar-head">
        <div id="title">PoliMapa</div>
        <img id="logo" class="button" src="img/logo.png" alt="Politechnika Gdanska" />
        <img id="logo-small" class="button" src="img/logo-small.png" alt="PG" />
      </div>
      <?php
      toolbar_begin();
      nav_button( 'sci' );
      nav_button( 'atm' );
      nav_button( 'food' );
      nav_button( 'pub' );
      if ( has_access_class( USER_AC::USER ) ) {
        nav_separator();
        nav_button( 'add-feature' );
      }
      nav_separator();
      nav_button( 'login', !isset( $_SESSION['user'] ) );
      nav_button( 'new-user', !isset( $_SESSION['user'] ) );
      nav_button( 'logout', isset( $_SESSION['user'] ) );
      toolbar_end();

      dialog_form_begin( 'new-user' );
      echo 'Zarejestruj sie w PoliMapie<br />';
      labelled_input( 'User:', 'new-user' );
      labelled_input( 'Pass:', 'new-pass', 'password' );
      labelled_input( 'Mail:', 'new-email' );
      dialog_form_end( 'new-user', 'Rejestruj' );

      dialog_form_begin( 'login' );
      echo 'Logowanie do PoliMapy<br />';
      labelled_input( 'User:', 'user', 'text', true, filter_cookie( 'user' ) );
      labelled_input( 'Pass:', 'pass', 'password' );
      dialog_form_end( 'login', 'Loguj' );

      dialog_form_begin( 'add-feature' );
      echo '<div style="text-align: center">Dodawarka obiektow PoliMapy</div>'."\n";
      $radio_group = radio_group_begin( 'feature-type' );
      radio_input( 'sci', $radio_group );
      radio_input( 'atm', $radio_group );
      radio_input( 'food', $radio_group );
      radio_input( 'pub', $radio_group );
      radio_group_end();
      labelled_input( 'Id:', 'feature-id' );
      labelled_input( 'Nazwa:', 'feature-name' );
      labelled_input( 'Opis:', 'feature-desc' );
      echo '<div class="input-with-locate">'."\n";
      labelled_input( 'Centrum:', 'feature-center', 'text', true, '', FORM_INPUT::MULTI_LINE );
      loc_button( "feature-center" );
      echo '</div>'."\n"
          .'<div class="input-with-locate">'."\n";
      labelled_input( 'Wierzcholki:', 'feature-vertex-0', 'text', FALSE, '', FORM_INPUT::MULTI_LINE );
      loc_button( "feature-vertex-0" );
      echo '</div>'."\n";
      dialog_form_end( 'add-feature', has_access_class( USER_AC::MOD ) ? 'Dodaj' : 'Zaproponuj'  );
      ?>
      <div id="selection-list">
      </div>
    </div>
    <div id="map-wrapper">
      <div id="feature-caption"></div>
      <?php nav_button( "scroll-to-top" ); ?>
      <div id="map-canvas"></div>
    </div>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxTDDVTUbi5bwVFJnifWYH9N4fRtptbeY&amp;sensor=true"></script>
    <script type="text/javascript" src="script/ui.js"></script>
    <script type="text/javascript" src="script/map.js"></script>
<?php html5_end_body() ?>
