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

// Add handling for the `/api` root endpoint
if ($_SERVER['REQUEST_URI'] === '/api') {
    header('Content-Type: application/json');
    echo json_encode([
        "message" => "Welcome to the Quote API. Use endpoints like /quotes, /authors, or /categories for data."
    ]);
    exit();
}

// Parse the requested URL
$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$endpoint = isset($request_uri[2]) ? $request_uri[2] : 'quotes'; // Default to 'quotes'
$method = $_SERVER['REQUEST_METHOD'];

// Validate and route to the appropriate controller
if (in_array($endpoint, ['quotes', 'authors', 'categories'])) {
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

        default:
            // Invalid endpoint handling
            http_response_code(404);
            echo json_encode(["message" => "Invalid endpoint."]);
            exit(); // Stop further processing
    }

    // Handle the request
    $controller->handleRequest($method, array_slice($request_uri, 3));
} else {
    // Invalid endpoint
    http_response_code(404);
    echo json_encode(["message" => "Invalid endpoint."]);
}
?>
