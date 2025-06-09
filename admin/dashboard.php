<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// İstatistik verilerini al
try {
    // Aktif pozisyonlar
    $query = "SELECT COUNT(*) as count FROM positions WHERE status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $active_positions = $stmt->fetch()['count'];

    // Toplam test
    $query = "SELECT COUNT(*) as count FROM tests";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $total_tests = $stmt->fetch()['count'];

    // Bekleyen başvurular
    $query = "SELECT COUNT(*) as count FROM applications WHERE status = 'pending' OR status = 'in_review' OR status = 'submitted'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $pending_applications = $stmt->fetch()['count'];

    // Toplam aday
    $query = "SELECT COUNT(DISTINCT c.id) as count 
              FROM candidates c 
              INNER JOIN applications a ON c.id = a.candidate_id 
              WHERE a.status != 'deleted'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $total_candidates = $stmt->fetch()['count'];

    // Son başvurular
    $query = "SELECT a.*, c.first_name, c.last_name, p.title as position_title 
             FROM applications a 
             JOIN candidates c ON a.candidate_id = c.id 
             JOIN positions p ON a.position_id = p.id 
             ORDER BY a.created_at DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $recent_applications = $stmt->fetchAll();

    // Pozisyon bazlı başvuru sayıları
    $query = "SELECT p.title, COUNT(a.id) as application_count,
              COUNT(CASE WHEN a.status = 'accepted' THEN 1 END) as accepted_count
              FROM positions p
              LEFT JOIN applications a ON p.id = a.position_id
              WHERE p.status = 'active'
              GROUP BY p.id, p.title
              ORDER BY application_count DESC
              LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $position_stats = $stmt->fetchAll();

    // Bu ayki başvuru istatistikleri
    $query = "SELECT 
              COUNT(*) as total_applications,
              COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted,
              COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
              COUNT(CASE WHEN status = 'pending' OR status = 'in_review' OR status = 'submitted' THEN 1 END) as in_process
              FROM applications 
              WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $monthly_stats = $stmt->fetch();

    // Test başarı oranları (max puan eklendi)
    $query = "SELECT t.id, t.title, 
              COUNT(tr.id) as total_attempts,
              AVG(tr.score) as average_score,
              COUNT(CASE WHEN tr.score >= 70 THEN 1 END) as passed_count,
              (SELECT COALESCE(SUM(points),0) FROM test_questions WHERE test_id = t.id) as max_points
              FROM tests t
              LEFT JOIN test_results tr ON t.id = tr.test_id
              WHERE tr.status = 'completed'
              GROUP BY t.id, t.title
              ORDER BY average_score DESC
              LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $test_stats = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Sorgu hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .quick-action {
            text-decoration: none;
            color: inherit;
        }

        .quick-action:hover {
            transform: translateY(-3px);
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
                            <a class="nav-link active" href="dashboard.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Dashboard</h2>
                </div>

                <!-- Hızlı İstatistikler -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Aktif Pozisyonlar</h5>
                                <h2><?php echo $active_positions; ?></h2>
                                <p class="mb-0"><i class="bi bi-briefcase"></i> Açık Pozisyon</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Toplam Aday</h5>
                                <h2><?php echo $total_candidates; ?></h2>
                                <p class="mb-0"><i class="bi bi-people"></i> Kayıtlı Aday</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Bekleyen Başvuru</h5>
                                <h2><?php echo $pending_applications; ?></h2>
                                <p class="mb-0"><i class="bi bi-hourglass-split"></i> İnceleme Bekliyor</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Toplam Test</h5>
                                <h2><?php echo $total_tests; ?></h2>
                                <p class="mb-0"><i class="bi bi-file-text"></i> Aktif Test</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Erişim Butonları -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Hızlı Erişim</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <a href="positions.php?action=new" class="quick-action">
                                            <div class="card text-center p-3">
                                                <i class="bi bi-plus-circle fs-1 text-primary"></i>
                                                <div class="mt-2">Yeni Pozisyon Aç</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="tests.php?action=new" class="quick-action">
                                            <div class="card text-center p-3">
                                                <i class="bi bi-file-plus fs-1 text-success"></i>
                                                <div class="mt-2">Yeni Test Oluştur</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="applications.php?status=pending" class="quick-action">
                                            <div class="card text-center p-3">
                                                <i class="bi bi-inbox fs-1 text-warning"></i>
                                                <div class="mt-2">Bekleyen Başvurular</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="reports.php" class="quick-action">
                                            <div class="card text-center p-3">
                                                <i class="bi bi-file-earmark-bar-graph fs-1 text-info"></i>
                                                <div class="mt-2">Raporlar</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Bu Ayki İstatistikler -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Bu Ayki Başvuru İstatistikleri</h5>
                                <canvas id="monthlyStats"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Pozisyon Bazlı Başvurular -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pozisyon Bazlı Başvurular</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Pozisyon</th>
                                                <th>Toplam Başvuru</th>
                                                <th>Kabul Edilen</th>
                                                <th>Oran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($position_stats as $stat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($stat['title']); ?></td>
                                                    <td><?php echo $stat['application_count']; ?></td>
                                                    <td><?php echo $stat['accepted_count']; ?></td>
                                                    <td>
                                                        <?php 
                                                        $ratio = $stat['application_count'] > 0 ? 
                                                            round(($stat['accepted_count'] / $stat['application_count']) * 100) : 0;
                                                        ?>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-success" role="progressbar" 
                                                                 style="width: <?php echo $ratio; ?>%">
                                                                <?php echo $ratio; ?>%
                                                            </div>
                                                        </div>
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

                <div class="row">
                    <!-- Test Başarı Oranları -->
                    <div class="col-md-6">
                        <div class="card h-100">
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
                                            <?php foreach ($test_stats as $test): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($test['title']); ?></td>
                                                    <td><?php echo round($test['average_score'], 1); ?>%</td>
                                                    <td><?php echo $test['max_points']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Son Başvurular -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Son Başvurular</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Aday</th>
                                                <th>Pozisyon</th>
                                                <th>Durum</th>
                                                <th>Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_applications as $app): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($app['position_title']); ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = [
                                                            'pending' => 'secondary',
                                                            'in_review' => 'info',
                                                            'accepted' => 'success',
                                                            'rejected' => 'danger'
                                                        ];
                                                        $statusText = [
                                                            'pending' => 'Bekliyor',
                                                            'in_review' => 'İncelemede',
                                                            'accepted' => 'Kabul',
                                                            'rejected' => 'Red'
                                                        ];
                                                        ?>
                                                        <span class="badge bg-<?php echo $statusClass[$app['status']] ?? 'secondary'; ?>">
                                                            <?php echo $statusText[$app['status']] ?? 'Bekliyor'; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d.m.Y', strtotime($app['created_at'])); ?></td>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bu ayki istatistikler grafiği
        const monthlyStatsCtx = document.getElementById('monthlyStats').getContext('2d');
        new Chart(monthlyStatsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Kabul Edilen', 'Reddedilen', 'İşlemde'],
                datasets: [{
                    data: [
                        <?php echo $monthly_stats['accepted']; ?>,
                        <?php echo $monthly_stats['rejected']; ?>,
                        <?php echo $monthly_stats['in_process']; ?>
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#dc3545',
                        '#ffc107'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>