<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Form verilerini al
$application_id = $_POST['application_id'] ?? '';
$kvkk_accepted = isset($_POST['kvkk_accepted']);

if (!$kvkk_accepted) {
    die("KVKK onayı gereklidir.");
}

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru kontrolü
    $stmt = $db->prepare("SELECT * FROM applications WHERE id = ? AND current_step = 3");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        die("Geçersiz başvuru.");
    }

    // Veritabanı işlemleri için transaction başlat
    $db->beginTransaction();

    $experience_points = 0;
    $reference_points = 0;

    // İş deneyimi bilgilerini kaydet
    if (isset($_POST['experience']) && is_array($_POST['experience'])) {
        foreach ($_POST['experience'] as $experience) {
            if (empty($experience['company_name']) || empty($experience['position']) || 
                empty($experience['start_date'])) {
                continue;
            }

            $is_current = isset($experience['is_current']) ? 1 : 0;
            $end_date = $is_current ? date('Y-m-d') : $experience['end_date'];

            // Çalışma süresini hesapla (ay cinsinden)
            $start = new DateTime($experience['start_date']);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            $months = ($interval->y * 12) + $interval->m;

            $stmt = $db->prepare("
                INSERT INTO experiences (
                    candidate_id, company_name, position, start_date, 
                    end_date, is_current, duration_months, responsibilities
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $application['candidate_id'],
                $experience['company_name'],
                $experience['position'],
                $experience['start_date'],
                $is_current ? null : $end_date,
                $is_current,
                $months,
                $experience['responsibilities'] ?? ''
            ]);

            // Her ay için 3 puan
            $experience_points += $months * 3;
        }
    }

    // Referans bilgilerini kaydet
    if (isset($_POST['reference']) && is_array($_POST['reference'])) {
        foreach ($_POST['reference'] as $reference) {
            if (empty($reference['name']) || empty($reference['company']) || 
                empty($reference['position']) || empty($reference['email'])) {
                continue;
            }

            $stmt = $db->prepare("
                INSERT INTO candidate_references (
                    candidate_id, name, company, position, email, phone
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $application['candidate_id'],
                $reference['name'],
                $reference['company'],
                $reference['position'],
                $reference['email'],
                $reference['phone'] ?? null
            ]);

            // Her referans 1 puan
            $reference_points += 1;
        }
    }

    // Başvuruyu güncelle
    $total_points = $application['portfolio_points'] + $application['education_points'] + 
                   $application['certificate_points'] + $experience_points + $reference_points;
    
    $stmt = $db->prepare("
        UPDATE applications 
        SET current_step = 4,
            experience_points = ?,
            reference_points = ?,
            total_points = ?,
            kvkk_accepted = ?
        WHERE id = ?
    ");
    $stmt->execute([$experience_points, $reference_points, $total_points, $kvkk_accepted, $application_id]);

    // Transaction'ı tamamla
    $db->commit();

    // Bir sonraki adıma yönlendir
    header("Location: apply_step4.php?application_id=" . $application_id);
    exit;

} catch(PDOException $e) {
    // Hata durumunda transaction'ı geri al
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Bir hata oluştu: " . $e->getMessage());
}
?> 