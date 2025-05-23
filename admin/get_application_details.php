<?php
require_once '../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Oturum kontrolü
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid application ID']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // Başvuru detaylarını getir
    $query = "SELECT 
        a.*,
        c.name as candidate_name,
        c.email as candidate_email,
        p.title as position_title,
        GROUP_CONCAT(
            DISTINCT JSON_OBJECT(
                'id', t.id,
                'title', t.title,
                'status', CASE WHEN tr.completed_at IS NOT NULL THEN 'completed' ELSE 'pending' END,
                'score', tr.score,
                'completed_at', tr.completed_at
            )
        ) as tests
    FROM applications a
    JOIN candidates c ON a.candidate_id = c.id
    JOIN positions p ON a.position_id = p.id
    LEFT JOIN position_tests pt ON p.id = pt.position_id
    LEFT JOIN tests t ON pt.test_id = t.id
    LEFT JOIN test_results tr ON (a.id = tr.application_id AND t.id = tr.test_id)
    WHERE a.id = :id
    GROUP BY a.id, a.created_at, c.name, c.email, p.title";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        http_response_code(404);
        echo json_encode(['error' => 'Application not found']);
        exit;
    }
    
    // Test sonuçlarını JSON formatına çevir
    if ($application['tests']) {
        $tests = explode(',', $application['tests']);
        $application['tests'] = array_map(function($test) {
            return json_decode($test, true);
        }, $tests);
    } else {
        $application['tests'] = [];
    }
    
    echo json_encode($application);
    
} catch (Exception $e) {
    error_log('Error in get_application_details.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
} 