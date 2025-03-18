<?php
// CORS and OPTIONS handling (same for all files)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Include files
include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../controllers/CategoryController.php';

// Database connection
$database = new Database();
$db = $database->connect();

// Controller instance
$controller = new CategoryController($db);

// Controller method call
$controller->handleRequest($method, $_GET, file_get_contents('php://input')); // added php://input for PUT and POST
?>