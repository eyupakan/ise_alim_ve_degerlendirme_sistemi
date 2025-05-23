<?php
require_once 'auth_check.php';
require_once '../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = new Database();
$db = $database->getConnection();

// Pozisyonları getir
$query = "SELECT id, title FROM positions WHERE status = 'active' ORDER BY title";
$stmt = $db->prepare($query);
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Arama ve pozisyon filtreleme parametrelerini al
$search = isset($_GET['search']) ? $_GET['search'] : '';
$position_id = isset($_GET['position_id']) ? (int)$_GET['position_id'] : 0;

// Başvuruları getir
$query = "SELECT a.*, 
          CONCAT(c.first_name, ' ', c.last_name) as candidate_name, 
          c.email as candidate_email,
          c.phone as candidate_phone,
          p.title as position_title,
          COUNT(DISTINCT t.id) as total_tests,
          COUNT(DISTINCT CASE WHEN tr.status = 'completed' THEN tr.id END) as completed_tests,
          a.total_points
          FROM applications a
          LEFT JOIN candidates c ON a.candidate_id = c.id
          LEFT JOIN positions p ON a.position_id = p.id
          LEFT JOIN position_tests pt ON p.id = pt.position_id
          LEFT JOIN tests t ON pt.test_id = t.id
          LEFT JOIN test_results tr ON (a.id = tr.application_id AND t.id = tr.test_id)";

// Eğer arama yapılıyorsa WHERE koşulu ekle
if (!empty($search)) {
    $query .= " WHERE (c.first_name LIKE :search 
                OR c.last_name LIKE :search 
                OR c.email LIKE :search 
                OR c.phone LIKE :search)";
}

// Pozisyon filtresi ekle
if ($position_id > 0) {
    $query .= empty($search) ? " WHERE " : " AND ";
    $query .= "a.position_id = :position_id";
}

$query .= " GROUP BY a.id, a.created_at, c.first_name, c.last_name, c.email, c.phone, p.title, a.total_points
          ORDER BY a.total_points DESC, a.created_at DESC";

$stmt = $db->prepare($query);

// Arama parametresini bind et
if (!empty($search)) {
    $searchParam = "%{$search}%";
    $stmt->bindParam(':search', $searchParam);
}

// Pozisyon ID'sini bind et
if ($position_id > 0) {
    $stmt->bindParam(':position_id', $position_id);
}

