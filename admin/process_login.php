<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Boş alan kontrolü
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        // Kullanıcıyı kullanıcı adı ve şifre ile bul
        $query = "SELECT id, username, role FROM users WHERE username = :username AND password = :password";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Admin rolü kontrolü
            if ($row['role'] != 'admin') {
                header("Location: login.php?error=not_admin");
                exit();
            }

            // Oturum başlat
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=invalid");
            exit();
        }
    } catch(PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
} else {
    header("Location: login.php");
    exit();
}
?> 