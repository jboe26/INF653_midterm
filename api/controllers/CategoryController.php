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
        if (isset($params['id'])) {
            $this->category->id = $params['id'];
            $this->category->readOne();
            if ($this->category->category != null) {
                $category_arr = array(
                    "id" => $this->category->id,
                    "category" => $this->category->category
                );
                http_response_code(200);
                echo json_encode($category_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Category Not Found."));
            }
        } else {
            $stmt = $this->category->read();
            $num = $stmt->rowCount();
    
            if ($num > 0) {
                $category_arr = array();
                $category_arr["data"] = array();
    
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $category_item = array(
                        "id" => $id,
                        "category" => $category
                    );
                    array_push($category_arr["data"], $category_item);
                }
                http_response_code(200);
                echo json_encode($category_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No Categories Found."));
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!empty($data->category)) {
            $this->category->category = $data->category;
    
            if ($this->category->create()) {
                http_response_code(201); // Created
                echo json_encode(array("message" => "Category was created."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to create category."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to create category. Data is incomplete."));
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!empty($data->id) && !empty($data->category)) {
            $this->category->id = $data->id;
            $this->category->category = $data->category;
    
            if ($this->category->update()) {
                http_response_code(200); // OK
                echo json_encode(array("message" => "Category was updated."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to update category."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to update category. Data is incomplete."));
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!empty($data->id)) {
            $this->category->id = $data->id;
    
            if ($this->category->delete()) {
                http_response_code(200); // OK
                echo json_encode(array("message" => "Category was deleted."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to delete category."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to delete category. Data is incomplete."));
        }
    }








}
?>