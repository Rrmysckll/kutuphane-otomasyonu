<?php
require_once 'config/db.php';

$username = 'admin';
$password = '123456';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';

try {
    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute(['username' => $username, 'password' => $hashed_password, 'role' => $role]);
    echo "Harika! Admin kullanıcısı başarıyla oluşturuldu. <br> Kullanıcı Adı: admin <br> Şifre: 123456";
} catch (PDOException $e) {
    echo "Bir hata oluştu veya bu kullanıcı zaten var: " . $e->getMessage();
}
?>