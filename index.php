<?php

error_reporting(E_ALL);

define('ENV', '0'); //Set to local or live.

ini_set("display_errors", 0);
ini_set('log_errors', 1);
if (ENV == 1)
    ini_set('error_log', '/home/exotic/public_html/MySNL_WebServicev2/Logs/snl_error.txt');
else
    ini_set('error_log', './Logs/snl_error.txt');

ini_set('log_errors_max_len', '0'); //no limit on log file length

require_once("server_main.php");
?>