// Maps API implementation of a map logic & UI 
"use strict";
/* global google */
/* global fireOnMousedown, makeButton */
/* global splitAfterLast, splitBeforeLast */
/* jshint -W031 */ // since Google Maps API uses side effects for constructors

var PG_lat = 54.37167, PG_lng = 18.61672,
        lat = PG_lat, lng = PG_lng,
        geo_lat = lat, geo_lng = lng;
var PG_LatLng = new google.maps.LatLng( PG_lat, PG_lng );
var poly_style_selected = { strokeWeight: 2, fillOpacity: 0.75 };
var dotline_style_selected = { strokeWeight: 8 };
var old_set_fn = google.maps.InfoWindow.prototype.set;
var loc_enabled = false;
var myMap;
var markers = [];

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

function setMapCaption( caption ) {
  var cap_elem = document.getElementById( "feature-caption" );
  cap_elem.style.visibility = caption ? "visible" : "hidden";
  cap_elem.innerHTML = caption;
}

function setMapDataStyle( data, tint ) {
  data.setStyle( function( feature ) {
    return feature.getProperty( "isHidden" ) ? {
      fillOpacity: 0,
      strokeOpacity: 0,
    } : {
      fillColor: tint,
      strokeColor: tint,
      strokeWeight: ( feature.getGeometry() && feature.getGeometry().getType() == "Polygon" ) ? 1 : 4
    };
  } );
}

function addMapDataListeners( data ) {
  data.addListener( "rightclick", function( event ) {
    var coords = event.feature.getProperty( "center" );
    myMap.setCenter( new google.maps.LatLng( coords[1], coords[0] ) );
  } );
  
  data.addListener( "click", function( event ) {
    var iw = event.feature.getProperty( "iw" );
    iw.open( myMap );
    iw.setPosition( event.latLng );
  } );

  data.addListener( "mouseover", function( event ) {
    setSelectedStyle( data, event.feature );
    setMapCaption( event.feature.getProperty( "name" ) );   
  } );

  data.addListener( "mouseout", function( event ) {
    data.revertStyle();
    setMapCaption();
  } );
}

function load_dataset( set_name, tint ) {
  if ( myMap ) {
    for( var i = markers.length - 1; i >= 0; i-- )
      markers[i].setMap( null );
    markers.length = 0;
    myMap.data.forEach( function( feature ) {
      var iw = feature.getProperty( "iw" );
      if ( iw )
        iw.close();
    } );
      
    var new_data = new google.maps.Data( { map: myMap } ); 
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
        var event_receiver;
        feature_span.addEventListener( "click", function() {
          myMap.setCenter( center_latlng );
          document.getElementById( "map-wrapper" ).scrollIntoView();
        } );
        var iw = new google.maps.InfoWindow( {
          content: feature.getProperty( "desc" ) 
        } );
        if ( feature.getGeometry() ) {
          if ( !feature.getProperty( "deco" ) ) {
            feature_span.addEventListener( "mouseover", function() {
              setSelectedStyle( new_data, feature );
            } );
            feature_span.addEventListener( "mouseout", function() {
              new_data.revertStyle();
            } );
          }
          feature.setProperty( "iw", iw );
        } else {
          var marker = new google.maps.Marker( {
            position: center_latlng,
            map: myMap,
            title: feature_name,
            icon: "img/nav-" + set_name + "-small.png",
            opacity: 0.5
          } );
          var highlight = function() {
            marker.setOpacity( 1 );          
          };
          var dim = function() {
            marker.setOpacity( 0.5 );
          };
          marker.addListener( "click", function( event ) {
            iw.open( myMap, marker );
          } );
          marker.addListener( "rightclick", function( event ) {
            var coords = feature.getProperty( "center" );
            myMap.setCenter( new google.maps.LatLng( coords[1], coords[0] ) );
          } );
          marker.addListener( "mouseover", function() {
            setMapCaption( feature.getProperty( "name" ) );    
            highlight(); 
          } );
          marker.addListener( "mouseout", function() {
            setMapCaption();
            dim();
          } );
          if ( !feature.getProperty( "deco" ) ) {
            feature_span.addEventListener( "mouseover", highlight );
            feature_span.addEventListener( "mouseout", dim );
          }
          markers.push( marker );
        }
        if ( !feature.getProperty( "deco" ) ) { 
          var feature_div = document.createElement( "div" );
            feature_div.appendChild( feature_span );
            feature_displ_list.appendChild( feature_div );
        }
      } );
        
      myMap.data.setMap( null );
      myMap.data = new_data;
    };
    setMapDataStyle( new_data, tint );
    addMapDataListeners( new_data );
    hReq.open( "GET", "data/geo/" + set_name + ".json" );
    hReq.send();
  }  
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
  myMap.addListener( "rightclick", function( event ) {
    myMap.setCenter( event.latLng );
  } );
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

function coordsToId( id, lat, lng ) {
  document.getElementById(id).value = lng + ", " + lat;
}

function enableLocateButton( name ) {
  fireOnMousedown( "loc-" + name, function() {
    if ( loc_enabled )
      return false;
    loc_enabled = true;
    myMap.setOptions({draggableCursor: "crosshair"});
    google.maps.event.addListenerOnce( myMap, "click", function( event ) {
      myMap.setOptions( {
        draggableCursor: "default",
      } );
      loc_enabled = false;
      coordsToId( "input-" + name, event.latLng.lat(), event.latLng.lng() );
            
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
      makeButton( new_button );
      
      var new_input = document.getElementById("input-" + name).cloneNode();
      new_input.id = "input-" + new_name;
      new_input.name = new_name;
      new_input.value = "";

      var my_parent = old_button.parentNode;
      var my_input_div = document.createElement("div");
      my_input_div.className = "input-field"; 
      my_input_div.appendChild( new_input );
      my_parent.appendChild( my_input_div );
      my_parent.appendChild( new_button );
      enableLocateButton( new_name );
    } );
    //google.maps.event.trigger( myMap, "resize" );
  } );
}

function onMousedownDataset( name, tint ) {
  fireOnMousedown( "nav-" + name, function() {
    load_dataset( name, tint );
  } );
}

(function(){

fireOnMousedown( "logo", centerOnPG );
fireOnMousedown( "logo-small", centerOnPG );

enableLocateButton( "feature-center" );
enableLocateButton( "feature-vertex-0" );

onMousedownDataset( "sci", "black" );
onMousedownDataset( "atm", "green" );
onMousedownDataset( "food", "red" );
onMousedownDataset( "pub", "orange" );

google.maps.event.addDomListener( window, "load", initialize_maps );

})();
