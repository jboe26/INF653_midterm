<?php
// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Display a simple HTML homepage
echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Quotes API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #A9A9A9;
            color: white;
            padding: 20px 0;
        }
        main {
            padding: 20px;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Quotes API</h1>
    </header>
    <main>
        <p>Welcome to the Quotes API! Use this API to manage quotes, authors, and categories.</p>
        <p>Available Endpoints:</p>
        <ul>
            <p><a href='/api/quotes/'>/api/quotes</a> - Get all quotes</p>
            <p><a href='/api/authors/'>/api/authors</a> - Get all authors</p>
            <p><a href='/api/categories/'>/api/categories</a> - Get all categories</p>
        </ul>
        <p>For more details, check the <a href='https://github.com/jboe26/INF653_midterm'>GitHub Repository</a>.</p>
    </main>
</body>
</html>
";
?>
