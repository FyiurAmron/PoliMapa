<?php

include 'php/const.php';
include 'php/func.php';

safe_session_start();

assert_access_class( USER_AC::MOD );

$dataset = filter_GET( 'dataset' ) ?: filter_POST( 'dataset' );
if ( !$dataset ) {
  echo 'update <b>what?</b>';
  return;
}
$dataset = 'data/geo/'.$dataset;
if ( !is_dir( $dataset ) ) {
  echo $dataset.' doesn\'t exist';
  return;
}
system( 'cat data/geo/pre.txt > '.$dataset.'.tmp' );
$first = true;
$output = '>>'.$dataset.'.tmp';
foreach( glob( $dataset.'/*.json' ) as $file ) {
  system( $first ? 'cat '.$file.$output : 'cat data/geo/separator.txt '.$file.$output  );
  $first = false;
}
system( 'cat data/geo/post.txt'.$output.'; mv '.$dataset.'.tmp '.$dataset.'.json' );
echo '<b>'.$dataset.'</b> updated';
