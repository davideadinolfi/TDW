<?php
require_once 'helpers.php';


// distruggo sessione in modo sicuro
$_SESSION = [];
if (ini_get('session.use_cookies')) {
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000,
$params["path"], $params["domain"],
$params["secure"], $params["httponly"]
);
}
session_destroy();


header('Location: index.php');
exit;