<?php
require_once '../config/database.php';

// Başvuru ID kontrolü
if (!isset($_GET['id'])) {
    header('Location: applications.php');
    exit;
}

$application_id = $_GET['id'];

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru ve aday bilgilerini al
    $stmt = $db->prepare("
        SELECT a.*, a.status as application_status, p.title as position_title, c.* 
        FROM applications a 
        JOIN positions p ON a.position_id = p.id 
        JOIN candidates c ON a.candidate_id = c.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    // Debugging: Check the fetched status
    // echo "Fetched Status: " . $application['status'] . "<br>";

    if (!$application) {
        header('Location: applications.php');
        exit;
    }

    // Eğitim bilgilerini al
    $stmt = $db->prepare("SELECT * FROM educations WHERE candidate_id = ? ORDER BY start_date DESC");
    $stmt->execute([$application['candidate_id']]);
    $educations = $stmt->fetchAll();

    // Sertifika bilgilerini al
    $stmt = $db->prepare("SELECT * FROM certificates WHERE candidate_id = ? ORDER BY issue_date DESC");
    $stmt->execute([$application['candidate_id']]);
    $certificates = $stmt->fetchAll();

    // İş deneyimi bilgilerini al
    $stmt = $db->prepare("SELECT * FROM experiences WHERE candidate_id = ? ORDER BY start_date DESC");
    $stmt->execute([$application['candidate_id']]);
    $experiences = $stmt->fetchAll();

    // Referans bilgilerini al
    $stmt = $db->prepare("SELECT * FROM candidate_references WHERE candidate_id = ?");
    $stmt->execute([$application['candidate_id']]);
    $references = $stmt->fetchAll();

    // Test sonuçlarını al
    $stmt = $db->prepare("
        SELECT tr.*, t.title, t.passing_score,
        (SELECT COUNT(*) FROM test_questions WHERE test_id = t.id) as total_questions,
        (SELECT SUM(points) FROM test_questions WHERE test_id = t.id) as total_points
        FROM test_results tr
        JOIN tests t ON tr.test_id = t.id
        WHERE tr.application_id = ?
        ORDER BY tr.created_at DESC
    ");
    $stmt->execute([$application_id]);
    $test_results = $stmt->fetchAll();

    $page_title = "Başvuru Detayı - " . $application['position_title'];
    // require_once '../includes/header.php';
} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt me-2"></i>Başvuru Detayı
                    </h3>
                    <a href="download_application_detail.php?id=<?php echo $application['id']; ?>" class="btn btn-outline-danger float-end" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF Olarak İndir
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Sol Kolon - Başvuru Bilgileri -->
                        <div class="col-md-8">
                            <!-- Kişisel Bilgiler -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-user me-2"></i>Kişisel Bilgiler
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">Ad Soyad</th>
                                            <td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>E-posta</th>
                                            <td><?php echo htmlspecialchars($application['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Telefon</th>
                                            <td><?php echo htmlspecialchars($application['phone']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Şehir</th>
                                            <td><?php echo htmlspecialchars($application['city']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Doğum Tarihi</th>
                                            <td><?php echo htmlspecialchars($application['birth_date']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Cinsiyet</th>
                                            <td><?php 
                                                $gender = [
                                                    'male' => 'Erkek',
                                                    'female' => 'Kadın',
                                                    'other' => 'Belirtmek İstemiyorum'
                                                ];
                                                echo htmlspecialchars($gender[$application['gender']] ?? $application['gender']); 
                                            ?></td>
                                        </tr>
                                        <tr>
                                            <th>Adres</th>
                                            <td><?php echo nl2br(htmlspecialchars($application['address'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>LinkedIn</th>
                                            <td>
                                                <?php if ($application['linkedin_url']): ?>
                                                    <a href="<?php echo htmlspecialchars($application['linkedin_url']); ?>" target="_blank" class="text-primary">
                                                        <i class="fab fa-linkedin me-1"></i><?php echo htmlspecialchars($application['linkedin_url']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Belirtilmemiş</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>GitHub</th>
                                            <td>
                                                <?php if ($application['github_url']): ?>
                                                    <a href="<?php echo htmlspecialchars($application['github_url']); ?>" target="_blank" class="text-dark">
                                                        <i class="fab fa-github me-1"></i><?php echo htmlspecialchars($application['github_url']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Belirtilmemiş</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Portföy</th>
                                            <td>
                                                <?php if ($application['portfolio_url']): ?>
                                                    <a href="<?php echo htmlspecialchars($application['portfolio_url']); ?>" target="_blank" class="text-success">
                                                        <i class="fas fa-briefcase me-1"></i><?php echo htmlspecialchars($application['portfolio_url']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Belirtilmemiş</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Eğitim Bilgileri -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-graduation-cap me-2"></i>Eğitim Bilgileri
                                </h5>
                                <?php if (!empty($educations)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Okul</th>
                                                    <th>Bölüm</th>
                                                    <th>Derece</th>
                                                    <th>Tarih</th>
                                                    <th>GPA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($educations as $education): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($education['school_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($education['field_of_study']); ?></td>
                                                        <td><?php 
                                                            $degree = [
                                                                'high_school' => 'Lise',
                                                                'associate' => 'Ön Lisans',
                                                                'bachelor' => 'Lisans',
                                                                'master' => 'Yüksek Lisans',
                                                                'doctorate' => 'Doktora'
                                                            ];
                                                            echo htmlspecialchars($degree[$education['degree']] ?? $education['degree']); 
                                                        ?></td>
                                                        <td>
                                                            <?php 
                                                            echo date('d.m.Y', strtotime($education['start_date']));
                                                            echo ' - ';
                                                            echo $education['is_current'] ? 'Devam Ediyor' : date('d.m.Y', strtotime($education['end_date']));
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            if ($education['gpa']) {
                                                                echo htmlspecialchars($education['gpa']);
                                                                echo ' (' . ($education['gpa_system'] == '100' ? '100\'lük' : '4\'lük') . ' Sistem)';
                                                            } else {
                                                                echo '<span class="text-muted">Belirtilmemiş</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>Eğitim bilgisi girilmemiş.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Sertifikalar -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-certificate me-2"></i>Sertifikalar
                                </h5>
                                <?php if (!empty($certificates)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sertifika</th>
                                                    <th>Kurum</th>
                                                    <th>Tarih</th>
                                                    <th>Geçerlilik</th>
                                                    <th>ID/URL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($certificates as $certificate): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($certificate['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($certificate['issuing_organization']); ?></td>
                                                        <td><?php echo date('d.m.Y', strtotime($certificate['issue_date'])); ?></td>
                                                        <td>
                                                            <?php 
                                                            if ($certificate['expiry_date']) {
                                                                echo date('d.m.Y', strtotime($certificate['expiry_date']));
                                                            } else {
                                                                echo '<span class="text-muted">Süresiz</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($certificate['credential_url']): ?>
                                                                <a href="<?php echo htmlspecialchars($certificate['credential_url']); ?>" target="_blank" class="text-primary">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Görüntüle
                                                                </a>
                                                            <?php elseif ($certificate['credential_id']): ?>
                                                                <?php echo htmlspecialchars($certificate['credential_id']); ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>Sertifika bilgisi girilmemiş.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- İş Deneyimi -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-briefcase me-2"></i>İş Deneyimi
                                </h5>
                                <?php if (!empty($experiences)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Şirket</th>
                                                    <th>Pozisyon</th>
                                                    <th>Tarih</th>
                                                    <th>Açıklama</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($experiences as $experience): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($experience['company_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($experience['position']); ?></td>
                                                        <td>
                                                            <?php 
                                                            echo date('d.m.Y', strtotime($experience['start_date']));
                                                            echo ' - ';
                                                            echo $experience['is_current'] ? 'Devam Ediyor' : date('d.m.Y', strtotime($experience['end_date']));
                                                            ?>
                                                        </td>
                                                        <td><?php echo nl2br(htmlspecialchars($experience['description'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>İş deneyimi bilgisi girilmemiş.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Referanslar -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-users me-2"></i>Referanslar
                                </h5>
                                <?php if (!empty($references)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Ad Soyad</th>
                                                    <th>Şirket</th>
                                                    <th>Pozisyon</th>
                                                    <th>İletişim</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($references as $reference): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($reference['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($reference['company']); ?></td>
                                                        <td><?php echo htmlspecialchars($reference['position']); ?></td>
                                                        <td>
                                                            <?php if ($reference['email']): ?>
                                                                <div><i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($reference['email']); ?></div>
                                                            <?php endif; ?>
                                                            <?php if ($reference['phone']): ?>
                                                                <div><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($reference['phone']); ?></div>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>Referans bilgisi girilmemiş.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Test Sonuçları -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-tasks me-2"></i>Test Sonuçları
                                </h5>
                                <?php if (!empty($test_results)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Test Adı</th>
                                                    <th>Başlangıç</th>
                                                    <th>Bitiş</th>
                                                    <th>Puan</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($test_results as $result): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($result['title']); ?></td>
                                                        <td><?php echo date('d.m.Y H:i', strtotime($result['start_time'])); ?></td>
                                                        <td><?php echo $result['end_time'] ? date('d.m.Y H:i', strtotime($result['end_time'])) : '-'; ?></td>
                                                        <td>
                                                            <?php 
                                                            $max_score = $result['total_points'] ?? 0;
                                                            echo htmlspecialchars($result['score']) . '%';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($result['status'] == 'completed'): ?>
                                                                <?php if ($result['score'] >= $result['passing_score']): ?>
                                                                    <span class="badge bg-success">Başarılı</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Başarısız</span>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning">Devam Ediyor</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>Henüz test sonucu bulunmuyor.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Sağ Kolon - Başvuru Durumu ve Fotoğraf -->
                        <div class="col-md-4">
                            <!-- Başvuru Durumu -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Başvuru Durumu
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <?php
                                        $status_colors = [
                                            'draft' => 'secondary',
                                            'submitted' => 'primary',
                                            'in_review' => 'info',
                                            'in_test' => 'warning',
                                            'rejected' => 'danger',
                                            'accepted' => 'success'
                                        ];
                                        
                                        $status_texts = [
                                            'draft' => 'Taslak',
                                            'submitted' => 'Gönderildi',
                                            'in_review' => 'İnceleniyor',
                                            'in_test' => 'Test Aşamasında',
                                            'rejected' => 'Reddedildi',
                                            'accepted' => 'Kabul Edildi'
                                        ];
                                        
                                        $status = $application['application_status'];
                                        $color = $status_colors[$status] ?? 'secondary';
                                        $text = $status_texts[$status] ?? 'Beklemede';
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?> fs-6">
                                            <?php echo $text; ?>
                                        </span>
                                    </div>
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Son Güncelleme: <?php echo date('d.m.Y H:i', strtotime($application['updated_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Aday Fotoğrafı -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-camera me-2"></i>Aday Fotoğrafı
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <?php 
                                    $photo_path = $application['photo_path'] ? '../' . $application['photo_path'] : null;
                                    if ($photo_path && file_exists($photo_path)): 
                                    ?>
                                        <img src="<?php echo htmlspecialchars($photo_path); ?>" 
                                             alt="Aday Fotoğrafı" 
                                             class="img-fluid rounded candidate-photo">
                                    <?php else: ?>
                                        <div class="no-photo">
                                            <i class="fas fa-user fa-4x text-muted"></i>
                                            <p class="mt-2 text-muted">Fotoğraf yok</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Başvuru Bilgileri -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Başvuru Bilgileri
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Başvuru No</th>
                                            <td><?php echo $application['id']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Pozisyon</th>
                                            <td><?php echo htmlspecialchars($application['position_title']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Başvuru Tarihi</th>
                                            <td><?php echo date('d.m.Y H:i', strtotime($application['created_at'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Son Güncelleme</th>
                                            <td><?php echo date('d.m.Y H:i', strtotime($application['updated_at'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-title {
    color: #2c3e50;
    border-bottom: 2px solid #eee;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.candidate-photo {
    max-width: 200px;
    max-height: 200px;
    object-fit: cover;
    border: 1px solid #ddd;
    padding: 5px;
}

.no-photo {
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.badge {
    padding: 0.5rem 1rem;
}

.table th {
    background-color: #f8f9fa;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
    border-radius: 8px;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #eee;
    padding: 1rem;
}

.card-body {
    padding: 1.25rem;
}

.alert {
    border-radius: 8px;
    border: none;
}

.alert i {
    margin-right: 0.5rem;
}
</style>

</body>
</html> 