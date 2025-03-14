<?php

include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../classes/Category.php';

class CategoryController {
    private $db;
    private $category;

    public function __construct($db) {
        $this->db = $db;
        $this->category = new Category($db);
    }

    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                $this->handleGet($params);
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['message' => 'Method not allowed.']);
        }
    }

    private function handleGet($params) {
        if (isset($params['id'])) { // Fetch a single category by ID
            if (!is_numeric($params['id'])) { // Validate ID
                http_response_code(400); // Bad Request
                echo json_encode(["message" => "Invalid or missing id parameter."]);
                return;
            }

            $this->category->id = htmlspecialchars(strip_tags($params['id']));
            $result = $this->category->readOne();

            if ($result) {
                http_response_code(200); // OK
                echo json_encode([
                    "id" => $this->category->id,
                    "category" => $this->category->category
                ]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "Category Not Found."]);
            }
        } else { // Fetch all categories
            $stmt = $this->category->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $categories_arr = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $categories_arr[] = [
                        "id" => $row['id'],
                        "category" => $row['category']
                    ];
                }
                http_response_code(200); // OK
                echo json_encode($categories_arr);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "No Categories Found."]);
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->category)) { // Validate input
            $this->category->category = htmlspecialchars(strip_tags($data->category));

            if ($this->category->create()) {
                http_response_code(201); // Created
                echo json_encode(["message" => "Category was created."]);
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to create category."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to create category. Data is incomplete."]);
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->category)) { // Validate input
            if (!is_numeric($data->id)) { // Validate ID
                http_response_code(400); // Bad Request
                echo json_encode(["message" => "Invalid id parameter."]);
                return;
            }

            $this->category->id = htmlspecialchars(strip_tags($data->id));
            $this->category->category = htmlspecialchars(strip_tags($data->category));

            if ($this->category->update()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Category was updated."]);
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to update category."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to update category. Data is incomplete."]);
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) { // Validate input
            if (!is_numeric($data->id)) { // Validate ID
                http_response_code(400); // Bad Request
                echo json_encode(["message" => "Invalid id parameter."]);
                return;
            }

            $this->category->id = htmlspecialchars(strip_tags($data->id));

            if ($this->category->delete()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Category was deleted."]);
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to delete category."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to delete category. Data is incomplete."]);
        }
    }
}
?>
