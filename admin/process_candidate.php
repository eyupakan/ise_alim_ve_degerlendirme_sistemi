<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// GET isteği - Aday bilgilerini getir veya sil
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'get' && isset($_GET['id'])) {
            // Aday bilgilerini getir
            $id = (int)$_GET['id'];
            
            // Aday detaylarını getir
            $query = "SELECT * FROM candidates WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($candidate) {
                // Adayın başvurularını getir
                $query = "SELECT a.*, p.title as position_title 
                         FROM applications a
                         LEFT JOIN positions p ON a.position_id = p.id
                         WHERE a.candidate_id = :candidate_id
                         ORDER BY a.created_at DESC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":candidate_id", $id);
                $stmt->execute();
                
                $candidate['applications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                header('Content-Type: application/json');
                echo json_encode($candidate);
                exit();
            }
            
            http_response_code(404);
            echo json_encode(['error' => 'Aday bulunamadı']);
            exit();
            
        } else if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
            // Adayı sil
            try {
                $id = (int)$_GET['id'];

                // 1. Adayın başvurularına bağlı test_results ve test_answers silinsin
                $query = "SELECT id FROM applications WHERE candidate_id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $application_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($application_ids)) {
                    // Test sonuçlarını ve cevaplarını sil
                    $in = str_repeat('?,', count($application_ids) - 1) . '?';
                    // Test cevapları
                    $db->prepare("DELETE FROM test_answers WHERE test_result_id IN (SELECT id FROM test_results WHERE application_id IN ($in))")
                        ->execute($application_ids);
                    // Test sonuçları
                    $db->prepare("DELETE FROM test_results WHERE application_id IN ($in)")
                        ->execute($application_ids);
                }

                // 2. Sertifikalar, eğitimler, deneyimler, referanslar, beceriler
                $db->prepare("DELETE FROM certificates WHERE candidate_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM educations WHERE candidate_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM experiences WHERE candidate_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM candidate_references WHERE candidate_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM candidate_skills WHERE candidate_id = ?")->execute([$id]);

                // 3. Başvuruları sil
                $db->prepare("DELETE FROM applications WHERE candidate_id = ?")->execute([$id]);

                // 4. Adayı sil
                $query = "DELETE FROM candidates WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":id", $id);

                if ($stmt->execute()) {
                    header("Location: candidates.php?success=deleted");
                } else {
                    throw new Exception("Aday silinirken bir hata oluştu.");
                }
            } catch (Exception $e) {
                header("Location: candidates.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
    }
}

// Geçersiz istek
header("Location: candidates.php");
exit(); 