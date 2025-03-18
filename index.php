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

    if (isset($request_uri[1]) && !empty($request_uri[1])) {
        $endpoint = $request_uri[1]; // Get the endpoint (quotes, authors, categories)
        error_log("API Endpoint: " . $endpoint);

        include_once __DIR__ . '/classes/Database.php';
        $db = new Database();
        $conn = $db->connect();

        switch ($endpoint) {
            case 'authors':
                include_once __DIR__ . '/controllers/AuthorController.php';
                $controller = new AuthorController($conn);
                break;
            case 'categories':
                include_once __DIR__ . '/controllers/CategoryController.php';
                $controller = new CategoryController($conn);
                break;
            case 'quotes':
                include_once __DIR__ . '/controllers/QuoteController.php';
                $controller = new QuoteController($conn);
                break;
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Endpoint not found']);
                exit;
        }

        $controller->handleRequest($method, $_GET, file_get_contents('php://input'));

    } else {
        // Handle the /api/ case (no endpoint specified)
        http_response_code(200); // Or another appropriate status code
        echo json_encode(['message' => 'Welcome to the Quotes API. Available endpoints: /api/quotes, /api/authors, /api/categories']);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not found']);
}
?>