$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Başvurular - Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #f8f9fa;
        }
        .main-content {
            padding: 20px;
        }
        .progress {
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4>Admin Paneli</h4>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="positions.php">
                                <i class="bi bi-briefcase"></i> Pozisyonlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tests.php">
                                <i class="bi bi-file-text"></i> Testler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="candidates.php">
                                <i class="bi bi-people"></i> Adaylar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="applications.php">
                                <i class="bi bi-file-earmark-text"></i> Başvurular
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Başvurular</h2>
                    <div class="d-flex gap-2">
                        <form action="" method="GET" class="d-flex gap-2">
                            <select name="position_id" class="form-select" onchange="this.form.submit()">
                                <option value="0">Tüm Pozisyonlar</option>
                                <?php foreach ($positions as $position): ?>
                                    <option value="<?php echo $position['id']; ?>" <?php echo $position_id == $position['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($position['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="İsim, e-posta veya telefon ile ara..."
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if (!empty($search) || $position_id > 0): ?>
                                <a href="applications.php" class="btn btn-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Başvuru Listesi -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($applications)): ?>
                            <div class="alert alert-info">
                                <?php echo empty($search) ? 'Henüz başvuru bulunmuyor.' : 'Arama kriterlerine uygun başvuru bulunamadı.'; ?>
                            </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Aday</th>
                                        <th>Pozisyon</th>
                                        <th>İlerleme</th>
                                        <th>Toplam Puan</th>
                                        <th>Durum</th>
                                        <th>Başvuru Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $application): ?>
                                    <tr>
                                        <td>
                                            <div><?php echo htmlspecialchars($application['candidate_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($application['candidate_email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($application['position_title']); ?></td>
                                        <td>
                                            <?php
                                            $progress = $application['total_tests'] > 0 
                                                ? ($application['completed_tests'] / $application['total_tests']) * 100 
                                                : 0;
                                            ?>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo $progress; ?>%"
                                                     aria-valuenow="<?php echo $progress; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?php echo round($progress); ?>%
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo $application['completed_tests']; ?>/<?php echo $application['total_tests']; ?> Test
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($application['total_points'] > 0): ?>
                                                <?php echo round($application['total_points']); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'pending' => 'secondary',
                                                'in_progress' => 'warning',
                                                'in_test' => 'warning',
                                                'completed' => 'success',
                                                'rejected' => 'danger',
                                                'submitted' => 'info',
                                                'accepted' => 'success',
                                                'in_review' => 'info'
                                            ];
                                            $statusText = [
                                                'draft' => 'Taslak',
                                                'pending' => 'Bekliyor',
                                                'in_progress' => 'Devam Ediyor',
                                                'in_test' => 'Test Aşamasında',
                                                'completed' => 'Tamamlandı',
                                                'rejected' => 'Reddedildi',
                                                'submitted' => 'Gönderildi',
                                                'accepted' => 'Kabul Edildi',
                                                'in_review' => 'İncelemede'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo isset($statusClass[$application['status']]) ? $statusClass[$application['status']] : 'secondary'; ?>">
                                                <?php echo isset($statusText[$application['status']]) ? $statusText[$application['status']] : htmlspecialchars($application['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($application['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-view-application" data-id="<?php echo $application['id']; ?>">
                                                <i class="bi bi-eye"></i> Detay
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-delete-application" data-id="<?php echo $application['id']; ?>">
                                                <i class="bi bi-trash"></i> Sil
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Başvuru Detay Modal -->
    <div class="modal fade" id="applicationDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Başvuru Detayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Kişisel Bilgiler</h6>
                            <p class="mb-1"><strong>Ad Soyad:</strong> <span id="candidate_name"></span></p>
                            <p class="mb-1"><strong>E-posta:</strong> <span id="candidate_email"></span></p>
                            <p class="mb-1"><strong>Telefon:</strong> <span id="candidate_phone"></span></p>
                            <p class="mb-1"><strong>Şehir:</strong> <span id="candidate_city"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Profil Bilgileri</h6>
                            <p class="mb-1"><strong>LinkedIn:</strong> <span id="linkedin_url"></span></p>
                            <p class="mb-1"><strong>GitHub:</strong> <span id="github_url"></span></p>
                            <p class="mb-1"><strong>Portfolio:</strong> <span id="portfolio_url"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Başvuru Bilgileri</h6>
                            <p class="mb-1"><strong>Pozisyon:</strong> <span id="position_title"></span></p>
                            <p class="mb-1"><strong>Başvuru Tarihi:</strong> <span id="application_date"></span></p>
                            <p class="mb-1"><strong>Durum:</strong> <span id="application_status"></span></p>
                        </div>
                        <div class="col-12">
                            <h6>Test Sonuçları</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Test</th>
                                            <th>Durum</th>
                                            <th>Puan</th>
                                            <th>Tamamlanma Tarihi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="test_results">
                                        <!-- Test sonuçları JavaScript ile doldurulacak -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a id="btn-detail-review" href="#" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i> Detaylı İncele
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-accept-application" style="display:none;">Kabul Et</button>
                    <button type="button" class="btn btn-danger" id="btn-reject-application" style="display:none;">Reddet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Başvuru detayını görüntüleme
        document.querySelectorAll('.btn-view-application').forEach(button => {
            button.addEventListener('click', function() {
                const applicationId = this.dataset.id;
                
                // Başvuru detaylarını getir
                fetch(`get_application_detail.php?id=${applicationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }

                        // Aday ve başvuru bilgilerini doldur
                        document.getElementById('candidate_name').textContent = data.candidate_name;
                        document.getElementById('candidate_email').textContent = data.candidate_email;
                        document.getElementById('candidate_phone').textContent = data.phone || '-';
                        document.getElementById('candidate_city').textContent = data.city || '-';
                        
                        // Profil linklerini doldur
                        document.getElementById('linkedin_url').innerHTML = data.linkedin_url ? 
                            `<a href="${data.linkedin_url}" target="_blank">Profili Görüntüle</a>` : '-';
                        document.getElementById('github_url').innerHTML = data.github_url ? 
                            `<a href="${data.github_url}" target="_blank">Profili Görüntüle</a>` : '-';
                        document.getElementById('portfolio_url').innerHTML = data.portfolio_url ? 
                            `<a href="${data.portfolio_url}" target="_blank">Profili Görüntüle</a>` : '-';

                        document.getElementById('position_title').textContent = data.position_title;
                        document.getElementById('application_date').textContent = new Date(data.created_at).toLocaleString('tr-TR');
                        
                        const statusText = {
                            'pending': 'Bekliyor',
                            'in_progress': 'Devam Ediyor',
                            'completed': 'Tamamlandı',
                            'rejected': 'Reddedildi',
                            'submitted': 'Gönderildi',
                            'accepted': 'Kabul Edildi'
                        };
                        const statusClass = {
                            'pending': 'secondary',
                            'in_progress': 'warning',
                            'completed': 'success',
                            'rejected': 'danger',
                            'submitted': 'info',
                            'accepted': 'success'
                        };
                        document.getElementById('application_status').innerHTML = 
                            `<span class="badge bg-${statusClass[data.status]}">${statusText[data.status]}</span>`;

                        // Test sonuçlarını doldur
                        const testResultsBody = document.getElementById('test_results');
                        testResultsBody.innerHTML = '';
                        
                        data.test_results.forEach(result => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${result.test_title}</td>
                                <td>${result.completed_at ? '<span class="badge bg-success">Tamamlandı</span>' : 
                                                          '<span class="badge bg-warning">Bekliyor</span>'}</td>
                                <td>${result.completed_at ? result.score + '%' : '-'}</td>
                                <td>${result.completed_at ? new Date(result.completed_at).toLocaleString('tr-TR') : '-'}</td>
                            `;
                            testResultsBody.appendChild(row);
                        });

                        // Modalı göster
                        const modal = new bootstrap.Modal(document.getElementById('applicationDetailModal'));
                        // Kabul/Reddet butonlarını göster/gizle
                        const btnAccept = document.getElementById('btn-accept-application');
                        const btnReject = document.getElementById('btn-reject-application');
                        btnAccept.style.display = (data.status !== 'accepted') ? 'inline-block' : 'none';
                        btnReject.style.display = (data.status !== 'rejected') ? 'inline-block' : 'none';
                        btnAccept.onclick = function() {
                            updateApplicationStatus(applicationId, 'accepted');
                        };
                        btnReject.onclick = function() {
                            updateApplicationStatus(applicationId, 'rejected');
                        };
                        modal.show();

                        // Detaylı incele butonunu güncelle
                        document.getElementById('btn-detail-review').href = `application_detail.php?id=${applicationId}`;
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: error.message
                        });
                    });
            });
        });

        function updateApplicationStatus(applicationId, newStatus) {
            Swal.fire({
                title: newStatus === 'accepted' ? 'Başvuru kabul edilsin mi?' : 'Başvuru reddedilsin mi?',
                icon: newStatus === 'accepted' ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonText: newStatus === 'accepted' ? 'Evet, kabul et' : 'Evet, reddet',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'update_status');
                    formData.append('id', applicationId);
                    formData.append('status', newStatus);
                    fetch('process_application.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Başvuru durumu güncellendi.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.error || 'Bir hata oluştu');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: error.message
                        });
                    });
                }
            });
        }

        // Başvuru silme
        document.querySelectorAll('.btn-delete-application').forEach(button => {
            button.addEventListener('click', function() {
                const applicationId = this.dataset.id;
                
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu başvuruyu silmek istediğinizden emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, sil',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', applicationId);

                        fetch('process_application.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: 'Başvuru başarıyla silindi.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.error || 'Bir hata oluştu');
                            }
                        })
                        .catch(error => {
                            console.error('Hata:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: error.message
                            });
                        });
                    }
                });
            });
        });
    </script>
</body>
</html> 