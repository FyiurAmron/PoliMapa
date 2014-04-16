<?php

include 'php/func.php';

safe_session_start();
unset( $_SESSION['user'] );
unset( $_SESSION['user-settings'] );
redirect();
