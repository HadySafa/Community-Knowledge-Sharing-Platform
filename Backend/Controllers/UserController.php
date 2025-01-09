<?php

require_once "/Users/user/OneDrive/Desktop/XAMP/htdocs/SharingPlatform/Backend/Database/DatabaseAccess.php";

    class UserController
    {
        private $databaseAccess;
        private $requestMethod;
        private $userId;

        public function __construct($requestMethod, $userId)
        {
            $this->databaseAccess = new DatabaseAccess();
            $this->requestMethod = $requestMethod;
            $this->userId = $userId;
            
        }

        public function processRequest()
        {

            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->userId) {
                        $response = $this->getUser($this->userId);
                    } else {
                        $response = $this->getAllUsers();
                    };
                    break;
                case 'POST':
                    $response = $this->createUserFromRequest();
                    break;
                case 'PUT':
                    if(!$this->userId){
                        $this->badRequestResponse();
                    }
                    $response = $this->updateUserFromRequest($this->userId);
                    break;
                case 'DELETE':
                    if(!$this->userId){
                        $this->badRequestResponse();
                    }
                    $response = $this->deleteUser($this->userId);
                    break;
                default:
                    $response = $this->notFoundResponse();
                    break;
            }

            // response header + body
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
        }

        // Functions implementation

        // Done
        private function getAllUsers()
        {
            $result = $this->databaseAccess->getAllUsers();
            return $this->successfullResponse($result);
        }

        // Done
        private function getUser($id)
        {
            $result = $this->databaseAccess->getUser($id);
            if (!$result) {
                return $this->notFoundResponse();
            }
            return $this->successfullResponse($result);
        }

        // Done
        private function createUserFromRequest()
        {
            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateUser($input)) {
                return $this->unprocessableEntityResponse();
            }
            $this->databaseAccess->addUser($input);
            return $this->createdResponse();
        }

        // Done
        private function updateUserFromRequest($id)
        {
            $result = $this->databaseAccess->getUser($id);
            if (!$result) {
                return $this->notFoundResponse();
            }

            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateUser($input)) {
                return $this->unprocessableEntityResponse();
            }

            $this->databaseAccess->updateUser($id, $input);
            return $this->successfullResponse(null);
        }

        private function deleteUser($id)
        {
            $result = $this->databaseAccess->getUser($id);
            if (! $result) {
                return $this->notFoundResponse();
            }
            $this->databaseAccess->deleteUser($id);
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
                'message' => 'User Created Successfully'
            ]);
            return $response;
        }

        private function notFoundResponse()
        {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode([
                'error' => 'User Not Found'
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

        private function validateUser($input)
        {
            if (! isset($input['FullName'])) {
                return false;
            }
            if (! isset($input['PhoneNumber'])) {
                return false;
            }
            if (! isset($input['Username'])) {
                return false;
            }
            if (! isset($input['Password'])) {
                return false;
            }
            if (! isset($input['Role'])) {
                return false;
            }
            return true;
        }

    }

?>
