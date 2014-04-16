<?php

include 'const.php';

function toolbar_begin() {
  echo '<div id="toolbar">', "\n";
}

function toolbar_end() {
  echo '</div>', "\n";
}

function nav_button( $name, $visible = TRUE ) {
  echo '<img class="button" style="display:', $visible ? 'inline-block' : 'none', '" id="nav-', $name, '" src="img/nav-', $name, '.png" alt="', $name, '" title="', $name, '" />', "\n";
}

function nav_separator() {
  echo '<div class="nav-separator"></div>', "\n";
}

function dialog_form_begin( $name ) {
  echo '<div ', isset( $_SESSION[$name.'-error'] ) ? 'style="opacity:1;"' : '', 'id="'.$name.'-box" class="ui-dialog">', "\n";
  echo '  <form id="'.$name.'-form" action="'.$name.'.php" method="post">', "\n";
  echo '    <fieldset id="'.$name.'-fieldset" class="ui-fieldset" disabled="disabled">', "\n";
}

function dialog_form_end( $name, $submit_name ) {
  if ( isset( $_SESSION[$name.'-error'] ) )
    echo '    <span class="error-msg">', $_SESSION[$name.'-error'], '&nbsp;</span>', "\n";
  echo '    <input type="submit" value="'.$submit_name.'" />', "\n";
  echo '    </fieldset>', "\n";
  echo '  </form>', "\n";
  echo '</div>', "\n";
}

function labelled_input( $caption, $input_name, $type = 'text', $value = '', $line_break = FORM_INPUT::SINGLE_LINE ) {
  echo '<label><div class="input-caption', ( $line_break === FORM_INPUT::MULTI_LINE ) ? '' : '-inline', '">', $caption,
  '</div><div class="input-field"><input type="', $type, '" name="', $input_name, '" value="', $value, '" required="required" />',
  '</div></label>', "\n";
}
