<?php

require_once "/Users/user/OneDrive/Desktop/XAMP/htdocs/SharingPlatform/Database/DatabaseAccess.php";

class CategoryController
{

    private $databaseAccess;
    private $requestMethod;
    private $categoryId;

    public function __construct($requestMethod, $categoryId)
    {
        $this->databaseAccess = new DatabaseAccess();
        $this->requestMethod = $requestMethod;
        $this->categoryId = $categoryId; 
    }

    public function processRequest()
    {

        switch ($this->requestMethod) {
            case 'GET':
                if ($this->categoryId) {
                    $response = $this->getCategory($this->categoryId);
                } else {
                    $response = $this->getAllCategories();
                };
                break;
            case 'POST':
                $response = $this->createCategory();
                break;
            case 'PUT':
                if(!$this->categoryId){
                    $this->badRequestResponse();
                }
                $response = $this->updateUserFromRequest($this->categoryId);
                break;
            case 'DELETE':
                if(!$this->categoryId){
                    $this->badRequestResponse();
                }
                $response = $this->deleteCategory($this->categoryId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        // Response Header + Body
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    // Done
    private function getAllCategories()
    {
        $result = $this->databaseAccess->getAllCategories();
        return $this->successfullResponse($result);
    }

    // Done
    private function getCategory($id)
    {
        $result = $this->databaseAccess->getCategory($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        return $this->successfullResponse($result);
    }

    // Done
    private function createCategory()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateCategory($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->databaseAccess->addCategory($input);
        return $this->createdResponse();

    }

    // Done
    private function updateUserFromRequest($id)
    {
        $result = $this->databaseAccess->getCategory($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateCategory($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->databaseAccess->updateCategory($id, $input);
        return $this->successfullResponse(null);
    }

    // Done
    private function deleteCategory($id)
    {
        $result = $this->databaseAccess->getCategory($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->databaseAccess->deleteCategory($id);
        return $this->successfullResponse(null);
    }

    // Response Functions

    private function successfullResponse($result)
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createdResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode([
            'message' => 'Category Created Successfully'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode([
            'error' => 'Category Not Found'
        ]);
        return $response;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid Input'
        ]);
        return $response;
    }

    function badRequestResponse()
    {
        header('HTTP/1.1 400 Bad Request');
        $response = [
            "error" => "Missing Id"
        ];
        echo json_encode($response);
        exit();
    }

    // Validate Input Function

    private function validateCategory($input)
    {
        if (! isset($input['Name'])) {
            return false;
        }
        return true;
    }

}

?>