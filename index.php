<?php
// Redirect root URL to API quotes endpoint
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') {
    include_once __DIR__ . '/api/index.php';
    exit;
}

// For any unexpected behavior, show a helpful message
echo json_encode([
    "status" => "error",
    "message" => "Invalid request. Please use the API endpoints under /api/."
]);
?>
