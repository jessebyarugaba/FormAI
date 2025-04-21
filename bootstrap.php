<?php
// bootstrap.php

// 1. Load Composer's Autoloader
require_once __DIR__ . '/vendor/autoload.php';

// 2. Load .env file variables into $_ENV
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    // Optional: You can validate required variables here
    // $dotenv->required(['OPENROUTER_API_KEY', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'])->notEmpty();
} catch (\Throwable $e) {
    error_log("Error loading .env file: " . $e->getMessage());
    die("Configuration error. Please check server logs.");
}

// 3. Set up Database Connection (Move logic from db_connect.php here)
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_NAME'] ?? '';
$dbUser = $_ENV['DB_USER'] ?? '';
$dbPass = $_ENV['DB_PASS'] ?? '';
$dbCharset = 'utf8mb4';

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$dbCharset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (\PDOException $e) {
     error_log("Database Connection Error: " . $e->getMessage());
     die("Sorry, a database connection error occurred.");
}

// The $pdo object is now available to scripts including this file.
?>