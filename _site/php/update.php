<?php

function update_dataset( $dataset, $path = '' ) {
  $dataset = $path.$dataset;
  system( 'cat '.$path.'pre.txt > '.$dataset.'.tmp' );
  $first = true;
  $output = '>>'.$dataset.'.tmp';
  foreach( glob( $dataset.'/*.json' ) as $file ) {
    system( $first ? 'cat '.$file.$output : 'cat '.$path.'separator.txt '.$file.$output  );
    $first = false;
  }
  system( 'cat '.$path.'post.txt'.$output.'; mv '.$dataset.'.tmp '.$dataset.'.json' );
}

function update_dataset_echo( $path ) {
  $dataset = filter_GET( 'dataset' ) ?: filter_POST( 'dataset' );
  if ( !$dataset ) {
    echo 'update <b>what?</b>'."\n";
    return;
  }

  if ( !is_dir( $path.$dataset ) )
    echo $dataset.' doesn\'t exist'."\n";
  else {
    update_dataset( $dataset, $path );
    echo '<b>'.$path.$dataset.'</b> updated'."\n";
  }
}
