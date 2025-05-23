<?php
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
    echo json_encode(['error' => 'Geçersiz başvuru ID.']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru detaylarını getir
    $query = "SELECT a.*, 
              CONCAT(c.first_name, ' ', c.last_name) as candidate_name, c.email as candidate_email,
              c.phone, c.city, c.linkedin_url, c.github_url, c.portfolio_url,
              p.title as position_title
              FROM applications a
              LEFT JOIN candidates c ON a.candidate_id = c.id
              LEFT JOIN positions p ON a.position_id = p.id
              WHERE a.id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        throw new Exception('Başvuru bulunamadı.');
    }

    // Test sonuçlarını getir
    $query = "SELECT t.title as test_title, tr.end_time as completed_at, tr.score
              FROM position_tests pt
              JOIN tests t ON pt.test_id = t.id
              LEFT JOIN test_results tr ON (t.id = tr.test_id AND tr.application_id = :application_id)
              WHERE pt.position_id = :position_id
              ORDER BY t.title";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':application_id', $_GET['id']);
    $stmt->bindParam(':position_id', $application['position_id']);
    $stmt->execute();
    $test_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Yanıtı hazırla
    echo json_encode([
        'id' => $application['id'],
        'candidate_name' => $application['candidate_name'],
        'candidate_email' => $application['candidate_email'],
        'phone' => $application['phone'],
        'city' => $application['city'],
        'linkedin_url' => $application['linkedin_url'],
        'github_url' => $application['github_url'],
        'portfolio_url' => $application['portfolio_url'],
        'position_title' => $application['position_title'],
        'status' => $application['status'],
        'created_at' => $application['created_at'],
        'test_results' => $test_results
    ]);

} catch (Exception $e) {
    error_log('Error in get_application_detail.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 