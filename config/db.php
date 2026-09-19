<?php
// Configuration for MySQL database connection in Docker
$db_host = 'db';
$db_user = 'sqli_user';
$db_pass = 'sqli_password';
$db_name = 'sqli_lab';

// Create database connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Force UTF-8 character encoding to prevent font corruption
mysqli_set_charset($conn, "utf8mb4");
mysqli_query($conn, "SET NAMES utf8mb4");
?>
