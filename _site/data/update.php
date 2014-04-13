<?php
$dataset = $_GET['dataset'];
if ( empty($dataset) || !is_dir($dataset) ) {
  echo $dataset, ' doesn\'t exist';
  return;
}
system( 'cat pre.txt > '.$dataset.'.tmp' );
$first = TRUE;
$output = '>>'.$dataset.'.tmp';
foreach( glob($dataset.'/*.json') as $file ) {
  system( $first ? 'cat '.$file.$output
                : 'cat separator.txt '.$file.$output );
  $first = FALSE;
}
system( "cat post.txt$output; mv $dataset.tmp $dataset.json" );
