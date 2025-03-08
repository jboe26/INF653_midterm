<?php

include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../classes/Author.php';


class AuthorController {
    private $db;
    private $author;

    public function __construct($db) {
        $this->db = $db;
        $this->author = new Author($db);
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
            $this->author->id = $params['id'];
            $this->author->readOne();
            if ($this->author->author != null) {
                $author_arr = array(
                    "id" => $this->author->id,
                    "author" => $this->author->author
                );
                http_response_code(200);
                echo json_encode($author_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Author Not Found."));
            }
        } else {
            $stmt = $this->author->read();
            $num = $stmt->rowCount();
    
            if ($num > 0) {
                $authors_arr = array();
                $authors_arr["data"] = array();
    
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $author_item = array(
                        "id" => $id,
                        "author" => $author
                    );
                    array_push($authors_arr["data"], $author_item);
                }
                http_response_code(200);
                echo json_encode($authors_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No Authors Found."));
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!empty($data->author)) {
            $this->author->author = $data->author;
    
            if ($this->author->create()) {
                http_response_code(201); // Created
                echo json_encode(array("message" => "Author was created."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to create author."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to create author. Data is incomplete."));
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!empty($data->id) && !empty($data->author)) {
            $this->author->id = $data->id;
            $this->author->author = $data->author;
    
            if ($this->author->update()) {
                http_response_code(200); // OK
                echo json_encode(array("message" => "Author was updated."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to update author."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to update author. Data is incomplete."));
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!empty($data->id)) {
            $this->author->id = $data->id;
    
            if ($this->author->delete()) {
                http_response_code(200); // OK
                echo json_encode(array("message" => "Author was deleted."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to delete author."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to delete author. Data is incomplete."));
        }
    }
}
?>