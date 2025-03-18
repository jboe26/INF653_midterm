<?php
// CORS Headers
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
include_once __DIR__ . '/../controllers/AuthorController.php';
include_once __DIR__ . '/../controllers/CategoryController.php';
include_once __DIR__ . '/../controllers/QuoteController.php';

// Database connection
$database = new Database();
$db = $database->connect();

// Parse the URL
$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$endpoint = isset($request_uri[1]) ? $request_uri[1] : ''; // Get the endpoint (authors, categories, quotes)

// Routing logic
switch ($endpoint) {
    case 'authors':
        $controller = new AuthorController($db);
        break;
    case 'categories':
        $controller = new CategoryController($db);
        break;
    case 'quotes':
        $controller = new QuoteController($db);
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint not found']);
        exit;
}

// Controller method call
$controller->handleRequest($method, $_GET, file_get_contents('php://input'));
?>