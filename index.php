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

// JSON response for the root URL
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') {
    echo json_encode([
        "message" => "Welcome to the Quotes API!",
        "endpoints" => [
            "/api/quotes" => "Get all quotes or manage quotes.",
            "/api/authors" => "Get all authors or manage authors.",
            "/api/categories" => "Get all categories or manage categories."
        ],
        "documentation" => "Visit your GitHub repository for detailed documentation."
    ]);
    exit;
}

// Fallback error message for unexpected requests
echo json_encode([
    "status" => "error",
    "message" => "Invalid request. Please use the API endpoints under /api/."
]);
?>
