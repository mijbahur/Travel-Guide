<?php

	// PDO database configuration for Task 4 MVC
	// Use 127.0.0.1 (TCP) instead of 'localhost' (socket) to avoid socket/auth issues
	$host = '127.0.0.1';
	$dbname = 'travel_guide';
	$username = 'root';
	$password = '';

	function getConnection()
	{
		global $host, $dbname, $username, $password;

		$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

		try {
			$pdo = new PDO($dsn, $username, $password, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			]);
			return $pdo;
		} catch (PDOException $e) {
			echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
			exit;
		}
	}
