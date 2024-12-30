<?php

require_once "/Users/user/OneDrive/Desktop/XAMP/htdocs/SharingPlatform/Database/DatabaseAccess.php";

class PostController
{

    private $databaseAccess;
    private $requestMethod;
    private $id1;
    private $id2;
    private $option;

    public function __construct($requestMethod, $id1,$option,$id2)
    {
        $this->databaseAccess = new DatabaseAccess();
        $this->requestMethod = $requestMethod;
        $this->id1 = $id1;
        $this->id2 = $id2;
        $this->option = $option;
    }

    public function processRequest()
        {

            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->id1) {
                        $response = $this->getPostsOfUser($this->id1);
                    } 
                    elseif($this->option == "Comments"){
                        $response = $this->getCommentsOfPost($this->id2);
                    }
                    elseif($this->option == "Reactions"){
                        $response = $this->getReactionsOfPost($this->id2);
                    }
                    elseif($this->option == "Tags"){
                        $response = $this->getTagsOfPost($this->id2);
                    }
                    else {
                        $response = $this->getAllPosts();
                    };
                    break;
                case 'POST':
                    if($this->id1) $response = $this->addPost();
                    elseif($this->option == "Comments") $response = $this->addComment();
                    elseif($this->option == "Reactions") $response = $this->addReaction();
                    elseif($this->option == "Tags") $response = $this->addTag();
                    break;
                case 'PUT':
                    $response = $this->updatePost($this->id1);
                    break;
                case 'DELETE':
                    if($this->id1) $response = $this->deletePost($this->id1);
                    elseif($this->option == "Tags") $response = $this->removeTag($this->id1);
                    break;
                default:
                    $response = $this->notFoundResponse("Request Method Doesn't Match");
                    break;
            }

            // response header + body
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
        }

        // Functions implementation => Posts

        // Done
        private function getAllPosts()
        {
            $result = $this->databaseAccess->getAllPosts();
            return $this->successfullResponse($result);
        }

        // Done
        private function getPostsOfUser($id)
        {
            $result = $this->databaseAccess->getUser($id);
            if (!$result) {
                return $this->notFoundResponse("User Not Found");
            }
            $result = $this->databaseAccess->getPostsOfUser($id);
            return $this->successfullResponse($result);
        }

        // Done
        private function addPost()
        {
            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateAddingPost($input)) {
                return $this->unprocessableEntityResponse();
            }
            $this->databaseAccess->addPost($input);
            return $this->createdResponse();
        }

        // Done
        private function updatePost($id)
        {
            $result = $this->databaseAccess->getPostById($id);
            if (!$result) {
                return $this->notFoundResponse("Post Not Found");
            }

            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateEditingPost($input)) {
                return $this->unprocessableEntityResponse();
            }

            $this->databaseAccess->editPost($id, $input);
            return $this->successfullResponse(null);
        }

        private function deletePost($id)
        {
            $result = $this->databaseAccess->getPostById($id);
            if (! $result) {
                return $this->notFoundResponse("Post Not Found");
            }
            $this->databaseAccess->deletePost($id);
            return $this->successfullResponse(null);
        }

        // Functions implementation => Comments

        private function getCommentsOfPost($postId){
            $result = $this->databaseAccess->getPostById($postId);
            if (!$result) {
                return $this->notFoundResponse("Post Not Found");
            }
            $result = $this->databaseAccess->getCommentsOfPost($postId);
            return $this->successfullResponse($result);
        }

        private function addComment(){
            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateComment($input)) {
                return $this->unprocessableEntityResponse();
            }
            $this->databaseAccess->addComment($input);
            return $this->createdResponse();
        }

        // Functions implementation => Reactions

        private function getReactionsOfPost($postId){
            $result = $this->databaseAccess->getPostById($postId);
            if (!$result) {
                return $this->notFoundResponse("Post Not Found");
            }
            $result = $this->databaseAccess->getReactionsOfPost($postId);
            return $this->successfullResponse($result);
        }

        private function addReaction(){
            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateReaction($input)) {
                return $this->unprocessableEntityResponse();
            }
            $this->databaseAccess->makeReaction($input);
            return $this->createdResponse();
        }

        // Functions implementation => Tags

        private function getTagsOfPost($postId){
            $result = $this->databaseAccess->getPostById($postId);
            if (!$result) {
                return $this->notFoundResponse("Post Not Found");
            }
            $result = $this->databaseAccess->getTagsOfPost($postId);
            return $this->successfullResponse($result);
        }

        private function addTag(){
            $input = json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateTag($input)) {
                return $this->unprocessableEntityResponse();
            }
            $this->databaseAccess->addTag($input);
            return $this->createdResponse();
        }

        private function removeTag($id){
            $result = $this->databaseAccess->getTagById($id);
            if (!$result) {
                return $this->notFoundResponse("Tag Not Found");
            }
            $this->databaseAccess->removeTag($id);
            return $this->createdResponse();
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
                'message' => 'Post Created Successfully'
            ]);
            return $response;
        }

        private function notFoundResponse($message)
        {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode([
                'error' => $message
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

        private function validateAddingPost($input)
        {
            if (! isset($input['UserId'])) {
                return false;
            }
            if (! isset($input['Title'])) {
                return false;
            }
            if (! isset($input['Description'])) {
                return false;
            }
            if (! isset($input['CategoryId'])) {
                return false;
            }
            // code snippet and link can be null
            return true;
        }

        private function validateEditingPost($input)
        {
            if (! isset($input['Title'])) {
                return false;
            }
            if (! isset($input['Description'])) {
                return false;
            }
            if (! isset($input['CategoryId'])) {
                return false;
            }
            // code snippet and link can be null
            return true;
        }

        private function validateComment($input)
        {
            if (! isset($input['Comment'])) {
                return false;
            }
            if (! isset($input['PostId'])) {
                return false;
            }
            if (! isset($input['UserId'])) {
                return false;
            }
            return true;
        }

        private function validateReaction($input)
        {
            if (! isset($input['Reaction'])) {
                return false;
            }
            if (! isset($input['PostId'])) {
                return false;
            }
            if (! isset($input['UserId'])) {
                return false;
            }
            return true;
        }

        private function validateTag($input)
        {
            if (! isset($input['Name'])) {
                return false;
            }
            if (! isset($input['PostId'])) {
                return false;
            }
            return true;
        }
    
}

?>