<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Form verilerini al
$application_id = $_POST['application_id'] ?? '';

// Başvuru kontrolü
try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT * FROM applications WHERE id = ? AND current_step = 2");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        die("Geçersiz başvuru.");
    }

    // Veritabanı işlemleri için transaction başlat
    $db->beginTransaction();

    $education_points = 0;
    $certificate_points = 0;

    // Eğitim bilgilerini kaydet
    if (isset($_POST['education']) && is_array($_POST['education'])) {
        foreach ($_POST['education'] as $education) {
            if (empty($education['school_name']) || empty($education['field_of_study']) || 
                empty($education['degree']) || empty($education['start_date'])) {
                continue;
            }

            $is_current = isset($education['is_current']) ? 1 : 0;
            $end_date = $is_current ? null : $education['end_date'];

            $stmt = $db->prepare("
                INSERT INTO educations (
                    candidate_id, school_name, degree, field_of_study, 
                    start_date, end_date, is_current
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $application['candidate_id'],
                $education['school_name'],
                $education['degree'],
                $education['field_of_study'],
                $education['start_date'],
                $end_date,
                $is_current
            ]);

            // Puanlama
            $education_points += $is_current ? 1 : 3;
        }
    }

    // Sertifika bilgilerini kaydet
    if (isset($_POST['certificate']) && is_array($_POST['certificate'])) {
        foreach ($_POST['certificate'] as $certificate) {
            if (empty($certificate['name']) || empty($certificate['issuing_organization']) || 
                empty($certificate['issue_date'])) {
                continue;
            }

            $stmt = $db->prepare("
                INSERT INTO certificates (
                    candidate_id, name, issuing_organization, issue_date
                ) VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $application['candidate_id'],
                $certificate['name'],
                $certificate['issuing_organization'],
                $certificate['issue_date']
            ]);

            // Her sertifika 2 puan
            $certificate_points += 2;
        }
    }

    // Başvuruyu güncelle
    $total_points = $application['portfolio_points'] + $education_points + $certificate_points;
    
    $stmt = $db->prepare("
        UPDATE applications 
        SET current_step = 3,
            education_points = ?,
            certificate_points = ?,
            total_points = ?
        WHERE id = ?
    ");
    $stmt->execute([$education_points, $certificate_points, $total_points, $application_id]);

    // Transaction'ı tamamla
    $db->commit();

    // Bir sonraki adıma yönlendir
    header("Location: apply_step3.php?application_id=" . $application_id);
    exit;

} catch(PDOException $e) {
    // Hata durumunda transaction'ı geri al
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Bir hata oluştu: " . $e->getMessage());
}
?> 