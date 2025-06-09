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
                    start_date, end_date, is_current, gpa
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $application['candidate_id'],
                $education['school_name'],
                $education['degree'],
                $education['field_of_study'],
                $education['start_date'],
                $end_date,
                $is_current,
                !empty($education['gpa']) ? $education['gpa'] : null
            ]);

            // Puanlama
            $base_points = $is_current ? 1 : 3; // Temel puan (devam ediyor: 1, tamamlandı: 3)
            
            // GPA bazlı ek puanlar
            $gpa_points = 0;
            if (!empty($education['gpa'])) {
                $gpa = floatval($education['gpa']);
                $gpa_system = $education['gpa_system'] ?? '100'; // Varsayılan olarak 100'lük sistem
                
                if ($gpa_system === '100') {
                    // 100'lük sistem için puanlama ve değer kontrolü
                    if ($gpa > 100) {
                        $gpa = 100; // Maksimum 100 olarak sınırla
                    }
                    if ($gpa < 0) {
                        $gpa = 0; // Minimum 0 olarak sınırla
                    }
                    
                    if ($gpa >= 85) {
                        $gpa_points = 10;
                    } elseif ($gpa >= 70) {
                        $gpa_points = 7;
                    } elseif ($gpa >= 60) {
                        $gpa_points = 3;
                    }
                } else {
                    // 4'lük sistem için puanlama ve değer kontrolü
                    if ($gpa > 4) {
                        $gpa = 4; // Maksimum 4 olarak sınırla
                    }
                    if ($gpa < 0) {
                        $gpa = 0; // Minimum 0 olarak sınırla
                    }
                    
                    if ($gpa >= 3.50) {
                        $gpa_points = 10;
                    } elseif ($gpa >= 3.00) {
                        $gpa_points = 7;
                    } elseif ($gpa >= 2.50) {
                        $gpa_points = 3;
                    }
                }
            }
            
            $education_points += $base_points + $gpa_points;
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