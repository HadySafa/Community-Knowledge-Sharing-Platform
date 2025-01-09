<?php

require_once "./Backend/Controllers/CategoryController.php";
require_once "./Backend/Controllers/PostController.php";
require_once "./Backend/Controllers/UserController.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$option1 = null; // must be (Users or Categories or Posts)
$option2 = null; // must be (Comments or Reactions or Tags) or Id
$id1 = null;
$id2 = null;

// get the path of the URL
$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = explode("/", $uri);

// test first parameter
if (isset($uri[3])) {

    // assign option1 if it is (Users or Categories or Posts), otherwise stop and return an error
    if ($uri[3] != "Users" && $uri[3] != "Posts" && $uri[3] != "Categories") {
        badRequestResponse();
    }
    $option1 = $uri[3];
}

// test second and third parameter
if (isset($uri[4])) {

    // if not an id neither option2, stop and return false
    if ($uri[4] != "Comments" && $uri[4] != "Reactions" && $uri[4] != "Tags" && !is_numeric($uri[4]) ) {
        badRequestResponse();
    }

    // assign id if the entered parameter is id, otherwise check if it is option2
    if (is_numeric($uri[4])) {
        $id1 = $uri[4];
    }

    // assign option2 if it is (Comments or Reactions or Tags)
    else {
        if($uri[3] != "Posts"){
            badRequestResponse();
        }
        $option2 = $uri[4];

        // assign id2 if passed
        if (isset($uri[5])) {
            if (!is_numeric($uri[5])) {
                badRequestResponse();
            }
            $id2 = $uri[5];
        }
    }
}

if ($option1 == "Users") {
    $userController = new UserController($requestMethod, $id1);
    $userController->processRequest();
} elseif ($option1 == "Posts") {
    $postController = new PostController($requestMethod, $id1, $option2, $id2);
    $postController->processRequest();
}  elseif ($option1 == "Categories") {
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
