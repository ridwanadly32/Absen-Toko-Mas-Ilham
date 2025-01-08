<?php
$host = 'localhost'; // Ganti dengan host Anda
$user = 'root';      // Ganti dengan username database Anda
$password = '';      // Ganti dengan password database Anda
$dbname = 'toko_mas_ilham';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
