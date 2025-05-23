<?php
session_start();

// Oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Admin rolü kontrolü
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// Veritabanı bağlantısı
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Admin bilgilerini kontrol et
try {
    $query = "SELECT id, role FROM users WHERE id = :id AND username = :username AND role = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $_SESSION['admin_id']);
    $stmt->bindParam(":username", $_SESSION['admin_username']);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // Oturumu sonlandır ve çıkış yap
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch(PDOException $e) {
    // Hata durumunda oturumu sonlandır
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?> 