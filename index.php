<?php
// Include the necessary files
include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../classes/Quote.php';

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

// Initialize the QuoteController with the database connection
$quoteController = new QuoteController($conn);

// Handle requests (e.g., GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch quotes from the database
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
