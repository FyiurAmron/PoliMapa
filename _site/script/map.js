// Maps API implementation of a map logic & UI 
"use strict";
/* global google */
/* global onClickID, makeClickButton */
/* global splitAfterLast, splitBeforeLast */
/* jshint -W031 */ // since Google Maps API uses side effects for constructors

var PG_lat = 54.37167, PG_lng = 18.61672,
        lat = PG_lat, lng = PG_lng,
        geo_lat = lat, geo_lng = lng;
var PG_LatLng = new google.maps.LatLng( PG_lat, PG_lng );
var poly_style_selected = { strokeWeight: 2, fillOpacity: 0.75 };
var dotline_style_selected = { strokeWeight: 8 };
var old_set_fn = google.maps.InfoWindow.prototype.set;
var myMap;

function getSelectedStyle( feature ) {
  var geom = feature.getGeometry();
  return geom ? ( ( geom.getType() == "Polygon" ) ? poly_style_selected : dotline_style_selected ) : null;
}

function setSelectedStyle( data, feature ) {
  data.overrideStyle( feature, getSelectedStyle( feature ) );  
}

// workaround for possible POI InfoWindow issues
// - see http://code.google.com/p/gmaps-api-issues/issues/detail?id=3866
function setPopupOnPOI( enable ) {
  google.maps.InfoWindow.prototype.set = enable ? old_set_fn : function( key, val ) {
    if ( key !== 'map' )
      old_set_fn.apply( this, arguments );
  }; 
}

function load_dataset( set_name, tint ) {
  if ( myMap ) {
    var new_data = new google.maps.Data( { map: myMap } ); 
    setMapDataStyle( new_data, tint );
    addMapDataListeners( new_data );
    var hReq = new XMLHttpRequest();
    hReq.onload = function() {
      new_data.addGeoJson( JSON.parse( hReq.response ) );
      var feature_displ_list = document.getElementById( "selection-list" );
      while( feature_displ_list.firstChild )
        feature_displ_list.removeChild( feature_displ_list.firstChild );
      new_data.forEach( function( feature ) {
        var feature_displ_list = document.getElementById( "selection-list" );
        var feature_span = document.createElement( "span" );
        var feature_name = feature.getProperty( "name" );
        feature_span.innerHTML = feature_name;
        var coords = feature.getProperty( "center" );
        var center_latlng = new google.maps.LatLng( coords[1], coords[0] );
        feature_span.addEventListener( "click", function() {
          myMap.setCenter( center_latlng );
          document.getElementById( "map-wrapper" ).scrollIntoView();
        } );
        if ( feature.getGeometry() ) {
          feature_span.addEventListener( "mouseover", function() {
            setSelectedStyle( new_data, feature );
          } );
          feature_span.addEventListener( "mouseout", function() {
            new_data.revertStyle();
          } );
        } else {
          var marker = new google.maps.Marker( {
            position: center_latlng,
            map: myMap,
            title: feature_name,
            icon: "img/nav-" + set_name + "-small.png",
            opacity: 0.5
          } );
          marker.addListener( "click", function( event ) {
            var coords = feature.getProperty( "center" );
            myMap.setCenter( new google.maps.LatLng( coords[1], coords[0] ) );
          } );
          feature_span.addEventListener( "mouseover", function() {
            marker.setOpacity( 1 );
          } );
          feature_span.addEventListener( "mouseout", function() {
            marker.setOpacity( 0.5 );
          } );          
        } 
        var feature_div = document.createElement( "div" );
        feature_div.appendChild( feature_span );
        feature_displ_list.appendChild( feature_div );
      } );
      myMap.data.setMap( null );
      myMap.data = new_data;
    };
    hReq.open( "GET", "data/geo/" + set_name + ".json" );
    hReq.send();
  }  
}

function setMapDataStyle( data, tint ) {
  data.setStyle( function( feature ) {
    return feature.getProperty( "isHidden" ) ? {
               fillOpacity: 0,
               strokeOpacity: 0,
             }
           : {
               fillColor: tint,
               strokeColor: tint,
               strokeWeight: ( feature.getGeometry() && feature.getGeometry().getType() == "Polygon" ) ? 1 : 4
             };
  } );
}

