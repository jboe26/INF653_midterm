<?php
// Headers for allowing cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Debugging: Output the REQUEST_URI
echo json_encode(array("REQUEST_URI" => $_SERVER['REQUEST_URI']));

// Get the requested endpoint and method
$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
echo json_encode(array("request_uri" => $request_uri));

// Check if the endpoint exists
$endpoint = isset($request_uri[2]) ? $request_uri[2] : null;

echo json_encode(array("endpoint" => $endpoint));

$method = $_SERVER['REQUEST_METHOD'];
echo json_encode(array("method" => $method));

if (isset($endpoint) && in_array($endpoint, ['quotes', 'authors', 'categories'])) {
    // Route requests to controllers
    echo json_encode(array("message" => "Valid endpoint."));

    // Include necessary files
    include_once 'classes/Database.php';
    include_once 'classes/Quote.php';
    include_once 'controllers/QuoteController.php';
    include_once 'classes/Author.php';
    include_once 'controllers/AuthorController.php';
    include_once 'classes/Category.php';
    include_once 'controllers/CategoryController.php';

    // Instantiate database and controllers
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
    }

    // Handle the request
    $controller->handleRequest($method, array_slice($request_uri, 3)); // Pass remaining parts as params

} else {
    // Invalid endpoint
    http_response_code(404);
    echo json_encode(array("message" => "Invalid endpoint."));
}
?>