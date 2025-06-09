<?php
require_once '../config/database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$database = new Database();
$db = $database->getConnection();
header('Content-Type: application/json');

// Oturum kontrolü
session_start();
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

// ID kontrolü
if (!isset($_POST['id']) && !isset($_POST['ids'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid application ID']);
    exit;
}

$applicationId = isset($_POST['id']) ? intval($_POST['id']) : null;
$applicationIds = isset($_POST['ids']) ? json_decode($_POST['ids'], true) : null;

try {
    switch ($_POST['action']) {
        case 'bulk_delete':
            if (!is_array($applicationIds) || empty($applicationIds)) {
                http_response_code(400);
                echo json_encode(['error' => 'Geçersiz başvuru ID\'leri']);
                exit;
            }

            // Önce test sonuçlarını sil
            $placeholders = str_repeat('?,', count($applicationIds) - 1) . '?';
            $query = "DELETE FROM test_results WHERE application_id IN ($placeholders)";
            $stmt = $db->prepare($query);
            $stmt->execute($applicationIds);

            // Sonra başvuruları sil
            $query = "DELETE FROM applications WHERE id IN ($placeholders)";
            $stmt = $db->prepare($query);
            $stmt->execute($applicationIds);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Seçili başvurular başarıyla silindi']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Başvurular bulunamadı']);
            }
            break;

        case 'delete':
            // Önce test sonuçlarını sil
            $query = "DELETE FROM test_results WHERE application_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$applicationId]);

            // Sonra başvuruyu sil
            $query = "DELETE FROM applications WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$applicationId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Başvuru başarıyla silindi']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Başvuru bulunamadı']);
            }
            break;

        case 'update_status':
            if (!isset($_POST['status'])) {
                http_response_code(400);
                echo json_encode(['error' => 'No status specified']);
                exit;
            }
            $status = $_POST['status'];
            $allowed = ['accepted', 'rejected'];
            if (!in_array($status, $allowed)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                exit;
            }
            $query = "UPDATE applications SET status = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$status, $applicationId]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Application not found or status unchanged']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log('Error in process_application.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
} 