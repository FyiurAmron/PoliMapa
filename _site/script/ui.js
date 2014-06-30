// UI interface scripts
"use strict";

function splitBeforeLast( str, splitter ) {
  return str.substr( 0, str.lastIndexOf( splitter ) );
}

function splitAfterLast( str, splitter ) {
  return str.substring( str.lastIndexOf( splitter ) + 1 );
}

function fireOnMousedown( id_name, func ) {
  var elem = document.getElementById( id_name );
  if ( elem )
    elem.addEventListener( "mousedown", func );
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
  var elem = document.getElementById( id + "-dialog" );
  var fieldset = document.getElementById( id + "-fieldset" ); 
  if ( elem.classList.contains( "ui-dialog-active" ) ) {
    elem.classList.remove( "ui-dialog-active" );
    if ( fieldset )
      fieldset.disabled = "disabled";  
  } else {
    hideDialogs();
    elem.classList.add( "ui-dialog-active" );
    if ( fieldset )
      fieldset.disabled = "";
  }
}

function navActionDialog( name ) {
  fireOnMousedown( "nav-" + name, function() {
    switchDialog( name );
  } );
}

function makeButton( butt ) {
  butt.addEventListener( "mousedown", function() {
    this.classList.add( "button-down" );
  } );
  butt.addEventListener( "mouseup", function() {
    this.classList.remove( "button-down" );
  } );
}

function makePushButton( butt ) {
  butt.addEventListener( "click", function() {
    this.classList.toggle( "button-down" );
  } );
}

(function(){

var navActionDialogs = [
  "login", "new-user", "add-feature", "info", "help", "admin-panel"
];

for( var i = navActionDialogs.length - 1; i >= 0; i-- )
  navActionDialog( navActionDialogs[i] );

fireOnMousedown( "nav-logout", function() {
  window.location.replace( "logout.php" );
} );

fireOnMousedown( "nav-scroll-to-top", function() {
  document.body.scrollIntoView();
} );

var elems = document.getElementsByClassName( "button" );
for( var i = elems.length - 1; i >= 0; i-- )
  //makePushButton( elems[i] );
  makeButton( elems[i] );

elems = document.getElementsByClassName( "dialog-close-button" );
for( var i = elems.length - 1; i >= 0; i-- )
  elems[i].onclick = hideDialogs;
  
})();
