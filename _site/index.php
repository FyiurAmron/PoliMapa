<?php
include 'php/func.php';
include 'php/html5_func.php';

safe_session_start();

html5_begin_body();
?>
    <div id="cover"></div>
    <div id="sidebar">
      <div id="sidebar-head">
        <div id="title-icons">
          <?php
          nav_button( 'info' );
          nav_button( 'help' );
          ?>
        </div>
        <h1 id="title">PoliMapa</h1>
        <img id="logo" class="button" src="img/logo.png" alt="Politechnika Gdanska" />
        <img id="logo-small" class="button" src="img/logo-small.png" alt="PG" />
      </div>
      <?php
      toolbar_begin();
      nav_button( 'sci' );
      nav_button( 'atm' );
      nav_button( 'food' );
      nav_button( 'pub' );
      //nav_button_radio( 'layer', 'sci' );
      //nav_button_radio( 'layer', 'atm' );
      //nav_button_radio( 'layer', 'food' );
      //nav_button_radio( 'layer', 'pub' );
      nav_button( 'add-feature', has_access_class( USER_AC::USER ) );
      nav_separator();
      nav_button( 'login', !isset( $_SESSION['user'] ) );
      nav_button( 'new-user', !isset( $_SESSION['user'] ) );
      nav_button( 'logout', isset( $_SESSION['user'] ) );
      //nav_button( 'user-panel', !has_access_class( USER_AC::MOD ) );
      nav_button( 'admin-panel', has_access_class( USER_AC::MOD ) );
      nav_separator();
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
      if ( has_access_class( USER_AC::MOD ) )
        labelled_input( 'autoupdate', 'autoupdate', 'checkbox', false );
      radio_input( 'sci', $radio_group );
      radio_input( 'atm', $radio_group );
      radio_input( 'food', $radio_group );
      radio_input( 'pub', $radio_group );
      radio_group_end();
      labelled_input( 'Id:', 'feature-id' );
      labelled_input( 'Nazwa:', 'feature-name' );
      labelled_input( 'Opis:', 'feature-desc' );
      labelled_input( 'Obiekt dekoracyjny', 'is-decoration', 'checkbox', false );
      echo '<div class="input-with-locate">'."\n";
      labelled_input( 'Centrum:', 'feature-center', 'text', true, '', FORM_INPUT::MULTI_LINE );
      loc_button( "feature-center" );
      echo '</div>'."\n";
      labelled_input( 'Utworz jako linie', 'as-line', 'checkbox', false );
      echo '<div class="input-with-locate">'."\n";
      labelled_input( 'Wierzcholki:', 'feature-vertex-0', 'text', false, '', FORM_INPUT::MULTI_LINE );
      loc_button( "feature-vertex-0" );
      echo '</div>'."\n";
      dialog_form_end( 'add-feature', has_access_class( USER_AC::MOD ) ? 'Dodaj' : 'Zaproponuj'  );
      
      dialog_form_begin( 'admin-panel', 'action-result-window' );
      //echo 'Dataset Update<br />';
      labelled_input( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dataset update?', 'dataset-update', 'checkbox', false );
      $radio_group = radio_group_begin( 'dataset' );
      radio_input( 'sci', $radio_group );
      radio_input( 'atm', $radio_group );
      radio_input( 'food', $radio_group );
      radio_input( 'pub', $radio_group );
      radio_group_end();
      echo '<iframe class="result-window" name="action-result-window">brak obslugi iFrame</iframe>'."\n";
      dialog_form_end( 'admin-action', 'Admin Action!' );
      
      dialog_box_begin( 'info' );
      echo '<h2>O aplikacji...</h2>'."\n"
          .'przygotowane w pocie czola przez sklad w skladzie: <ul>'."\n"
          .'<li>kod - eM Wilk</li>'."\n"
          .'<li>grafika - eM Wilk</li>'."\n"
          .'<li>muzyka - eM Wilk</li>'."\n"
          .'<li>efekty dzwiekowe - eM Wilk</li>'."\n"
          .'<li>choreografia - eM Wilk</li>'."\n"
          .'<li>asystent pomocnika rezysera - eM Wilk</li>'."\n"
          .'</ul>'."\n"
          .'z pozdrowieniami dla: KaWa, eM Wilk, Totoro'."\n";
      dialog_box_end();
      
      dialog_box_begin( 'help' );
      echo '<h2>Pomoc Polimapy</h2>'."\n"
          .'Tutaj znajduja sie bardzo wazne dla uzytkownika informacje, dotyczace '
          .'dzialania strony, funkcjonalnosci oferowanych, potencjalnych problemow, '
          .'metodyki wykorzystywania...<br /><br />'."\n" 
          .'...<br /><br />'."\n"
          .'<i>no i kogo my chcemy oszukac?</i>'."\n"; 
      dialog_box_end();
      ?>
      <div id="selection-list">
      </div>
    </div>
    <div id="map-wrapper">
      <div id="feature-caption"></div>
      <?php nav_button( "scroll-to-top" ); ?>
      <div id="map-canvas"></div>
    </div>
    <script type="text/javascript" src="script/ui.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxTDDVTUbi5bwVFJnifWYH9N4fRtptbeY&amp;sensor=true"></script>
    <script type="text/javascript" src="script/map.js"></script>
<?php html5_end_body() ?>
