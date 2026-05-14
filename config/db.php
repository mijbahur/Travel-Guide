<?php

function getConnection()
{
    $host = "localhost";
    $dbname = "travel_guide";
    $username = "root";
    $password = "";

    try {
        $con = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );

        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $con;

    } catch (PDOException $e) {
        die("Database connection failed.");
    }
}