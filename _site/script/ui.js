// Google Maps interface for the page
"use strict";

function onClickID( id_name, func ) {
  var elem = document.getElementById( id_name );
  if ( elem )
    elem.addEventListener( "click", func );
}

function toggleDisplay( id, display_type ) {
  if ( typeof ( display_type ) === 'undefined' )
    display_type = "block";
  var e_style = document.getElementById( id ).style;
  e_style.display = ( e_style.display === $display_type ) ? "none" : $display_type;
}

function toggleVisible( $id ) {
  var e_style = document.getElementById( $id ).style;
  e_style.opacity = 1 - e_style.opacity;
}

function hideDialogs() {
  var elems = document.getElementsByClassName( "ui-dialog" );
  for( var i = elems.length - 1; i >= 0; i-- ) {
    elems[i].style.opacity = 0;
    elems[i].style.zIndex = -100;
  }
  var elems = document.getElementsByClassName( "ui-fieldset" );
  for( var i = elems.length - 1; i >= 0; i-- )
    elems[i].disabled = "disabled";
}

function switchDialog( id ) {
  var e_style = document.getElementById( id + "-box" ).style;
  var old_opacity = e_style.opacity;
  hideDialogs();
  if ( old_opacity == 0 ) { // 0 or not set
    e_style.opacity = 1;
    e_style.zIndex = 100;
    document.getElementById( id + "-fieldset" ).disabled = "";
  }
}

function onClickDialog( name ) {
  onClickID( "nav-" + name, function() {
    switchDialog( name );
  } );
}

onClickDialog( "login" );
onClickDialog( "new-user" );
onClickDialog( "add-feature" );

onClickID( "nav-logout", function() {
  window.location.replace( "logout.php" );
} );

var buttons = document.getElementsByClassName( "button" );
for( var i = buttons.length - 1; i >= 0; i-- ) {
  var butt = buttons[i];
  butt.addEventListener( "mousedown", function() {
    this.style.top = "5px";
  } );
  butt.addEventListener( "mouseup", function() {
    this.style.top = "0";
  } );
}
