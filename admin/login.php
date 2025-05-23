<?php
session_start();
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Admin Girişi</h2>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    switch($_GET['error']) {
                        case 'invalid':
                            echo "Geçersiz kullanıcı adı veya şifre!";
                            break;
                        case 'empty':
                            echo "Lütfen tüm alanları doldurun!";
                            break;
                        case 'not_admin':
                            echo "Bu hesap admin yetkisine sahip değil!";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="username" name="username" value="admin" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" value="admin" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
            </form>
        </div>
    </div>
</body>
</html> 