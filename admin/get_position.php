<?php
// Hata raporlamayı devre dışı bırak
error_reporting(0);
ini_set('display_errors', 0);

require_once 'auth_check.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz pozisyon ID.']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Pozisyon detaylarını getir
    $query = "SELECT p.*, GROUP_CONCAT(t.id) as test_ids
              FROM positions p
              LEFT JOIN position_tests pt ON p.id = pt.position_id
              LEFT JOIN tests t ON pt.test_id = t.id
              WHERE p.id = :id
              GROUP BY p.id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $position = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$position) {
        throw new Exception('Pozisyon bulunamadı.');
    }

    // Yanıtı hazırla
    echo json_encode([
        'id' => $position['id'],
        'title' => $position['title'],
        'description' => $position['description'],
        'requirements' => $position['requirements'],
        'status' => $position['status'],
        'test_ids' => $position['test_ids'] ? explode(',', $position['test_ids']) : []
    ]);

} catch (Exception $e) {
    error_log('Error in get_position.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 