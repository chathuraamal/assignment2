<?php 
// the database is used to store fileid of files that are uploaded
// Database connectivity configuration are defined here    
define('DB_HOST', 'localhost'); 
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', 'root'); 
define('DB_NAME', 'gdrive'); 
 
// configuration for Google API connectivity
define('GOOGLE_CLIENT_ID', '620123644339-pgmlq6ug84027b24phs33c28onfcq393.apps.googleusercontent.com'); 
define('GOOGLE_CLIENT_SECRET', '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'); 
define('GOOGLE_OAUTH_SCOPE', 'https://www.googleapis.com/auth/drive'); 
define('REDIRECT_URI', 'http://localhost/gdrive/google_drive_sync.php'); 
 
// starting session 
if(!session_id()) session_start(); 
 
// OAuth URL for google
$OauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . REDIRECT_URI . '&response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&access_type=online'; 
 
?>