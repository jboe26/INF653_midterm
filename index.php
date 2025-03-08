<?php
// Include all necessary class files
include_once __DIR__ . '/api/classes/Database.php';       // Database connection class
include_once __DIR__ . '/api/classes/Quote.php';          // Quote model
include_once __DIR__ . '/api/classes/Author.php';         // Author model
include_once __DIR__ . '/api/classes/Category.php';       // Category model

// Include all necessary controller files
include_once __DIR__ . '/api/controllers/QuoteController.php';    // Quote controller
include_once __DIR__ . '/api/controllers/AuthorController.php';   // Author controller
include_once __DIR__ . '/api/controllers/CategoryController.php'; // Category controller

// Initialize the database connection
$db = new Database();
$conn = $db->getConnection();

// Check the database connection
if (!$conn) {
    die(json_encode([
        "status" => "error",
        "message" => "Failed to connect to the database."
    ]));
}

// Instantiate controllers
$quoteController = new QuoteController($conn);
$authorController = new AuthorController($conn);
$categoryController = new CategoryController($conn);

// Handle HTTP requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Example: Fetch quotes from the database
        $quotes = $quoteController->fetchQuotes();

        // Return the quotes as JSON
        echo json_encode([
            "status" => "success",
            "data" => $quotes
        ]);
    } catch (Exception $e) {
        // Handle errors gracefully
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
} else {
    // Return an error for unsupported request methods
    echo json_encode([
        "status" => "error",
        "message" => "Unsupported request method."
    ]);
}
?>
