<?php

require_once "./Backend/Controllers/CategoryController.php";
require_once "./Backend/Controllers/PostController.php";
require_once "./Backend/Controllers/UserController.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type,Authorization');
header("Content-Type: application/json; charset=UTF-8");

define("ALLOWED_OPTIONS1", ["Users", "Posts", "Categories"]);
define("ALLOWED_OPTIONS2", ["Comments", "Reactions", "Tags", "Password", "Login", "CheckUser", "Search", "Category", "Top"]);

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$option1 = null;
$option2 = null;
$id1 = null;
$id2 = null;
$username = null;

// get the path of the URL
$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = explode("/", $uri);


if (isset($uri[3]) && in_array($uri[3], ALLOWED_OPTIONS1)) {
    $option1 = $uri[3];
} else {
    badRequestResponse();
}

// test second and third parameter
if (isset($uri[4])) {
    if (is_numeric($uri[4])) {
        $id1 = $uri[4];
    } elseif (in_array($uri[4], ALLOWED_OPTIONS2)) {
        $option2 = $uri[4];
        if (isset($uri[5])) {
            if (is_numeric($uri[5])) {
                $id2 = $uri[5];
            } else {
                $username = $uri[5];
            }
        }
    } else {
        badRequestResponse();
    }
}

if ($option1 == "Users") {
    $userController = new UserController($requestMethod, $id1, $option2, $id2, $username);
    $userController->processRequest();
} elseif ($option1 == "Posts") {
    $postController = new PostController($requestMethod, $id1, $option2, $id2, $username);
    $postController->processRequest();
} elseif ($option1 == "Categories") {
    $categoryController = new CategoryController($requestMethod, $id1);
    $categoryController->processRequest();
}

function badRequestResponse()
{
    header('HTTP/1.1 400 Bad Request');
    $response = [
        "error" => "Incorrect Request Path, Not an Expected Input"
    ];
    echo json_encode($response);
    exit();
}

/*
function unauthorizedResponse($message)
{
    header('HTTP/1.1 401 Unauthorized');
    $response = [
        "error" => $message
    ];
    echo json_encode($response);
    exit();
}

function validateJWT($jwt) {
    try {

        $decodedToken = JWT::decode($jwt, 'fS3&nP8oH!r9ZxD1m2W$QpVj8uX7fA6iL@tK5gT#Yb');
        
        if (isset($decodedToken->exp) && $decodedToken->exp < time()) {
            unauthorizedResponse("Token has expired");
        }

        return true;

    } catch (Exception $e) {
        unauthorizedResponse("Invalid Token");
    }
}
*/
