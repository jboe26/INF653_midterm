<?php

// CORS Headers
header('Access-Control-Allow-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

error_log("SERVER REQUEST URI: " . $_SERVER['REQUEST_URI']);

$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

error_log("Raw REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("Parsed REQUEST_URI: " . print_r($request_uri, true));
error_log("REQUEST_URI[0]: " . (isset($request_uri[0]) ? $request_uri[0] : 'Not Set'));
error_log("REQUEST_URI[1]: " . (isset($request_uri[1]) ? $request_uri[1] : 'Not Set'));

// Simplified if condition for testing
if ($request_uri[0] === 'api') {
    error_log("API Routing Triggered");

    // API Routing Logic
    header('Content-Type: application/json');

    // Add this check for /api/
    if (!isset($request_uri[1])) {
        http_response_code(200);
        echo json_encode(["message" => "Welcome to the QuoteDB API. Available endpoints: /api/quotes, /api/authors, /api/categories"]);
        exit;
    }

    $endpoint = $request_uri[1];

    if (in_array($endpoint, ['quotes', 'authors', 'categories'])) {
        include_once 'api/classes/Database.php';
        include_once 'api/classes/Quote.php';
        include_once 'api/controllers/QuoteController.php';
        include_once 'api/classes/Author.php';
        include_once 'api/controllers/AuthorController.php';
        include_once 'api/classes/Category.php';
        include_once 'api/controllers/CategoryController.php';

        $database = new Database();
        $db = $database->getConnection();

        switch ($endpoint) {
            case 'quotes':
                $controller = new QuoteController($db);
                break;
            case 'authors':
                $controller = new AuthorController($db);
                break;
            case 'categories':
                $controller = new CategoryController($db);
                break;
            default:
                http_response_code(404);
                echo json_encode(["message" => "Invalid endpoint."]);
                exit();
        }

        $controller->handleRequest($method, $_GET);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Invalid endpoint."]);
    }

} else {
    http_response_code(404);
    echo json_encode("HTML Routing Triggered");
}
?>