<?php

include 'php/func.php';
include 'php/update.php';

safe_session_start();

assert_access_class( USER_AC::MOD );

update_dataset_echo( 'data/geo/' );
