<?php
$host = "http://localhost:8080";
$domain = parse_url($host, PHP_URL_HOST);

$cookieParams = session_get_cookie_params();
$cookieParams['lifetime'] = 0;
$cookieParams['path'] = '/';
$cookieParams['domain'] = '';
$cookieParams['secure'] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')? true : false;
$cookieParams['httponly'] = true;

// $cookieParams = [
//     'lifetime' => 0, // Cookie lifetime (0 means until the browser is closed)
//     'path' => '/', // Cookie path (available on all paths of the domain)
//     'domain' => $domain, // Cookie domain (empty string means current domain)
//     'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')? true : false, // Whether the cookie should be sent only over HTTPS
//     'httponly' => true, // Whether the cookie should be accessible only through HTTP
//     'samesite' => 'None' // SameSite attribute value (can be 'None', 'Lax', or 'Strict')
// ];

$c = session_set_cookie_params($cookieParams);
if(session_status() != PHP_SESSION_ACTIVE) session_start();

define('COOKIE_PARAMS', $cookieParams);