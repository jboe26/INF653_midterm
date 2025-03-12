<?php
// Redirect root URL to API (main quotes endpoint)
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') {
    header("Location: /api/quotes/");
    exit;
}

// Include necessary files for non-API logic (if needed)
include_once __DIR__ . '/api/classes/Database.php';
include_once __DIR__ . '/api/controllers/QuoteController.php';

// Example: Handle basic fallback or custom logic here (optional)
echo json_encode([
    "status" => "error",
    "message" => "Invalid request. Please use the API endpoints under /api/."
]);
?>
