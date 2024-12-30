<?php

class DatabaseHelper
{
    public static $connection = null;

    public static function createConnection($connectionString, $username, $password)
    {

        // If null, initialize a new connection
        if (self::$connection == null) {

            self::$connection = new PDO($connectionString, $username, $password);

            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return self::$connection;

        } 
        // If a connection is established, return it
        elseif (self::$connection !== null) {
            return self::$connection;
        }

    }
}

?>