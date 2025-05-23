<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$interview_id = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    // Mülakat detaylarını getir
    $query = "SELECT 
              i.*,
              CONCAT(c.first_name, ' ', c.last_name) as candidate_name,
              c.email as candidate_email,
              c.phone as candidate_phone,
              p.title as position_title,
              a.status as application_status
              FROM interviews i
              JOIN applications a ON i.application_id = a.id
              JOIN candidates c ON a.candidate_id = c.id
              JOIN positions p ON a.position_id = p.id
              WHERE i.id = :interview_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':interview_id', $interview_id);
    $stmt->execute();
    $interview = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$interview) {
        die("Mülakat bulunamadı.");
    }

} catch (PDOException $e) {
    die("Sorgu hatası: " . $e->getMessage());
}

// Durum güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'update_status') {
            $new_status = $_POST['status'];
            $notes = $_POST['notes'];
            
            $query = "UPDATE interviews 
                     SET status = :status, 
                         notes = :notes,
                         updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':id', $interview_id);
            $stmt->execute();

            header("Location: interview_detail.php?id=" . $interview_id);
            exit;
        }
    } catch (PDOException $e) {
        $error = "Güncelleme hatası: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mülakat Detayı - Admin Paneli</title>
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
                            <a class="nav-link" href="candidates.php">
                                <i class="bi bi-people"></i> Adaylar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="applications.php">
                                <i class="bi bi-file-earmark-text"></i> Başvurular
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="bi bi-file-earmark-bar-graph"></i> Raporlar
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
                    <h2>Mülakat Detayı</h2>
                    <a href="reports.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Geri Dön
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Mülakat Bilgileri -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Mülakat Bilgileri</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Aday:</strong> <?php echo htmlspecialchars($interview['candidate_name']); ?></p>
                                        <p><strong>Pozisyon:</strong> <?php echo htmlspecialchars($interview['position_title']); ?></p>
                                        <p><strong>Tarih:</strong> <?php echo date('d.m.Y H:i', strtotime($interview['interview_date'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>E-posta:</strong> <a href="mailto:<?php echo htmlspecialchars($interview['candidate_email']); ?>"><?php echo htmlspecialchars($interview['candidate_email']); ?></a></p>
                                        <p><strong>Telefon:</strong> <a href="tel:<?php echo htmlspecialchars($interview['candidate_phone']); ?>"><?php echo htmlspecialchars($interview['candidate_phone']); ?></a></p>
                                        <p><strong>Tür:</strong> <?php echo $interview['interview_type'] == 'online' ? 'Online' : 'Yüz yüze'; ?></p>
                                    </div>
                                </div>

                                <?php if ($interview['interview_type'] == 'online'): ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-camera-video"></i>
                                        <strong>Online Mülakat Linki:</strong>
                                        <a href="<?php echo htmlspecialchars($interview['meeting_link']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($interview['meeting_link']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Durum Güncelleme -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Durum Güncelleme</h5>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_status">
                                    <div class="mb-3">
                                        <label class="form-label">Durum</label>
                                        <select name="status" class="form-select">
                                            <option value="pending" <?php echo $interview['status'] == 'pending' ? 'selected' : ''; ?>>Bekliyor</option>
                                            <option value="confirmed" <?php echo $interview['status'] == 'confirmed' ? 'selected' : ''; ?>>Onaylandı</option>
                                            <option value="completed" <?php echo $interview['status'] == 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                                            <option value="cancelled" <?php echo $interview['status'] == 'cancelled' ? 'selected' : ''; ?>>İptal Edildi</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notlar</label>
                                        <textarea name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($interview['notes']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Güncelle</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Durum Kartı -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Mevcut Durum</h5>
                                <?php
                                $statusClass = [
                                    'pending' => 'warning',
                                    'confirmed' => 'success',
                                    'completed' => 'info',
                                    'cancelled' => 'danger'
                                ];
                                $statusText = [
                                    'pending' => 'Bekliyor',
                                    'confirmed' => 'Onaylandı',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal Edildi'
                                ];
                                ?>
                                <div class="alert alert-<?php echo $statusClass[$interview['status']] ?? 'secondary'; ?>">
                                    <i class="bi bi-circle-fill"></i>
                                    <strong><?php echo $statusText[$interview['status']] ?? 'Bekliyor'; ?></strong>
                                </div>
                                <p><strong>Son Güncelleme:</strong> <?php echo date('d.m.Y H:i', strtotime($interview['updated_at'])); ?></p>
                            </div>
                        </div>

                        <!-- Başvuru Durumu -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Başvuru Durumu</h5>
                                <?php
                                $applicationStatusClass = [
                                    'pending' => 'warning',
                                    'in_review' => 'info',
                                    'accepted' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $applicationStatusText = [
                                    'pending' => 'Bekliyor',
                                    'in_review' => 'İncelemede',
                                    'accepted' => 'Kabul Edildi',
                                    'rejected' => 'Reddedildi'
                                ];
                                ?>
                                <div class="alert alert-<?php echo $applicationStatusClass[$interview['application_status']] ?? 'secondary'; ?>">
                                    <i class="bi bi-circle-fill"></i>
                                    <strong><?php echo $applicationStatusText[$interview['application_status']] ?? 'Bekliyor'; ?></strong>
                                </div>
                                <a href="application_detail.php?id=<?php echo $interview['application_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-text"></i> Başvuru Detayını Gör
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 