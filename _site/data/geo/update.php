<?php

include '../../php/const.php';
include '../../php/func.php';

safe_session_start();

if ( !isset($_SESSION['user-settings']['AC']) || $_SESSION['user-settings']['AC'] < USER_AC::MOD ) {
  echo 'you wish.';
  return;
}

$dataset = filter_input( INPUT_GET, 'dataset' );
if ( empty( $dataset ) ) {
  echo 'update <b>what?</b>';
  return;
}
if ( !is_dir( $dataset ) ) {
  echo $dataset, ' doesn\'t exist';
  return;
}
system( 'cat pre.txt > '.$dataset.'.tmp' );
$first = TRUE;
$output = '>>'.$dataset.'.tmp';
foreach( glob( $dataset.'/*.json' ) as $file ) {
  system( $first ? 'cat '.$file.$output : 'cat separator.txt '.$file.$output  );
  $first = FALSE;
}
system( "cat post.txt$output; mv $dataset.tmp $dataset.json" );
echo $dataset, ' updated';
