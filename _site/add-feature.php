<?php

include 'php/func.php';
include 'php/update.php';

safe_session_start();

assert_access_class( USER_AC::USER );

$type = filter_POST( 'feature-type' );
if ( !$type )
  form_error( 'add-feature', 'brak typu obiektu' );
$id = filter_POST( 'feature-id' );
if ( !ctype_alnum( $id ) )
  form_error( 'add-feature', 'id nie alfanumeryczne' );

$dataset_path = 'data/geo/';
$base_path = $type.'/'.$id.'.json'; 
$target_path = $dataset_path.$base_path;
$incoming_path = $dataset_path.'/_incoming/'.$base_path;
 
if ( has_access_class( USER_AC::MOD ) ) {
  if ( file_exists( $target_path ) )
    form_error( 'add-feature', 'id juz w uzyciu (target)' );
} else if ( file_exists( $target_path ) || file_exists( $incoming_path ) )
  form_error( 'add-feature', 'id juz w uzyciu' );

$name = filter_POST( 'feature-name' );
if ( strlen( $name ) < 4 )
  form_error( 'add-feature', 'nazwa za krotka (< 4 znakow)' );
$desc = filter_POST( 'feature-desc' );
if ( strlen( $desc ) < 4 )
  form_error( 'add-feature', 'opis za krotki (< 4 znakow)' );
$center = filter_POST( 'feature-center' );
if ( !$center )
  form_error( 'add-feature', 'centrum puste' );

$vertex_nr = 0;
$vertices = [];
for( ; ; $vertex_nr++ ) {
  $fv = filter_POST( 'feature-vertex-'.$vertex_nr );
  if ( !$fv )
    break;
  $vertices[] = json_decode( '['.$fv.']', true );
}

$feat_type;
if ( $vertex_nr == 1 ) {
  $vertices = $vertices[0];
  $feat_type = 'Point';  
} else if ( $vertex_nr == 2 || filter_POST( 'as-line' ) ) {
  $feat_type = 'LineString';
} else {
  $vertices[$vertex_nr] = $vertices[0]; // close the poly's loop
  $vertices = [ $vertices ];
  $feat_type = 'Polygon';
} 

$feat = [
  'type' => 'Feature',
  'properties' => [
    'type' => $type,
    'name' => $name,
    'desc' => $desc,
    'center' => json_decode( '['.$center.']', true ),
    'deco' => filter_POST( 'is-decoration' )
  ],
  'id' => $id,
  'geometry' => !$vertex_nr ? null : [
    'type' => $feat_type,
    'coordinates' => $vertices
  ] 
];

json_encode_file( has_access_class( USER_AC::MOD ) ? $target_path : $incoming_path, $feat );

if ( has_access_class( USER_AC::MOD ) && filter_POST( 'autoupdate' ) )
  update_dataset( $type, $dataset_path );  

//var_dump( $_POST );

form_error( 'add-feature', 'obiekt wyslano' );