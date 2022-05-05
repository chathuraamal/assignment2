<?php 
// Include the main configuration file 
require_once 'config.php'; 
 
// this creates a new db connection 
$dbconn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 
 
// Checking if DB connection succeeded
if ($dbconn->connect_error) { 
    die("Connection failed: " . $dbconn->connect_error); 
    echo "database connectivity failed due to an error";
}