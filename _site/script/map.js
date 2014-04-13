// Google Maps interface for the page
"use strict";

var PG_lat = 54.37167, PG_lng = 18.61672,
    lat = PG_lat, lng = PG_lng,
    geo_lat = lat, geo_lng = lng;
var PG_LatLng = new google.maps.LatLng( PG_lat, PG_lng );
var map;
var poly;

function initialize_maps() {
  getLocation();
  var mapOptions = {
    center: PG_LatLng,
    //center: JSON.parse('{"k":54.371539847953464,"A":18.61434817314148}'),
    //center: JSON.parse('{"k":54.371539847953464,"A":18.61434817314148}'),
    zoom: 17,
    draggableCursor: "default"
  };
  map = new google.maps.Map( document.getElementById("map-panel"), mapOptions );
  map.data.loadGeoJson( "data/sci.json" );

  //var request = new XMLHttpRequest();
  //request.open("GET", "data/sci.json", false);
  //request.send(null);
  //var CGPG_data = JSON.parse(request.responseText);
  /*
  JSON.parse(request.responseText).locations.forEach( function(v,n) {
    var center = new google.maps.LatLng( v.center[0], v.center[1] );
    var marker = new google.maps.Marker( {
        map: map,
        title: v.name,
        position: center
    } );
    var infowindow = new google.maps.InfoWindow({
      content: v.desc
    } );
    google.maps.event.addListener(marker, "click", function() {
      infowindow.open( map, marker );
    } );
    var polypoints = [];
    v.points.forEach( function(w) {
      polypoints.push( new google.maps.LatLng( w[0], w[1] ) );
    } );
    var newpoly = new google.maps.Polygon( {
      paths: polypoints,
      strokeColor: "#0000FF",
      strokeOpacity: 0.8,
      strokeWeight: 1,
      fillColor: "#0000FF",
      fillOpacity: 0.35,
      map: map,
      visible: false
    });
    var itemdiv = document.createElement("div");
    itemdiv.onclick = function() {
      if (poly)
        poly.setVisible(false);
      poly = newpoly;
      poly.setVisible(true);
      map.setCenter(center);
    }
    var itemspan = document.createElement("span");
    itemspan.innerHTML = v.name;
    itemdiv.appendChild(itemspan);
    document.getElementById("selection-list").appendChild(itemdiv);
  } );
  */
  //alert( JSON.stringify(CGPG_data) );

  // TEMP
  google.maps.event.addListener(map,'click',function(event) {
    //document.getElementById("coords-temp").appendChild( document.createTextNode( event.latLng ) );
    document.getElementById("coords-temp").appendChild( document.createTextNode( JSON.stringify(event.latLng) ) );
  } );
}

function getLocation() {
  var gl = navigator.geolocation;
  if ( gl )
    gl.getCurrentPosition( function(position) {
      geo_lat = position.coords.latitude;
      geo_lng = position.coords.longitude;
      var my_pos = new google.maps.Marker( {
        position: new google.maps.LatLng( geo_lat, geo_lng ),
        map: map,
        title: "Jestes Tutaj!",
        icon: "img/crosshair.png"
      } );
      var geobutton = document.getElementById("geoloc");
      geobutton.onclick = function() {
        if ( map )
          map.setCenter( new google.maps.LatLng(geo_lat,geo_lng) );
      };
      geobutton.style.opacity = 0.8;
      geobutton.className += " button";
    } );
}

document.getElementById("logo").onclick = function() {
  if ( map )
    map.setCenter( PG_LatLng );
};

google.maps.event.addDomListener( window, "load", initialize_maps );
