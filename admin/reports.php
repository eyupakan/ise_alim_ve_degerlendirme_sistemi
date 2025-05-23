<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Filtreleme parametrelerini al
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$position_id = isset($_GET['position_id']) ? $_GET['position_id'] : 0;

try {
    // Pozisyonları getir
    $query = "SELECT id, title FROM positions WHERE status = 'active' ORDER BY title";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $positions = $stmt->fetchAll();

    // Genel istatistikler
    $query = "SELECT 
              COUNT(*) as total_applications,
              COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted,
              COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
              COUNT(CASE WHEN status IN ('pending', 'in_review', 'submitted') THEN 1 END) as in_process,
              AVG(CASE WHEN status = 'accepted' THEN TIMESTAMPDIFF(DAY, created_at, updated_at) END) as avg_processing_time
              FROM applications a
              WHERE DATE(a.created_at) BETWEEN :start_date AND :end_date";
    
    if ($position_id > 0) {
        $query .= " AND a.position_id = :position_id";
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    if ($position_id > 0) {
        $stmt->bindParam(':position_id', $position_id);
    }
    $stmt->execute();
    $stats = $stmt->fetch();

    // Günlük başvuru trendi (son 30 gün)
    $query = "SELECT 
              DATE(a.created_at) as day,
              COUNT(*) as count,
              COUNT(CASE WHEN a.status = 'accepted' THEN 1 END) as accepted
              FROM applications a
              WHERE DATE(a.created_at) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
              AND DATE(a.created_at) <= CURDATE()";
    if ($position_id > 0) {
        $query .= " AND a.position_id = :position_id";
    }
    $query .= " GROUP BY DATE(a.created_at)
                ORDER BY day ASC";
    $stmt = $db->prepare($query);
    if ($position_id > 0) {
        $stmt->bindParam(':position_id', $position_id);
    }
    $stmt->execute();
    $monthly_trend = $stmt->fetchAll();

    // Pozisyon bazlı istatistikler
    $query = "SELECT 
              p.title,
              COUNT(a.id) as total_applications,
              COUNT(CASE WHEN a.status = 'accepted' THEN 1 END) as accepted,
              COUNT(CASE WHEN a.status = 'rejected' THEN 1 END) as rejected,
              AVG(CASE WHEN a.status = 'accepted' THEN TIMESTAMPDIFF(DAY, a.created_at, a.updated_at) END) as avg_processing_time
              FROM positions p
              LEFT JOIN applications a ON p.id = a.position_id 
              AND DATE(a.created_at) BETWEEN :start_date AND :end_date
              WHERE p.status = 'active'";
    
    if ($position_id > 0) {
        $query .= " AND p.id = :position_id";
    }
    
    $query .= " GROUP BY p.id, p.title
                ORDER BY total_applications DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    if ($position_id > 0) {
        $stmt->bindParam(':position_id', $position_id);
    }
    $stmt->execute();
    $position_stats = $stmt->fetchAll();

    // Test başarı oranları (tüm testler, tarih filtresi yok)
    $query = "SELECT t.id, t.title, 
              COUNT(tr.id) as total_attempts,
              AVG(tr.score) as average_score,
              COUNT(CASE WHEN tr.score >= 70 THEN 1 END) as passed_count,
              (SELECT COALESCE(SUM(points),0) FROM test_questions WHERE test_id = t.id) as max_points
              FROM tests t
              LEFT JOIN test_results tr ON t.id = tr.test_id AND tr.status = 'completed'
              GROUP BY t.id, t.title
              ORDER BY average_score DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $test_stats = $stmt->fetchAll();

    // Yaklaşan mülakatlar
    $query = "SELECT 
              i.interview_date,
              CONCAT(c.first_name, ' ', c.last_name) as candidate_name,
              p.title as position_title,
              i.status,
              i.interview_type
              FROM interviews i
              JOIN applications a ON i.application_id = a.id
              JOIN candidates c ON a.candidate_id = c.id
              JOIN positions p ON a.position_id = p.id
              WHERE i.interview_date >= CURRENT_DATE()
              ORDER BY i.interview_date ASC
              LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $upcoming_interviews = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Sorgu hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlar - Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.js'></script>
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
        .stat-card {
            border-radius: 15px;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        #calendar {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .fc-event {
            cursor: pointer;
        }
        .interview-pending {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .interview-confirmed {
            background-color: #28a745;
            border-color: #28a745;
        }
        .interview-completed {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .interview-cancelled {
            background-color: #dc3545;
            border-color: #dc3545;
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
                            <a class="nav-link active" href="reports.php">
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
                    <div class="d-flex align-items-center">
                        <a href="dashboard.php" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left"></i> Geri
                        </a>
                        <h2 class="mb-0">Raporlar</h2>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInterviewModal">
                            <i class="bi bi-plus-circle"></i> Mülakat Ekle
                        </button>
                    </div>
                </div>

                <!-- Filtreler -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Başlangıç Tarihi</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bitiş Tarihi</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pozisyon</label>
                                <select class="form-select" name="position_id">
                                    <option value="0">Tümü</option>
                                    <?php foreach ($positions as $position): ?>
                                        <option value="<?php echo $position['id']; ?>" 
                                                <?php echo $position_id == $position['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($position['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i> Filtrele
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Genel İstatistikler -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Toplam Başvuru</h5>
                                <h2><?php echo $stats['total_applications']; ?></h2>
                                <p class="mb-0">Seçilen dönemdeki toplam başvuru sayısı</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Kabul Oranı</h5>
                                <h2>
                                    <?php 
                                    $acceptance_rate = $stats['total_applications'] > 0 ? 
                                        round(($stats['accepted'] / $stats['total_applications']) * 100) : 0;
                                    echo $acceptance_rate;
                                    ?>%
                                </h2>
                                <p class="mb-0">Kabul edilen başvuruların oranı</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Ort. İşlem Süresi</h5>
                                <h2><?php echo round($stats['avg_processing_time'] ?? 0); ?> gün</h2>
                                <p class="mb-0">Kabul edilen başvuruların ortalama işlem süresi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Bekleyen Başvuru</h5>
                                <h2><?php echo $stats['in_process']; ?></h2>
                                <p class="mb-0">İnceleme bekleyen başvuru sayısı</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Aylık Başvuru Trendi -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Aylık Başvuru Trendi</h5>
                                <canvas id="monthlyTrend"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Pozisyon Bazlı İstatistikler -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pozisyon Bazlı İstatistikler</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Pozisyon</th>
                                                <th>Toplam</th>
                                                <th>Kabul</th>
                                                <th>Red</th>
                                                <th>Ort. Süre</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($position_stats as $stat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($stat['title']); ?></td>
                                                    <td><?php echo $stat['total_applications']; ?></td>
                                                    <td>
                                                        <?php 
                                                        $ratio = $stat['total_applications'] > 0 ? 
                                                            round(($stat['accepted'] / $stat['total_applications']) * 100) : 0;
                                                        ?>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-success" role="progressbar" 
                                                                 style="width: <?php echo $ratio; ?>%">
                                                                <?php echo $ratio; ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $reject_ratio = $stat['total_applications'] > 0 ? 
                                                            round(($stat['rejected'] / $stat['total_applications']) * 100) : 0;
                                                        ?>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-danger" role="progressbar" 
                                                                 style="width: <?php echo $reject_ratio; ?>%">
                                                                <?php echo $reject_ratio; ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo round($stat['avg_processing_time'] ?? 0); ?> gün</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Başarı Oranları -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Test Başarı Oranları</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Ort. Yüzde</th>
                                        <th>Max Puan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($test_stats)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Bu dönemde test sonucu bulunamadı.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($test_stats as $test): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($test['title']); ?></td>
                                                <td><?php echo is_null($test['average_score']) ? '-' : round($test['average_score'], 1) . '%'; ?></td>
                                                <td><?php echo $test['max_points']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Takvim -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Mülakat Takvimi</h5>
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

                <!-- Yaklaşan Mülakatlar -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Yaklaşan Mülakatlar</h5>
                            <div class="list-group">
                                <?php foreach ($upcoming_interviews as $interview): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($interview['candidate_name']); ?></h6>
                                            <small><?php echo date('d.m.Y H:i', strtotime($interview['interview_date'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($interview['position_title']); ?></p>
                                        <small>
                                            <?php 
                                            $statusClass = [
                                                'pending' => 'text-warning',
                                                'confirmed' => 'text-success',
                                                'completed' => 'text-info',
                                                'cancelled' => 'text-danger'
                                            ];
                                            $statusText = [
                                                'pending' => 'Bekliyor',
                                                'confirmed' => 'Onaylandı',
                                                'completed' => 'Tamamlandı',
                                                'cancelled' => 'İptal Edildi'
                                            ];
                                            ?>
                                            <span class="<?php echo $statusClass[$interview['status']] ?? ''; ?>">
                                                <i class="bi bi-circle-fill"></i>
                                                <?php echo $statusText[$interview['status']] ?? 'Bekliyor'; ?>
                                            </span>
                                            <span class="ms-2">
                                                <i class="bi bi-camera-video"></i>
                                                <?php echo $interview['interview_type'] == 'online' ? 'Online' : 'Yüz yüze'; ?>
                                            </span>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($upcoming_interviews)): ?>
                                    <div class="list-group-item">
                                        <p class="mb-0 text-muted">Yaklaşan mülakat bulunmuyor.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mülakat Ekleme Modal -->
    <div class="modal fade" id="addInterviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Mülakat Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_interview.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Başvuru</label>
                            <select name="application_id" class="form-select" required>
                                <option value="">Başvuru Seçin</option>
                                <?php
                                try {
                                    $query = "SELECT 
                                            a.id,
                                            CONCAT(c.first_name, ' ', c.last_name, ' - ', p.title) as title,
                                            a.created_at,
                                            p.title as position_title,
                                            a.status
                                            FROM applications a 
                                            JOIN candidates c ON a.candidate_id = c.id 
                                            JOIN positions p ON a.position_id = p.id 
                                            WHERE a.status IN ('pending', 'in_review', 'accepted')
                                            AND (
                                                NOT EXISTS (
                                                    SELECT 1 FROM interviews i 
                                                    WHERE i.application_id = a.id 
                                                    AND i.status NOT IN ('cancelled')
                                                )
                                                OR 
                                                a.status = 'accepted'
                                            )
                                            ORDER BY a.created_at DESC";
                                    
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $statusText = '';
                                            switch($row['status']) {
                                                case 'pending':
                                                    $statusText = '(Beklemede)';
                                                    break;
                                                case 'in_review':
                                                    $statusText = '(İncelemede)';
                                                    break;
                                                case 'accepted':
                                                    $statusText = '(Kabul Edildi)';
                                                    break;
                                            }
                                            $optionText = htmlspecialchars($row['title']) . ' ' . $statusText . ' - ' . 
                                                        date('d.m.Y', strtotime($row['created_at']));
                                            echo '<option value="' . $row['id'] . '">' . $optionText . '</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>Uygun başvuru bulunamadı</option>';
                                    }
                                } catch (PDOException $e) {
                                    echo '<option value="" disabled>Başvurular yüklenirken hata oluştu: ' . $e->getMessage() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mülakat Tarihi</label>
                            <input type="datetime-local" name="interview_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mülakat Türü</label>
                            <select name="interview_type" class="form-select" required>
                                <option value="online">Online</option>
                                <option value="in_person">Yüz yüze</option>
                            </select>
                        </div>
                        <div class="mb-3" id="meetingLinkDiv">
                            <label class="form-label">Toplantı Linki</label>
                            <input type="url" name="meeting_link" class="form-control">
                        </div>
                        <div class="mb-3" id="locationDiv" style="display:none;">
                            <label class="form-label">Konum</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notlar</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aylık başvuru trendi grafiği --> Günlük trend
        const monthlyTrendCtx = document.getElementById('monthlyTrend').getContext('2d');
        new Chart(monthlyTrendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_trend, 'day')); ?>,
                datasets: [{
                    label: 'Toplam Başvuru',
                    data: <?php echo json_encode(array_column($monthly_trend, 'count')); ?>,
                    borderColor: '#007bff',
                    tension: 0.1
                }, {
                    label: 'Kabul Edilen',
                    data: <?php echo json_encode(array_column($monthly_trend, 'accepted')); ?>,
                    borderColor: '#28a745',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'tr',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: 'get_interviews.php', // AJAX ile mülakatları çekecek endpoint
                eventClick: function(info) {
                    // Mülakat detaylarını göster
                    window.location.href = 'interview_detail.php?id=' + info.event.id;
                },
                eventClassNames: function(arg) {
                    return ['interview-' + arg.event.extendedProps.status];
                }
            });
            calendar.render();
        });

        // Mülakat türü değiştiğinde ilgili alanları göster/gizle
        document.querySelector('select[name="interview_type"]').addEventListener('change', function() {
            const meetingLinkDiv = document.getElementById('meetingLinkDiv');
            const locationDiv = document.getElementById('locationDiv');
            
            if (this.value === 'online') {
                meetingLinkDiv.style.display = 'block';
                locationDiv.style.display = 'none';
            } else {
                meetingLinkDiv.style.display = 'none';
                locationDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html> 