<?php

include 'const.php';

function toolbar_begin() {
  echo '<div id="toolbar">'."\n";
}

function toolbar_end() {
  echo '</div>'."\n";
}

function nav_button( $name, $visible = true ) {
  echo '<img class="button" '.($visible ? '' : 'style="display: none;" ').'id="nav-'.$name
      .'" src="img/nav-'.$name.'.png" alt="'.$name.'" title="'.$name.'" />'."\n";
}

function loc_button( $name ) {
  echo '<img class="button loc-button" id="loc-'.$name
      .'" src="img/locate.png" alt="locate '.$name.'" title="locate '.$name.'" />'."\n";
}

function nav_separator() {
  echo '<div class="nav-separator"></div>'."\n";
}

function dialog_form_begin( $name ) {
  echo '<div id="'.$name.'-box" class="ui-dialog'.(isset( $_SESSION[$name.'-error'] ) ? ' ui-dialog-active' : '' ).'">'."\n"
      .'  <img src="img/close.png" alt="close" title="close" class="button dialog-close-button" />'."\n"
      .'  <form id="'.$name.'-form" action="'.$name.'.php" method="post">'."\n"
      .'    <fieldset id="'.$name.'-fieldset" class="ui-fieldset"'.(isset( $_SESSION[$name.'-error'] ) ? '' : ' disabled="disabled"').'>'."\n";
}

function dialog_form_end( $name, $submit_name ) {
  echo (isset( $_SESSION[$name.'-error'] ) ? '    <span class="error-msg">'.$_SESSION[$name.'-error'].'&nbsp;</span>'."\n" : '' )
       .'    <input type="submit" value="'.$submit_name.'" />'."\n"
       .'    </fieldset>'."\n"
       .'  </form>'."\n"
       .'</div>'."\n";
  unset( $_SESSION[$name.'-error'] );  
}

function labelled_input( $caption, $input_name, $type = 'text', $required = true, $value = '', $line_break = FORM_INPUT::SINGLE_LINE ) {
  echo '<div class="input-caption'.(( $line_break === FORM_INPUT::MULTI_LINE ) ? '' : '-inline')
    .'"><label for="input-'.$input_name.'">'.$caption.'</label></div><div class="input-field"><input type="'
    .$type.'" name="'.$input_name.'" id="input-'.$input_name.'"'.( $value ? ' value="'.$value.'"' : '' )
    .( $required ? ' required="required"' : '' ).' />'.'</div>'."\n";
}

function radio_input( $value, array &$group, $required = true, $caption = null ) {
  $id = $group['name'].$group['nr'];
  echo '<input type="radio" name="'.$group['name'].'" id="'.$id.'" value="'.$value.'"'.( $required ? ' required="required"' : '' ).' />'
    .'<label for="'.$id.'">'.( $caption ? $caption : $value ).'</label>'."\n";
  $group['nr']++;
}

function radio_group_begin( $group_name ) {
  echo '<div class="input-radio-group" id="input-radio-group-'.$group_name.'">'."\n";
  return [ 'name' => $group_name , 'nr' => 0 ];
}

function radio_group_end() {
  echo '</div>'."\n";
}
