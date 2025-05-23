<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Başvuru ID kontrolü
if (!isset($_POST['application_id'])) {
    header('Location: index.php');
    exit;
}

$application_id = $_POST['application_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru ve pozisyon bilgilerini al
    $stmt = $db->prepare("
        SELECT a.*, p.id as position_id 
        FROM applications a 
        JOIN positions p ON a.position_id = p.id 
        WHERE a.id = ? AND a.current_step = 4
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        header('Location: index.php');
        exit;
    }

    // Test atlamak için istek geldiyse
    if (isset($_POST['action']) && $_POST['action'] === 'skip_test' && isset($_POST['test_id'])) {
        $test_id = $_POST['test_id'];
        
        // Testin zorunlu olup olmadığını kontrol et
        $stmt = $db->prepare("
            SELECT required 
            FROM position_tests 
            WHERE position_id = ? AND test_id = ?
        ");
        $stmt->execute([$application['position_id'], $test_id]);
        $test_info = $stmt->fetch();

        if (!$test_info || $test_info['required']) {
            $_SESSION['error'] = "Zorunlu testler atlanamaz!";
            header("Location: apply_step4.php?application_id=" . $application_id);
            exit;
        }

        // Test sonucunu 0 puan olarak kaydet
        $stmt = $db->prepare("
            INSERT INTO test_results (application_id, test_id, score, status, completion_time)
            VALUES (?, ?, 0, 'skipped', NOW())
        ");
        $stmt->execute([$application_id, $test_id]);

        header("Location: apply_step4.php?application_id=" . $application_id);
        exit;
    }

    // Tüm zorunlu testlerin tamamlanıp tamamlanmadığını kontrol et
    $stmt = $db->prepare("
        SELECT pt.test_id, tr.status
        FROM position_tests pt
        LEFT JOIN test_results tr ON tr.test_id = pt.test_id AND tr.application_id = ?
        WHERE pt.position_id = ? AND pt.required = 1
    ");
    $stmt->execute([$application_id, $application['position_id']]);
    $required_tests = $stmt->fetchAll();

    $all_required_completed = true;
    foreach ($required_tests as $test) {
        if (!$test['status'] || $test['status'] === 'in_progress') {
            $all_required_completed = false;
            break;
        }
    }

    if (!$all_required_completed) {
        $_SESSION['error'] = "Lütfen tüm zorunlu testleri tamamlayın!";
        header("Location: apply_step4.php?application_id=" . $application_id);
        exit;
    }

    // Test puanlarını topla
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(score), 0) as total_test_score
        FROM test_results
        WHERE application_id = ?
    ");
    $stmt->execute([$application_id]);
    $test_scores = $stmt->fetch();

    // Başvuruyu tamamla
    $db->beginTransaction();

    try {
        // Test puanlarını ekle
        $stmt = $db->prepare("
            UPDATE applications 
            SET test_score = ?, 
                total_score = education_score + experience_score + reference_score + ?,
                current_step = 5,
                status = 'completed',
                completion_date = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$test_scores['total_test_score'], $test_scores['total_test_score'], $application_id]);

        $db->commit();
        
        $_SESSION['success'] = "Başvurunuz başarıyla tamamlanmıştır!";
        header("Location: application_success.php?id=" . $application_id);
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
} 