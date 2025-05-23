<?php
require_once 'auth_check.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

try {
    // Tarih parametrelerini al
    $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
    $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d', strtotime('+1 month'));

    // Mülakatları getir
    $query = "SELECT 
              i.id,
              i.interview_date as start,
              i.interview_date as end,
              CONCAT(c.first_name, ' ', c.last_name, ' - ', p.title) as title,
              i.status,
              i.interview_type,
              p.title as position,
              c.first_name,
              c.last_name
              FROM interviews i
              JOIN applications a ON i.application_id = a.id
              JOIN candidates c ON a.candidate_id = c.id
              JOIN positions p ON a.position_id = p.id
              WHERE i.interview_date BETWEEN :start AND :end
              ORDER BY i.interview_date ASC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':start', $start);
    $stmt->bindParam(':end', $end);
    $stmt->execute();
    
    $interviews = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Her mülakat için renk ve ek bilgileri ayarla
        $backgroundColor = '';
        switch ($row['status']) {
            case 'pending':
                $backgroundColor = '#ffc107';
                break;
            case 'confirmed':
                $backgroundColor = '#28a745';
                break;
            case 'completed':
                $backgroundColor = '#17a2b8';
                break;
            case 'cancelled':
                $backgroundColor = '#dc3545';
                break;
        }

        $interviews[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start'],
            'end' => $row['end'],
            'backgroundColor' => $backgroundColor,
            'borderColor' => $backgroundColor,
            'extendedProps' => [
                'status' => $row['status'],
                'type' => $row['interview_type'],
                'position' => $row['position'],
                'candidateName' => $row['first_name'] . ' ' . $row['last_name']
            ]
        ];
    }

    echo json_encode($interviews);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?> 