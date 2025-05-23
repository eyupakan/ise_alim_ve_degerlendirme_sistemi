<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Veritabanını oluştur
    $conn->exec("CREATE DATABASE IF NOT EXISTS recruitment_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE recruitment_system");

    // Users tablosunu oluştur
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        role ENUM('admin', 'candidate') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Admin kullanıcısını oluştur
    $username = "admin";
    $email = "admin@example.com";
    $password = "admin"; // Şifreyi hashlemeden düz metin olarak kaydediyoruz
    $firstName = "Admin";
    $lastName = "User";
    $role = "admin";

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, role) 
                           VALUES (:username, :email, :password, :first_name, :last_name, :role)");
    
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":first_name", $firstName);
    $stmt->bindParam(":last_name", $lastName);
    $stmt->bindParam(":role", $role);
    
    $stmt->execute();

    echo "Veritabanı ve admin kullanıcısı başarıyla oluşturuldu!<br>";
    echo "Kullanıcı adı: admin<br>";
    echo "Şifre: admin";

} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?> 