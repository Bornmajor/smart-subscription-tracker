<?php
//neccessary to start session before logging out
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['usr_id'] = null;
$_SESSION['mail'] = null;

//deleting login cookie
// Set the cookie to expire in the past to delete it
setcookie('smartpark', '', time() - 3600, '/', 'localhost', true, true);


header("location: ?page=login");


?>