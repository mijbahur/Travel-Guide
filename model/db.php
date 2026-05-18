<?php

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'travel_guide');

function getConnection() {
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf8mb4');
    return $con;
}
