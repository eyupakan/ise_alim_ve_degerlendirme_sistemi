<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$application_id = $_POST['application_id'] ?? '';
if (!$application_id) {
    die("Geçersiz başvuru.");
}

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru bilgilerini al
    $stmt = $db->prepare("
        SELECT a.*, p.title as position_title
        FROM applications a 
        JOIN positions p ON a.position_id = p.id
        WHERE a.id = ?
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        die("Geçersiz başvuru.");
    }

    // Zorunlu testleri kontrol et
    $stmt = $db->prepare("
        SELECT COUNT(*) as required_count,
        (
            SELECT COUNT(*) 
            FROM test_results tr
            JOIN position_tests pt ON tr.test_id = pt.test_id
            WHERE tr.application_id = ?
            AND pt.position_id = ?
            AND pt.required = 1
            AND tr.status = 'completed'
        ) as completed_count
        FROM position_tests
        WHERE position_id = ? AND required = 1
    ");
    $stmt->execute([$application_id, $application['position_id'], $application['position_id']]);
    $test_counts = $stmt->fetch();

    // Eğer tüm zorunlu testler tamamlanmamışsa
    if ($test_counts['required_count'] > $test_counts['completed_count']) {
        $_SESSION['error'] = "Tüm zorunlu testleri tamamlamanız gerekmektedir.";
        header("Location: apply_step4.php?application_id=" . $application_id);
        exit;
    }

    // Test puanlarını hesapla
    $stmt = $db->prepare("
        SELECT COALESCE(AVG(score), 0) as average_score
        FROM test_results
        WHERE application_id = ? AND status = 'completed'
    ");
    $stmt->execute([$application_id]);
    $test_result = $stmt->fetch();
    $test_points = $test_result['average_score'];

    // Pozisyonun puan katsayılarını al
    $stmt = $db->prepare("SELECT portfolio_point, certificate_point, education_point, reference_point, experience_point FROM positions WHERE id = ?");
    $stmt->execute([$application['position_id']]);
    $position_points = $stmt->fetch();

    // Toplam puan hesaplama (her alanın puanı * pozisyonun katsayısı)
    $total_points =
        ($application['portfolio_points'] * $position_points['portfolio_point']) +
        ($application['education_points'] * $position_points['education_point']) +
        ($application['certificate_points'] * $position_points['certificate_point']) +
        ($application['experience_points'] * $position_points['experience_point']) +
        ($application['reference_points'] * $position_points['reference_point']) +
        $test_points;

    // Başvuruyu tamamla
    $stmt = $db->prepare("
        UPDATE applications 
        SET status = 'submitted',
            current_step = 5,
            test_points = ?,
            total_points = ?
        WHERE id = ?
    ");
    $stmt->execute([$test_points, $total_points, $application_id]);

    // Başarı mesajını ayarla
    $_SESSION['success'] = "Başvurunuz başarıyla tamamlanmıştır. Başvurunuz değerlendirildikten sonra size bilgi verilecektir.";
    
    // Başarı sayfasına yönlendir
    header("Location: application_success.php");
    exit;

} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?> 