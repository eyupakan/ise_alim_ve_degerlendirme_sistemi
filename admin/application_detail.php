<?php
require_once 'auth_check.php';
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Geçersiz başvuru ID.');
}

$application_id = (int)$_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Başvuru ve aday bilgilerini çek
$query = "SELECT a.*, c.*, c.id as candidate_id, p.title as position_title
          FROM applications a
          JOIN candidates c ON a.candidate_id = c.id
          JOIN positions p ON a.position_id = p.id
          WHERE a.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $application_id);
$stmt->execute();
$application = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$application) {
    die('Başvuru bulunamadı.');
}

// Test sonuçlarını çek
$query = "SELECT t.title, tr.score, tr.status, tr.end_time
          FROM position_tests pt
          JOIN tests t ON pt.test_id = t.id
          LEFT JOIN test_results tr ON t.id = tr.test_id AND tr.application_id = :application_id
          WHERE pt.position_id = :position_id
          ORDER BY t.title";
$stmt = $db->prepare($query);
$stmt->bindParam(':application_id', $application_id);
$stmt->bindParam(':position_id', $application['position_id']);
$stmt->execute();
$test_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sertifikaları çek
$query = "SELECT * FROM certificates WHERE candidate_id = :candidate_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':candidate_id', $application['candidate_id']);
$stmt->execute();
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// İş deneyimlerini çek
$query = "SELECT * FROM experiences WHERE candidate_id = :candidate_id ORDER BY start_date DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':candidate_id', $application['candidate_id']);
$stmt->execute();
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Aday Başvuru Detayı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="download_application_detail.php?id=<?php echo $application_id; ?>" class="btn btn-success" target="_blank">
            <i class="bi bi-download"></i> PDF Olarak İndir
        </a>
    </div>
    <h2>Aday Başvuru Detayı</h2>
    <hr>
    <h4>Kişisel Bilgiler</h4>
    <table class="table table-bordered">
        <tr><th>Ad Soyad</th><td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($application['email']); ?></td></tr>
        <tr><th>Telefon</th><td><?php echo htmlspecialchars($application['phone']); ?></td></tr>
        <tr><th>Şehir</th><td><?php echo htmlspecialchars($application['city']); ?></td></tr>
        <tr><th>LinkedIn</th><td><?php echo htmlspecialchars($application['linkedin_url']); ?></td></tr>
        <tr><th>Github</th><td><?php echo htmlspecialchars($application['github_url']); ?></td></tr>
        <tr><th>Portföy</th><td><?php echo htmlspecialchars($application['portfolio_url']); ?></td></tr>
    </table>
    <h4>Başvuru Bilgileri</h4>
    <table class="table table-bordered">
        <tr><th>Pozisyon</th><td><?php echo htmlspecialchars($application['position_title']); ?></td></tr>
        <tr><th>Başvuru Tarihi</th><td><?php echo htmlspecialchars($application['created_at']); ?></td></tr>
        <tr><th>Durum</th><td><?php echo htmlspecialchars($application['status']); ?></td></tr>
        <tr><th>Toplam Puan</th><td><?php echo htmlspecialchars($application['total_points']); ?></td></tr>
    </table>
    <h4>Test Sonuçları</h4>
    <table class="table table-bordered">
        <thead><tr><th>Test</th><th>Puan</th><th>Durum</th><th>Tamamlanma</th></tr></thead>
        <tbody>
        <?php foreach ($test_results as $test): ?>
            <tr>
                <td><?php echo htmlspecialchars($test['title']); ?></td>
                <td><?php echo is_null($test['score']) ? '-' : round($test['score'], 1) . '%'; ?></td>
                <td><?php echo $test['status'] === 'completed' ? 'Tamamlandı' : ($test['status'] === 'skipped' ? 'Atlandı' : 'Bekliyor'); ?></td>
                <td><?php echo $test['end_time'] ? htmlspecialchars($test['end_time']) : '-'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h4>Sertifikalar</h4>
    <?php if (empty($certificates)): ?>
        <div class="alert alert-secondary">Sertifika bilgisi yok.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead><tr><th>Sertifika Adı</th><th>Kurum</th><th>Veriliş Tarihi</th></tr></thead>
            <tbody>
            <?php foreach ($certificates as $cert): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cert['name']); ?></td>
                    <td><?php echo htmlspecialchars($cert['issuing_organization']); ?></td>
                    <td><?php echo htmlspecialchars($cert['issue_date']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <h4>İş Deneyimi</h4>
    <?php if (empty($experiences)): ?>
        <div class="alert alert-secondary">İş deneyimi bilgisi yok.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead><tr><th>Pozisyon</th><th>Şirket</th><th>Başlangıç</th><th>Bitiş</th><th>Açıklama</th></tr></thead>
            <tbody>
            <?php foreach ($experiences as $exp): ?>
                <tr>
                    <td><?php echo htmlspecialchars($exp['position']); ?></td>
                    <td><?php echo htmlspecialchars($exp['company']); ?></td>
                    <td><?php echo htmlspecialchars($exp['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($exp['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($exp['description']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html> 