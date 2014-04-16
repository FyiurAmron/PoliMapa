// Google Maps interface for the page
"use strict";

var PG_lat = 54.37167, PG_lng = 18.61672,
        lat = PG_lat, lng = PG_lng,
        geo_lat = lat, geo_lng = lng;
var PG_LatLng = new google.maps.LatLng( PG_lat, PG_lng );
var map;

function initialize_maps() {
  getLocation();
  var mapOptions = {
    center: PG_LatLng,
    zoom: 17,
    draggableCursor: "default"
  };
  map = new google.maps.Map( document.getElementById( "map-canvas" ), mapOptions );
  map.data.loadGeoJson( "data/geo/sci.json" );

  map.data.setStyle( function( feature ) {
    if ( feature.getProperty( "isHidden" ) )
      return {
        fillOpacity: 0,
        strokeOpacity: 0,
      };
    return { strokeWeight: 1 };
  } );

  map.data.addListener( "click", function( event ) {
    event.feature.setProperty( "isHidden", true );
  } );

  map.data.addListener( "mouseover", function( event ) {
    //map.data.revertStyle();
    map.data.overrideStyle( event.feature, { strokeWeight: 2 } );
    document.getElementById( "feature-caption" ).innerHTML = event.feature.getProperty( "name" );
  } );

  map.data.addListener( "mouseout", function( event ) {
    map.data.revertStyle();
    document.getElementById( "feature-caption" ).innerHTML = "";
  } );

  // TEMP
  google.maps.event.addListener( map, 'click', function( event ) {
    document.activeElement.value = event.latLng.lng() + ", " + event.latLng.lat();
  } );
}

function getLocation() {
  var gl = navigator.geolocation;
  if ( gl )
    gl.getCurrentPosition( function( position ) {
      geo_lat = position.coords.latitude;
      geo_lng = position.coords.longitude;
      var my_pos = new google.maps.Marker( {
        position: new google.maps.LatLng( geo_lat, geo_lng ),
        map: map,
        title: "Jestes Tutaj!",
        icon: "img/crosshair.png",
        iconAnchor: "16 16"
      } );
      var geobutton = document.createElement( "img" );
      geobutton.src = "img/crosshair.png";
      geobutton.className = "button";
      geobutton.addEventListener( "click", function() {
        if ( map )
          map.setCenter( new google.maps.LatLng( geo_lat, geo_lng ) );
      } );
      document.getElementById( "toolbar" ).appendChild( geobutton );
    } );
}

onClickID( "logo", function() {
  if ( map )
    map.setCenter( PG_LatLng );
} );

google.maps.event.addDomListener( window, "load", initialize_maps );
