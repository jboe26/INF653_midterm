<?php
// CORS Headers
header('Access-Control-Allow-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("SERVER REQUEST URI: " . $_SERVER['REQUEST_URI']);

$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

error_log("Raw REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("Parsed REQUEST_URI: " . print_r($request_uri, true));
error_log("REQUEST_URI[0]: " . (isset($request_uri[0]) ? $request_uri[0] : 'Not Set'));
error_log("REQUEST_URI[1]: " . (isset($request_uri[1]) ? $request_uri[1] : 'Not Set'));

if ($request_uri[0] === 'api') {
    error_log("API Routing Triggered");

    if (!isset($request_uri[1]) || (isset($request_uri[1]) && empty($request_uri[1]))) {
        include 'index.html'; 
        exit;
    }
} else {
    include 'index.html';
}
?>