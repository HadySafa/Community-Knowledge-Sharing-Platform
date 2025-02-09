<?php

require_once "DatabaseInfo.php";
require_once "DatabaseHelper.php";

class DatabaseAccess
{

    public $connection;

    public function __construct()
    {
        $this->connection = DatabaseHelper::createConnection(connectionString, username, password);
    }

    // users

    // get all users
    public function getAllUsers()
    {
        $query = "SELECT Id, FullName, PhoneNumber, Username, Role FROM Users";

        try {
            $result = $this->connection->query($query);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get user by id
    public function getUser($id)
    {
        $query = "SELECT Id, FullName, PhoneNumber, Username, Role FROM Users WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // delete user
    public function deleteUser($id)
    {
        $query = "DELETE FROM Users WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // update user
    public function updateUser($id, $info)
    {
        $query = "UPDATE Users SET FullName = ?, PhoneNumber = ? WHERE id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["FullName"], $info["PhoneNumber"], $id]);
            return $this->getUser($id);
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // update user password
    public function updateUserPassword($id, $info)
    {
        $query = "UPDATE Users SET Password = ? WHERE id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$this->hashPassword($info["Password"]), $id]);
            return $this->getUser($id);
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // add user
    public function addUser($info)
    {
        $query = "INSERT INTO Users (FullName, PhoneNumber, Username, Password ,Role) VALUES (?, ?, ?, ?, ?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["FullName"], $info["PhoneNumber"], $info["Username"], $this->hashPassword($info["Password"]), $info["Role"]]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    public function hashPassword($password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashedPassword;
    }

    // get a user by username (for login verification)
    public function getUserByUsername($username)
    {
        $query = "SELECT * FROM Users WHERE Username = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$username]);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // categories

    // get all categories
    public function getAllCategories()
    {

        $query = "SELECT * FROM Categories";

        try {
            $result = $this->connection->query($query);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get category by id
    public function getCategory($id)
    {

        $query = "SELECT * FROM Categories WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // delete category
    public function deleteCategory($id)
    {

        $query = "DELETE FROM Categories WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // add category
    public function addCategory($info)
    {

        $query = "INSERT INTO Categories (Name) VALUES (?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["Name"]]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // update category
    public function updateCategory($id, $info)
    {
        $query = "UPDATE Categories SET Name = ? WHERE id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["Name"], $id]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // posts

    // get all posts
    public function getAllPosts()
    {
        $query = "SELECT * 
                  FROM (SELECT Id AS CategoryId,Name AS CategoryName FROM Categories) AS Categories 
                  NATURAL JOIN Posts 
                  NATURAL JOIN (SELECT Id AS UserId,Username FROM Users) AS Users";

        try {
            $result = $this->connection->query($query);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get posts of a specific user
    public function getPostsOfUser($userId)
    {
        $query = "SELECT * 
                  FROM (SELECT Id AS CategoryId,Name AS CategoryName FROM Categories) AS Categories 
                  NATURAL JOIN Posts 
                  NATURAL JOIN (SELECT Id AS UserId,Username FROM Users) AS Users
                  WHERE UserId = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$userId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get posts based on search parameter
    public function getPostsBySearch($givenSearchParameter)
    {
        $searchParameter = "%" . $givenSearchParameter . "%";

        $query = "SELECT * 
                  FROM (SELECT Id AS TagId, PostId AS Id,Name FROM Tags) AS Tags 
                  NATURAL JOIN  Posts 
                  NATURAL JOIN (SELECT Id AS UserId,Username FROM Users) AS Users
                  NATURAL JOIN (SELECT Id AS CategoryId,Name AS CategoryName FROM Categories) AS Categories
                  WHERE Name LIKE ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$searchParameter]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get liked posts in decreasing order (top->least)
    public function getTopPosts()
    {
        $query = "SELECT * FROM posts 
                  NATURAL JOIN (SELECT Id AS UserId,Username FROM users) AS Users 
                  NATURAL JOIN (SELECT Id AS CategoryId, Name AS CategoryName FROM categories) AS categories 
                  NATURAL JOIN (SELECT PostId AS Id, COUNT(*) AS LikeCount FROM reactions WHERE Reaction = 'Like' GROUP BY PostId) AS Reactions 
                  ORDER BY LikeCount DESC";
        try {
            $result = $this->connection->query($query);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get post by id
    public function getPostById($postId)
    {

        $query = "SELECT * FROM
                  Posts 
                  WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$postId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get posts based on category id
    public function getPostByCategoryId($categoryId)
    {

        $query = "SELECT * 
        FROM (SELECT Id AS CategoryId,Name AS CategoryName FROM Categories) AS Categories 
        NATURAL JOIN Posts 
        NATURAL JOIN (SELECT Id AS UserId,Username FROM Users) AS Users
        WHERE CategoryId = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$categoryId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // delete post
    public function deletePost($id)
    {

        $query = "DELETE FROM Posts WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // add post
    public function addPost($info)
    {

        $query = "INSERT INTO Posts (UserId, Title, Description, Link, CodeSnippet, CategoryId) VALUES (?,?,?,?,?,?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["UserId"], $info["Title"], $info["Description"], $info["Link"], $info["CodeSnippet"], $info["CategoryId"]]);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // edit a post
    public function editPost($id, $info)
    {
        $query = "UPDATE Posts SET Title = ?,Description = ?,Link = ?,CodeSnippet = ? WHERE id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["Title"], $info["Description"], $info["Link"], $info["CodeSnippet"], $id]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // comments

    // get comments of a specific post
    public function getCommentsOfPost($postId)
    {
        $query = "SELECT * FROM Comments 
                  NATURAL JOIN (SELECT Id as UserId,Username from Users) as Users 
                  Where PostId = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$postId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // add comment
    public function addComment($info)
    {
        $query = "INSERT INTO Comments (Comment,PostId,UserId) VALUES (?,?,?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["Comment"], $info["PostId"], $info["UserId"]]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }


    // reactions

    // get reactions of a post
    public function getReactionsOfPost($postId)
    {
        $query = "SELECT * FROM Reactions 
                  NATURAL JOIN (SELECT Id as UserId,Username from Users) as Users 
                  Where PostId=?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$postId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // make a reaction on a post 
    public function makeReaction($info)
    {
        $query = "INSERT INTO Reactions (Reaction,PostId,UserId) VALUES (?,?,?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["Reaction"], $info["PostId"], $info["UserId"]]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get a reaction on a post made by a specific user
    public function getReaction($UserId, $PostId)
    {

        $query = "SELECT * FROM Reactions WHERE (UserId = ? and PostId = ?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$UserId, $PostId]);
            return $result->rowCount();
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // remove a reaction on a post 
    public function removeReaction($UserId, $PostId)
    {
        $query = "DELETE FROM Reactions WHERE (UserId = ? and PostId = ?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$UserId, $PostId]);
            return $result;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }


    // tags

    // get tags of a post
    public function getTagsOfPost($postId)
    {
        $query = "SELECT * FROM Tags 
                  Where PostId = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$postId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // remove a tag by id 
    public function removeTag($id)
    {
        $query = "Delete FROM Tags Where Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }


    // add tag on a post
    public function addTag($info)
    {
        $query = "INSERT INTO Tags (PostId,Name) VALUES (?,?)";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$info["PostId"], $info["Name"]]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }

    // get tag by Id
    public function getTagById($id)
    {
        $query = "SELECT * FROM Tags WHERE Id = ?";

        try {
            $result = $this->connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            die("Error occcured: " . $e->getMessage());
        }
    }
    
}
