<?php
$host = 'localhost';
$db_name = 'php_project';
$username = 'postgres';
$password = 'atazhanov99M';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}