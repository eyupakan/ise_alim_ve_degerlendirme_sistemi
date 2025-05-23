<?php
require_once 'auth_check.php';
require_once '../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// POST method kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Action kontrolü
if (!isset($_POST['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No action specified']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    switch ($_POST['action']) {
        case 'create':
            // Gerekli alanların kontrolü
            $required_fields = ['title', 'time_limit', 'passing_score', 'status'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("$field alanı gereklidir.");
                }
            }

            // Test oluştur
            $query = "INSERT INTO tests (title, description, time_limit, passing_score, status) 
                     VALUES (:title, :description, :time_limit, :passing_score, :status)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $_POST['title']);
            $stmt->bindParam(':description', $_POST['description']);
            $stmt->bindParam(':time_limit', $_POST['time_limit']);
            $stmt->bindParam(':passing_score', $_POST['passing_score']);
            $stmt->bindParam(':status', $_POST['status']);
            
            if ($stmt->execute()) {
                $test_id = $db->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'Test başarıyla oluşturuldu.',
                    'test_id' => $test_id
                ]);
            } else {
                throw new Exception('Test oluşturulurken bir hata oluştu.');
            }
            break;

        case 'delete':
            // ID kontrolü
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
                throw new Exception('Geçersiz test ID.');
            }

            // Önce test sorularını sil
            $query = "DELETE FROM test_questions WHERE test_id = :test_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':test_id', $_POST['id']);
            $stmt->execute();

            // Sonra testi sil
            $query = "DELETE FROM tests WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_POST['id']);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Test başarıyla silindi.'
                ]);
            } else {
                throw new Exception('Test silinirken bir hata oluştu.');
            }
            break;

        default:
            throw new Exception('Geçersiz işlem.');
    }
} catch (Exception $e) {
    error_log('Error in process_test.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?> 