<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "travel_guide";

function getConnection()
{
    try {
        global $host;
        global $username;
        global $password;
        global $dbname;
        $con = new PDO(
            "mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $con;

    } catch (PDOException $e) {

        die("Connection failed: " . $e->getMessage());

    }
}
?>