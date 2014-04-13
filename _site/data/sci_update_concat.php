<?php
$dataset = "sci";
system( 'cat pre.txt > '.$dataset.'.json' );
$first = TRUE;
$output = ' >> '.$dataset.'.json';
foreach( glob($dataset.'/*.json') as $file ) {
  system( $first ? 'cat '.$file.$output
                : 'cat separator.txt '.$file.$output );
  $first = FALSE;
}
system( 'cat post.txt'.$output );
