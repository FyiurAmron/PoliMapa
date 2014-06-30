<?php

include 'php/func.php';
include 'php/update.php';

safe_session_start();

assert_access_class( USER_AC::MOD );

if ( filter_POST( 'dataset-update' ) )
  update_dataset_echo( 'data/geo/' );   
//var_dump( $_POST );
