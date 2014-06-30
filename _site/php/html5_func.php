<?php

include_once 'const.php';

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

/*
function nav_button_radio( $group, $name, $visible = true ) {
  echo '<label
      .'<input class="nav-radio" style="'.( $visible ? '' : 'display: none;' )
      .'" name="'.$group.'" id="nav-radio-'.$name.'" value="'.$name
      .'" type="radio"></input>'."\n";
}
*/

function loc_button( $name ) {
  echo '<img class="button loc-button" id="loc-'.$name
      .'" src="img/locate.png" alt="locate '.$name.'" title="locate '.$name.'" />'."\n";
}

function nav_separator() {
  echo '<div class="nav-separator"></div>'."\n";
}

function dialog_begin( $name, $style_class = '' ) {
  echo '<div id="'.$name.'-dialog" class="ui-dialog'.(isset( $_SESSION[$name.'-error'] ) ? ' ui-dialog-active' : '' ).'">'."\n"
      .'  <img src="img/close.png" alt="close" title="close" class="button dialog-close-button" />'."\n"
      .'  <div class="ui-dialog-content '.$style_class.'">'."\n";
      
}

function dialog_end() {
  echo '  </div>'."\n"
      .'</div>'."\n";
}

function dialog_form_begin( $name, $target = '' ) {
  dialog_begin( $name );
  echo '  <form id="'.$name.'-form" action="'.$name.'.php" method="post"'.( $target ? ' target="'.$target.'"': '').'>'."\n"
      .'    <fieldset id="'.$name.'-fieldset" class="ui-fieldset"'.(isset( $_SESSION[$name.'-error'] ) ? '' : ' disabled="disabled"').'>'."\n";
}

function dialog_form_end( $name, $submit_name ) {
  echo (isset( $_SESSION[$name.'-error'] ) ? '    <span class="error-msg">'.$_SESSION[$name.'-error'].'&nbsp;</span>'."\n" : '' )
       .'    <input type="submit" value="'.$submit_name.'" />'."\n"
       .'    </fieldset>'."\n"
       .'  </form>'."\n";
  dialog_end();
  unset( $_SESSION[$name.'-error'] );  
}

function dialog_box_begin( $name ) {
  dialog_begin( $name, 'ui-dialog-box' );
}

function dialog_box_end() {
  dialog_end();
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
