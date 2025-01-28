<?php

require_once "/Users/user/OneDrive/Desktop/XAMP/htdocs/SharingPlatform/Backend/Database/DatabaseAccess.php";
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;


class UserController
{
    private $databaseAccess;
    private $requestMethod;
    private $userId;
    private $login;
    private $password;
    private $username;
    private $checkUsername;

    public function __construct($requestMethod, $userId, $option2,$id2,$username)
    {
        $this->databaseAccess = new DatabaseAccess();
        $this->requestMethod = $requestMethod;
        $this->userId = $userId ? $userId : null;
        $this->userId = $id2 ? $id2 : null;
        $this->login = $option2 == "Login" ? true : false;
        $this->password = $option2 == "Password" ? true : false;
        $this->checkUsername = $option2 == "CheckUser" ? true : false;
        $this->username = $username;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if($this->checkUsername){
                    $response = $this->checkUser($this->username);
                }
                elseif ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                };
                break;
            case 'POST':
                if ($this->login) $response = $this->verifyLogin();
                else $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                if (!$this->userId) {
                    $this->badRequestResponse();
                }
                if($this->password) $response = $this->updateUserPassword($this->userId);
                else $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                if (!$this->userId) {
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
        $userUpdatedInfo = $this->databaseAccess->updateUser($id, $input);
        return $this->successfullUpdate($userUpdatedInfo[0]);
    }

    private function updateUserPassword($id)
    {
        $result = $this->databaseAccess->getUser($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validatePassword($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->databaseAccess->updateUserPassword($id, $input);
        return $this->successfullPasswordUpdate();
    }

    private function verifyLogin() {
        $input = json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateUserCredentials($input)) {
            return $this->unprocessableEntityResponse();
        }
        $username = $input["Username"];
        $result = $this->databaseAccess->getUserByUsername($username);  
        if (!$result) {
            return $this->notFoundResponse();
        }
        $password = $input["Password"];
        if(password_verify($password, $result["Password"])){
            return $this->successfullLogin($result);
        }
        
        return $this->notFoundResponse(null);
    }

    private function checkUser($username){
        echo "In check user";
        $result = $this->databaseAccess->getUserByUsername($username);
        if (!$result) {
            return $this->notFoundResponse();
        }
        return $this->userSuccessfullResponse();
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

    private function userSuccessfullResponse()
    {
        echo "User successfull response";
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(["message"=>"user found"]);
        return $response;
    }

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

    private function successfullLogin($userInfo){
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => 'Successfull Login',
            'token' => $this->createJWT($userInfo)
        ]);
        return $response;
    }

    private function successfullUpdate($userInfo){
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => 'Successfull Update',
            'token' => $this->createJWT($userInfo)
        ]);
        return $response;
    }

    private function successfullPasswordUpdate(){
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => 'Successfull Password Update'
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

    // Validate Input Functions

    private function validateUser($input)
    {
        if (! isset($input['FullName'])) {
            return false;
        }
        if (! isset($input['PhoneNumber'])) {
            return false;
        }
        return true;
    }

    private function validatePassword($input)
    {
        if (! isset($input['Password'])) {
            return false;
        }
        return true;
    }

    private function validateUserCredentials($input){
        if (! isset($input['Username'])) {
            return false;
        }
        if (! isset($input['Password'])) {
            return false;
        }
        return true;
    }


    // Token-Related Functions

    function createJWT($userInfo) {

        $key = 'fS3&nP8oH!r9ZxD1m2W$QpVj8uX7fA6iL@tK5gT#Yb';
        $algorithm = 'HS256';
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour from the issued time
        $payload = [
            'Id' => $userInfo["Id"],
            'Username' => $userInfo["Username"],
            'PhoneNumber' => $userInfo["PhoneNumber"],
            'FullName' => $userInfo["FullName"],
            'Role' => $userInfo["Role"],
            'iat' => $issuedAt,   // issued at time
            'exp' => $expirationTime  // expiration time
        ];
         
        return JWT::encode($payload, $key, $algorithm);
    }
    
}
