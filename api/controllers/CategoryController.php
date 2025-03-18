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
                http_response_code(405);
                echo json_encode(['message' => 'Method not allowed']);
                exit;
        }
    }

    private function handleGet($params) {
        if (isset($params['id'])) {
            if (!is_numeric($params['id'])) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid or missing id parameter"]);
                exit;
            }

            $this->category->id = htmlspecialchars($params['id']);
            $result = $this->category->readOne();

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "id" => $this->category->id,
                    "category" => $this->category->category
                ]);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Category Not Found"]);
                exit;
            }
        } else {
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
                http_response_code(200);
                echo json_encode($categories_arr);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(["message" => "No Categories Found"]);
                exit;
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->category) && strlen($data->category) <= 255) {
            $this->category->category = htmlspecialchars(strip_tags($data->category));

            if ($this->category->create()) {
                http_response_code(201);
                echo json_encode([
                    "id" => $this->category->id,
                    "category" => $this->category->category,
                    "message" => "Category was created"
                ]);
                exit;
            } else {
                error_log("Failed to create category: " . json_encode($data));
                http_response_code(503);
                echo json_encode(["message" => "Unable to create category"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Category name is either empty or too long"]);
            exit;
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;

        if (!empty($data->id) && !empty($data->category) && strlen($data->category) <= 255) {
            if (!is_numeric($data->id)) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid id parameter"]);
                exit;
            }

            $this->category->id = htmlspecialchars($data->id);
            $this->category->category = htmlspecialchars(strip_tags($data->category));

            if ($this->category->update()) {
                http_response_code(200);
                echo json_encode([
                    "id" => $this->category->id,
                    "category" => $this->category->category,
                    "message" => "Category was updated."
                ]);
                exit;
            } else {
                error_log("Failed to update category with ID: " . $data->id);
                http_response_code(503);
                echo json_encode(["message" => "Unable to update category"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Category name is either empty or too long"]);
            exit;
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            if (!is_numeric($data->id)) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid id parameter"]);
                exit;
            }

            $this->category->id = htmlspecialchars($data->id);

            if ($this->category->delete()) {
                http_response_code(200);
                echo json_encode(["id" => $this->category->id, "message" => "Category was deleted"]);
                exit;
            } else {
                error_log("Failed to delete category with ID: " . $data->id);
                http_response_code(503);
                echo json_encode(["message" => "Unable to delete category."]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Unable to delete category. Data is incomplete"]);
            exit;
        }
    }
}
?>