function addMapDataListeners( data ) {
  data.addListener( "click", function( event ) {
    var coords = event.feature.getProperty( "center" );
    myMap.setCenter( new google.maps.LatLng( coords[1], coords[0] ) );
  } );

  data.addListener( "mouseover", function( event ) {
    setSelectedStyle( data, event.feature );
    var caption = document.getElementById( "feature-caption" );
    caption.style.visibility = "visible";    
    caption.innerHTML = event.feature.getProperty( "name" );
  } );

  data.addListener( "mouseout", function( event ) {
    data.revertStyle();
    var caption = document.getElementById( "feature-caption" );
    caption.style.visibility = "hidden";
    caption.innerHTML = "";
  } );
}

function initialize_maps() {
  getLocation();
  var styles = [ {
    featureType: "poi",
    elementType: "labels",
    stylers: [
      { visibility: "off" }
    ]
  } ]; 
  var noPOI_style = new google.maps.StyledMapType( styles, {name: "Bez znacznikow z Google"} );
  
  var mapOptions = {
    center: PG_LatLng,
    zoom: 17,
    draggableCursor: "default",
    mapTypeControlOptions: {
      mapTypeIds: [
        google.maps.MapTypeId.ROADMAP,
        google.maps.MapTypeId.HYBRID,
        google.maps.MapTypeId.SATELLITE,
        google.maps.MapTypeId.TERRAIN,
        "noPOI_style"
      ]
    }
  };
  myMap = new google.maps.Map( document.getElementById( "map-canvas" ), mapOptions );
  myMap.mapTypes.set( "noPOI_style", noPOI_style );
  //myMap.setMapTypeId( "noPOI_style" );
}

function getLocation() {
  var gl = navigator.geolocation;
  if ( gl )
    gl.getCurrentPosition( function( position ) {
      geo_lat = position.coords.latitude;
      geo_lng = position.coords.longitude;
      new google.maps.Marker( {
        position: new google.maps.LatLng( geo_lat, geo_lng ),
        map: myMap,
        title: "Jestes Tutaj!",
        icon: "img/crosshair.png",
        iconAnchor: "16 16"
      } );
      var geobutton = document.createElement( "img" );
      geobutton.src = "img/crosshair.png";
      geobutton.className = "button";
      geobutton.addEventListener( "click", function() {
        if ( myMap )
          myMap.setCenter( new google.maps.LatLng( geo_lat, geo_lng ) );
      } );
      document.getElementById( "toolbar" ).appendChild( geobutton );
    } );
}

function centerOnPG() {
  if ( myMap )
    myMap.setCenter( PG_LatLng );
}

onClickID( "logo", centerOnPG );
onClickID( "logo-small", centerOnPG );

function coordsToId( id, lat, lng ) {
  document.getElementById(id).value = lng + ", " + lat;
}

var loc_enabled = false;

function enableLocateButton( name ) {
  onClickID( "loc-" + name, function() {
    if ( loc_enabled )
      return false;
    loc_enabled = true;
    myMap.setOptions({draggableCursor: "crosshair"});
    google.maps.event.addListenerOnce( myMap, "click", function( event ) {
      coordsToId( "input-" + name, event.latLng.lat(), event.latLng.lng() );
      myMap.setOptions( {
        draggableCursor: "default",
      } );
      loc_enabled = false;
      
      var number = parseInt( splitAfterLast( name, '-') );
      if ( isNaN(number) )
        return;
        
      number++;
      var new_name = splitBeforeLast( name, '-' ) + '-' + number;
      var new_button_name = "loc-" + new_name;
      if ( document.getElementById( new_button_name ) )
        return;
        
      var old_button = document.getElementById( "loc-" + name );      
      var new_button = old_button.cloneNode();
      new_button.id = "loc-" + new_name;
      new_button.alt = "locate " + new_name;
      new_button.title = new_button.alt;
      makeClickButton( new_button );
      
      var new_input = document.getElementById("input-" + name).cloneNode();
      new_input.id = "input-" + new_name;
      new_input.name = new_name;

      var my_parent = old_button.parentNode;
      var my_input_div = document.createElement("div");
      my_input_div.className = "input-field"; 
      my_input_div.appendChild( new_input );
      my_parent.appendChild( my_input_div );
      my_parent.appendChild( new_button );
      enableLocateButton( new_name );
    } );
    google.maps.event.trigger( myMap, "resize" );
  } );
}

enableLocateButton( "feature-center" );
enableLocateButton( "feature-vertex-0" );

function onClickDataset( name, tint ) {
  onClickID( "nav-" + name, function() {
    load_dataset( name, tint );
  } );
}

onClickDataset( "sci", "black" );
onClickDataset( "atm", "green" );
onClickDataset( "food", "red" );
onClickDataset( "pub", "orange" );

google.maps.event.addDomListener( window, "load", initialize_maps );
