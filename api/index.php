<?php
// Allow cross-origin requests and set JSON response header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Parse the requested URL
$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$endpoint = isset($request_uri[2]) ? $request_uri[2] : null;
$method = $_SERVER['REQUEST_METHOD'];

// Validate and route to the appropriate controller
if (isset($endpoint) && in_array($endpoint, ['quotes', 'authors', 'categories'])) {
    // Include necessary files
    include_once 'classes/Database.php';
    include_once 'classes/Quote.php';
    include_once 'controllers/QuoteController.php';
    include_once 'classes/Author.php';
    include_once 'controllers/AuthorController.php';
    include_once 'classes/Category.php';
    include_once 'controllers/CategoryController.php';

    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();

    // Instantiate the appropriate controller
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
    }

    // Handle the request
    $controller->handleRequest($method, array_slice($request_uri, 3));
} else {
    // Invalid endpoint
    http_response_code(404);
    echo json_encode(["message" => "Invalid endpoint."]);
}
?>
