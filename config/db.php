<?php
$host = 'localhost';
$dbname = 'kutuphane_db';
$user = 'root'; 
$pass = 'şifre'; // DİKKAT: MySQL Workbench şifreni buraya yazmayı unutma!

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>