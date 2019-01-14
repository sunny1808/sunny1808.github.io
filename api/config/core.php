<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Asia/Manila');
 
// variables used for jwt
define('AUTH_KEY', 'tg/@N^#LufP:dktD@?FXg.gc@2eDELwOmFXXOM)n_rGiO<t$/:uJ%kGN[p8&<S[=');
define('ISSUSER', "http://localhost.org");
define('AUDIENCE', "http://localhost.com");
define('ISSUED_AT', time());
define('NOT_BEFORE', ISSUED_AT + 10);
?>