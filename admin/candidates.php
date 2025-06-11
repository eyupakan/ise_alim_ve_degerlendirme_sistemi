<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Pozisyonları getir
$query = "SELECT id, title FROM positions WHERE status = 'active' ORDER BY title";
$stmt = $db->prepare($query);
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Seçilen pozisyona göre adayları getir
$position_id = isset($_GET['position_id']) ? (int) $_GET['position_id'] : 0;

if ($position_id > 0) {
    $query = "SELECT c.*, 
              COUNT(DISTINCT a.id) as application_count,
              COUNT(DISTINCT CASE WHEN a.status = 'completed' THEN a.id END) as completed_count,
              a.total_points,
              c.photo_path
              FROM candidates c
              INNER JOIN applications a ON c.id = a.candidate_id
              WHERE a.position_id = :position_id
              AND a.status = 'accepted'
              GROUP BY c.id, c.first_name, c.last_name, c.email, c.phone, c.created_at, a.total_points, c.photo_path
              ORDER BY a.total_points DESC, c.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':position_id', $position_id);
} else {
    $query = "SELECT c.*, 
              COUNT(DISTINCT a.id) as application_count,
              COUNT(DISTINCT CASE WHEN a.status = 'completed' THEN a.id END) as completed_count,
              MAX(a.total_points) as total_points,
              c.photo_path
              FROM candidates c
              INNER JOIN applications a ON c.id = a.candidate_id
              WHERE a.status IN ('accepted', 'in_review')
              GROUP BY c.id, c.first_name, c.last_name, c.email, c.phone, c.created_at, c.photo_path
              ORDER BY MAX(a.total_points) DESC, c.created_at DESC";
    $stmt = $db->prepare($query);
}

$stmt->execute();
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaylar - Admin Paneli</title>
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
                            <a class="nav-link active" href="candidates.php">
                                <i class="bi bi-people"></i> Adaylar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="applications.php">
                                <i class="bi bi-file-earmark-text"></i> Başvurular
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="interviews.php">
                                <i class="bi bi-calendar-event"></i> Mülakatlar
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Adaylar</h5>
                        <div class="d-flex align-items-center">
                            <select class="form-select me-2" id="positionFilter"
                                onchange="filterByPosition(this.value)">
                                <option value="0">Tüm Pozisyonlar</option>
                                <?php foreach ($positions as $position): ?>
                                    <option value="<?php echo $position['id']; ?>" <?php echo $position_id == $position['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($position['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ad Soyad</th>
                                        <th>E-posta</th>
                                        <th>Telefon</th>
                                        <th>Aday Fotoğrafı</th>
                                        <th>Toplam Puan</th>
                                        <th>Toplam Başvuru</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($candidates as $candidate): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($candidate['email']); ?></td>
                                            <td><?php echo htmlspecialchars($candidate['phone']); ?></td>
                                            <td>
                                                <?php if (!empty($candidate['photo_path'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($candidate['photo_path']); ?>" 
                                                         alt="Aday Fotoğrafı" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                                <?php else: ?>
                                                    <div style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-person" style="font-size: 24px; color: #6c757d;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo isset($candidate['total_points']) ? $candidate['total_points'] : '0'; ?></td>
                                            <td><?php echo $candidate['application_count']; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($candidate['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info"
                                                    onclick="viewCandidate(<?php echo $candidate['id']; ?>)">
                                                    <i class="bi bi-eye"></i> Detay
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteCandidate(<?php echo $candidate['id']; ?>)">
                                                    <i class="bi bi-trash"></i> Sil
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aday Detay Modal -->
    <div class="modal fade" id="candidateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aday Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Aday bilgileri buraya yüklenecek -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewCandidate(id) {
            // AJAX ile aday detaylarını getir
            fetch('process_candidate.php?action=get&id=' + id)
                .then(response => response.json())
                .then(data => {
                    const modalBody = document.querySelector('#candidateModal .modal-body');
                    let applicationsHtml = '';
                    if (data.applications && data.applications.length > 0) {
                        applicationsHtml = '<h6 class="mt-3">Başvurular</h6><ul class="list-group">';
                        data.applications.forEach(app => {
                            applicationsHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${app.position_title} - ${app.created_at}</span>
                                <a href="application_detail.php?id=${app.id}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-search"></i> Detaylı İncele
                                </a>
                            </li>`;
                        });
                        applicationsHtml += '</ul>';
                    }
                    modalBody.innerHTML = `
                        <div class="row mb-3">
                            <div class="col-md-4 text-center mb-3">
                                ${data.photo_path ? 
                                    `<img src="../${data.photo_path}" alt="Aday Fotoğrafı" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">` :
                                    `<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                        <i class="bi bi-person" style="font-size: 64px; color: #6c757d;"></i>
                                    </div>`
                                }
                            </div>
                            <div class="col-md-8">
                                <h6>Kişisel Bilgiler</h6>
                                <p><strong>Ad Soyad:</strong> ${data.first_name} ${data.last_name}</p>
                                <p><strong>E-posta:</strong> <a href="mailto:${data.email}">${data.email}</a></p>
                                <p><strong>Telefon:</strong> ${data.phone}</p>
                                <p><strong>Şehir:</strong> ${data.city || '-'}</p>
                                ${applicationsHtml}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6>Profil Bilgileri</h6>
                                <p><strong>LinkedIn:</strong> ${data.linkedin_url ? `<a href="${data.linkedin_url}" target="_blank">Profili Görüntüle</a>` : '-'}</p>
                                <p><strong>GitHub:</strong> ${data.github_url ? `<a href="${data.github_url}" target="_blank">Profili Görüntüle</a>` : '-'}</p>
                                <p><strong>Portfolio:</strong> ${data.portfolio_url ? `<a href="${data.portfolio_url}" target="_blank">Profili Görüntüle</a>` : '-'}</p>
                            </div>
                        </div>
                    `;

                    // Modal'ı göster
                    new bootstrap.Modal(document.getElementById('candidateModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Aday bilgileri alınırken bir hata oluştu.');
                });
        }

        function deleteCandidate(id) {
            if (confirm('Bu adayı silmek istediğinize emin misiniz?')) {
                fetch('process_candidate.php?action=delete&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Aday silinirken bir hata oluştu.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Aday silinirken bir hata oluştu.');
                    });
            }
        }

        function getStatusClass(status) {
            const classes = {
                'draft': 'secondary',
                'submitted': 'primary',
                'in_review': 'info',
                'in_test': 'warning',
                'rejected': 'danger',
                'accepted': 'success'
            };
            return classes[status] || 'secondary';
        }

        function getStatusText(status) {
            const texts = {
                'draft': 'Taslak',
                'submitted': 'Gönderildi',
                'in_review': 'İncelemede',
                'in_test': 'Test Aşamasında',
                'rejected': 'Reddedildi',
                'accepted': 'Kabul Edildi'
            };
            return texts[status] || status;
        }

        function filterByPosition(positionId) {
            window.location.href = 'candidates.php' + (positionId > 0 ? '?position_id=' + positionId : '');
        }
    </script>

    <?php if (isset($_GET['success'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Başarılı</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <?php
                    switch ($_GET['success']) {
                        case 'deleted':
                            echo 'Aday başarıyla silindi.';
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="toast show" role="alert">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Hata</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>