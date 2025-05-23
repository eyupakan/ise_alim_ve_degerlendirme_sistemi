<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

echo "<pre>\n";
echo "Veritabanına bağlanılıyor...\n";

// Veritabanı bağlantısı
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Veritabanı bağlantısı başarısız!\n");
}

echo "Veritabanı bağlantısı başarılı.\n\n";

try {
    // Önce positions tablosunu kontrol et
    $checkQuery = "SHOW TABLES LIKE 'positions'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "\nDİKKAT: 'positions' tablosu bulunamadı!\n";
        echo "Veritabanı şeması düzgün yüklenmemiş olabilir.\n";
        echo "database.sql dosyasını çalıştırmanız gerekiyor.\n";
        die("</pre>");
    }
    
    // Tüm pozisyonları getir
    $query = "SELECT id, title, status, created_at FROM positions";
    echo "Sorgu çalıştırılıyor: " . $query . "\n\n";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Pozisyonlar:\n\n";
    if (count($positions) > 0) {
        foreach ($positions as $position) {
            echo "ID: " . $position['id'] . "\n";
            echo "Başlık: " . $position['title'] . "\n";
            echo "Durum: " . $position['status'] . "\n";
            echo "Oluşturulma: " . $position['created_at'] . "\n";
            echo "------------------------\n";
        }
    } else {
        echo "Hiç pozisyon bulunamadı! Veritabanı boş.\n\n";
        
        // Tablo yapısını kontrol et
        $descQuery = "DESCRIBE positions";
        $stmt = $db->prepare($descQuery);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Tablo yapısı:\n";
        foreach ($columns as $column) {
            echo $column['Field'] . " - " . $column['Type'] . 
                 ($column['Null'] === 'NO' ? ' (NOT NULL)' : '') . 
                 ($column['Default'] ? " (Default: {$column['Default']})" : '') . "\n";
        }
    }
} catch(PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage() . "\n";
    echo "Hata kodu: " . $e->getCode() . "\n";
}
echo "</pre>";
?> 