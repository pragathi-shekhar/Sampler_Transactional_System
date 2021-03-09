<?php
/* Database credentials for running MySQL server with username value 'root' and no password */

/*
// To run MySQL on localhost
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'samplerdatabase');
*/

define('DB_SERVER', 'sql112.epizy.com');
define('DB_USERNAME', 'epiz_28103132');
define('DB_PASSWORD', 'xdBMrXG6pyw');
define('DB_NAME', 'epiz_28103132_samplerdatabase');

/* Connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if the connection exists
if ($link === false)
{
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
