<?php
// Database configuration and PDO connection
// All database operations should use prepared statements

$host = getenv('DB_HOST') ?: 'db';
$db = getenv('DB_NAME') ?: 'oarnote';
$user = getenv('DB_USER') ?: 'oaruser';
$pass = getenv('DB_PASS') ?: 'oarpw';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed. Please contact support.");
}
