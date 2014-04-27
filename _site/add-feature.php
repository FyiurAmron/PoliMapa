<?php

include 'php/const.php';
include 'php/func.php';

safe_session_start();

$type = filter_input( INPUT_POST, 'feature-type' );
if ( !$type )
  form_error( 'add-feature', 'brak typu obiektu' );
$id = filter_input( INPUT_POST, 'feature-id' );
if ( !ctype_alnum( $id ) )
  form_error( 'add-feature', 'id nie alfanumeryczne' );
$base_path = $type.'/'.$id.'.json'; 
$target_path = 'data/geo/'.$base_path;
$incoming_path = 'data/geo/_incoming/'.$base_path; 
if ( has_access_class( USER_AC::MOD ) ) {
  if ( file_exists( $target_path ) )
    form_error( 'add-feature', 'id juz w uzyciu (target)' );
} else if ( file_exists( $target_path ) || file_exists( $incoming_path ) )
  form_error( 'add-feature', 'id juz w uzyciu' );

$name = filter_input( INPUT_POST, 'feature-name' );
if ( strlen( $name ) < 4 )
  form_error( 'add-feature', 'nazwa za krotka (< 4 znakow)' );
$desc = filter_input( INPUT_POST, 'feature-desc' );
if ( strlen( $desc ) < 4 )
  form_error( 'add-feature', 'opis za krotki (< 4 znakow)' );
$center = filter_input( INPUT_POST, 'feature-center' );
if ( !$center )
  form_error( 'add-feature', 'centrum puste' );

$vertex_nr = 0;
$vertices = [];
for( ; ; $vertex_nr++ ) {
  $fv = filter_input( INPUT_POST, 'feature-vertex-'.$vertex_nr );
  if ( !$fv )
    break;
  $vertices[] = json_decode( '['.$fv.']', true );
}
if ( $vertex_nr > 2 ) // close the poly's loop
  $vertices[$vertex_nr] = $vertices[0]; 

$feat = [
  'type' => 'Feature',
  'properties' => [
    'type' => $type,
    'name' => $name,
    'desc' => $desc,
    'center' => json_decode( '['.$center.']', true )
  ],
  'id' => $id,
  'geometry' => !$vertex_nr ? null : [
    'type' => ( $vertex_nr <= 1 ) ? 'Point' : ( ( $vertex_nr == 2 ) ? 'LineString' : 'Polygon' ),
    'coordinates' => ( ( $vertex_nr > 2 ) ? [ $vertices ] : $vertices ) 
  ] 
];

json_encode_file( has_access_class( USER_AC::MOD ) ? $target_path : $incoming_path, $feat );

//var_dump( $_POST );

form_error( 'add-feature', 'obiekt wyslano' );
