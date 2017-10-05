<?php
set_time_limit(0);
error_reporting(0);
date_default_timezone_set('PRC');
define('USEPROXY', 0);
define('USESOCKS5', 0);
define('P_Y', '127.0.0.1:8088');

//*

define('CONSUMER_KEY', getenv('CONSUMER_KEY'));
define('CONSUMER_SECRET', getenv('CONSUMER_SECRET'));
define('AC_CONSUMER_KEY', getenv('AC_CONSUMER_KEY'));
define('AC_CONSUMER_SECRET', getenv('AC_CONSUMER_SECRET'));

define('COUNT', 200);
//*/