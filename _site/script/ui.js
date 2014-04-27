// UI interface scripts
"use strict";

function splitBeforeLast( str, splitter ) {
  return str.substr( 0, str.lastIndexOf( splitter ) );
}

function splitAfterLast( str, splitter ) {
  return str.substring( str.lastIndexOf( splitter ) + 1 );
}

function onClickID( id_name, func ) {
  var elem = document.getElementById( id_name );
  if ( elem )
    elem.addEventListener( "click", func );
}

function toggleDisplay( id, display_type ) {
  if ( typeof ( display_type ) === 'undefined' )
    display_type = "block";
  var e_style = document.getElementById( id ).style;
  e_style.display = ( e_style.display === display_type ) ? "none" : display_type;
}

function toggleVisible( $id ) {
  var e_style = document.getElementById( $id ).style;
  e_style.opacity = 1 - e_style.opacity;
}

function hideDialogs() {
  var elems = document.getElementsByClassName( "ui-dialog-active" );
  for( var i = elems.length - 1; i >= 0; i-- )
    elems[i].classList.remove("ui-dialog-active");
  elems = document.getElementsByClassName( "ui-fieldset" );
  for( i = elems.length - 1; i >= 0; i-- )
    elems[i].disabled = "disabled";
}

function switchDialog( id ) {
  var elem = document.getElementById( id + "-box" );
  if ( elem.classList.contains( "ui-dialog-active" ) ) {
    elem.classList.remove( "ui-dialog-active" );
    document.getElementById( id + "-fieldset" ).disabled = "disabled";  
  } else {
    hideDialogs();
    elem.classList.add( "ui-dialog-active" );
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

onClickID( "nav-scroll-to-top", function() {
  document.body.scrollIntoView();
} );

function makeClickButton( butt ) {
  butt.addEventListener( "mousedown", function() {
    this.classList.add( "button-down" );
  } );
  butt.addEventListener( "mouseup", function() {
    this.classList.remove( "button-down" );
  } );
}

var buttons = document.getElementsByClassName( "button" );
for( var i = buttons.length - 1; i >= 0; i-- )
  makeClickButton( buttons[i] );

var close_buttons = document.getElementsByClassName( "dialog-close-button" );
for( var i = close_buttons.length - 1; i >= 0; i-- )
  close_buttons[i].onclick = hideDialogs;
