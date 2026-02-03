<?php
session_start(); // start session for login and CSRF

$host = "localhost";
$db   = "np03cs4s250008";
$user = "np03cs4s250008";
$pass = "B9L18BCgx4";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
