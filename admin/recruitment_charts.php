<?php
// Hata raporlamayı aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);

// AJAX isteği kontrolü
if (isset($_GET['type']) && isset($_GET['position_id'])) {
    // Veritabanı bağlantısı
    $host = 'localhost';
    $db = 'recruitment_system';
    $user = 'root';
    $pass = 'root';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()]);
        exit;
    }

    $type = $_GET['type'];
    $positionId = $_GET['position_id'];

    header('Content-Type: application/json');

    try {
        if ($type === 'status') {
            // Başvuru durumu dağılımı
            $sql = "SELECT status, COUNT(*) as count 
                    FROM applications 
                    WHERE position_id = :position_id 
                    GROUP BY status";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['position_id' => $positionId]);
            $data = $stmt->fetchAll();

            // Durumları Türkçeleştir
            $data = array_map(function ($item) {
                $translations = [
                    'draft' => 'Taslak',
                    'submitted' => 'Gönderildi',
                    'in_review' => 'İncelemede',
                    'in_test' => 'Test Aşamasında',
                    'rejected' => 'Reddedildi',
                    'accepted' => 'Kabul Edildi'
                ];
                $item['status'] = $translations[$item['status']] ?? $item['status'];
                return $item;
            }, $data);

            echo json_encode($data);

        } elseif ($type === 'points') {
            // Puan dağılımı için min-max değerleri
            $sql_minmax = "SELECT 
                          MIN(NULLIF(total_points, 0)) as min_points,
                          MAX(total_points) as max_points,
                          COUNT(*) as total_applications
                          FROM applications 
                          WHERE position_id = :position_id 
                          AND total_points IS NOT NULL";

            $stmt = $pdo->prepare($sql_minmax);
            $stmt->execute(['position_id' => $positionId]);
            $minmax = $stmt->fetch();

            if ($minmax['total_applications'] == 0) {
                echo json_encode([]);
                exit;
            }

            $min_points = floor($minmax['min_points'] ?? 0);
            $max_points = ceil($minmax['max_points'] ?? 0);

            // Puan dağılımı verilerini al
            $sql = "SELECT 
                    total_points,
                    COUNT(*) as count
                    FROM applications 
                    WHERE position_id = :position_id 
                    AND total_points IS NOT NULL
                    GROUP BY total_points
                    ORDER BY total_points";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['position_id' => $positionId]);
            $raw_data = $stmt->fetchAll();

            // Puan aralıklarını oluştur
            $num_ranges = 10;
            $range_size = max(ceil(($max_points - $min_points) / $num_ranges), 1);
            $ranges = [];
            $current_min = $min_points;

            for ($i = 0; $i < $num_ranges; $i++) {
                $current_max = $current_min + $range_size;
                $range_key = $i === ($num_ranges - 1) ? "$current_min+" : "$current_min-$current_max";
                $ranges[$range_key] = 0;
                $current_min = $current_max;
            }

            // Verileri aralıklara dağıt
            foreach ($raw_data as $row) {
                $point = $row['total_points'];
                $count = $row['count'];

                foreach ($ranges as $range => $value) {
                    if (strpos($range, '+') !== false) {
                        $min = (int) str_replace('+', '', $range);
                        if ($point >= $min) {
                            $ranges[$range] += $count;
                            break;
                        }
                    } else {
                        list($min, $max) = array_map('intval', explode('-', $range));
                        if ($point >= $min && $point < $max) {
                            $ranges[$range] += $count;
                            break;
                        }
                    }
                }
            }

            $result = [];
            foreach ($ranges as $range => $count) {
                $result[] = [
                    'point_range' => $range,
                    'count' => $count
                ];
            }

            echo json_encode($result);
        }
    } catch (\PDOException $e) {
        echo json_encode(['error' => 'Sorgu hatası: ' . $e->getMessage()]);
    }
    exit;
}

// Ana sayfa içeriği
try {
    // Veritabanı bağlantısı
    $host = 'localhost';
    $db = 'recruitment_system';
    $user = 'root';
    $pass = 'root';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    // Genel başvuru durumu dağılımı
    $sql_status = "SELECT status, COUNT(*) as count FROM applications GROUP BY status";
    $status_data = $pdo->query($sql_status)->fetchAll();

    // Pozisyon başına ortalama başvuru ve test sonuçları
    $sql_position_stats = "SELECT 
        p.id,
        p.title as position_name,
        COUNT(DISTINCT a.id) as application_count,
        COALESCE(AVG(NULLIF(tr.score, 0)), 0) as avg_test_score
        FROM positions p
        LEFT JOIN applications a ON p.id = a.position_id
        LEFT JOIN test_results tr ON a.id = tr.application_id
        GROUP BY p.id, p.title";
    $position_stats = $pdo->query($sql_position_stats)->fetchAll();

    // Tüm pozisyonlar
    $sql_positions = "SELECT id, title FROM positions ORDER BY title";
    $positions_list = $pdo->query($sql_positions)->fetchAll();

    // Durumları Türkçeleştir
    $status_data = array_map(function ($item) {
        $translations = [
            'draft' => 'Taslak',
            'submitted' => 'Gönderildi',
            'in_review' => 'İncelemede',
            'in_test' => 'Test Aşamasında',
            'rejected' => 'Reddedildi',
            'accepted' => 'Kabul Edildi'
        ];
        $item['status'] = $translations[$item['status']] ?? $item['status'];
        return $item;
    }, $status_data);

} catch (\PDOException $e) {
    $error_message = "Veritabanı hatası: " . $e->getMessage();
    $status_data = [];
    $position_stats = [];
    $positions_list = [];
}
?>

<!-- İstatistik Grafikleri -->
<div class="row">
    <h2 class="text-center mb-4">Genel İstatistikler</h2>

    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <canvas id="positionStatsChart"></canvas>
        </div>
    </div>
</div>

<!-- Pozisyon Bazlı İstatistikler -->
<div class="row mt-4">
    <h2 class="text-center mb-4">Pozisyon Bazlı İstatistikler</h2>

    <div class="col-md-6 offset-md-3 mb-4">
        <select id="positionSelect" class="form-select">
            <option value="">Pozisyon Seçiniz</option>
            <?php foreach ($positions_list as $position): ?>
                <option value="<?php echo htmlspecialchars($position['id']); ?>">
                    <?php echo htmlspecialchars($position['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div id="selectedPositionStats" style="display: none;">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Toplam Başvuru</h5>
                    <p class="card-text" id="totalApplications">-</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Ortalama Test Puanı</h5>
                    <p class="card-text" id="avgTestScore">-</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Başvuru Durumu</h5>
                    <p class="card-text" id="applicationStatus">-</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <canvas id="selectedStatusChart"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <canvas id="selectedPointsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        margin-bottom: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // GENEL GRAFİKLER

        // Başvuru Durumu Grafiği
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($status_data, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($status_data, 'count')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Genel Başvuru Durumu Dağılımı',
                        font: { size: 16 }
                    },
                    legend: { position: 'bottom' }
                }
            }
        });

        // Pozisyon İstatistikleri Grafiği
        new Chart(document.getElementById('positionStatsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($position_stats, 'position_name')); ?>,
                datasets: [{
                    label: 'Başvuru Sayısı',
                    data: <?php echo json_encode(array_column($position_stats, 'application_count')); ?>,
                    backgroundColor: '#36A2EB',
                    yAxisID: 'y'
                }, {
                    label: 'Ortalama Test Puanı',
                    data: <?php echo json_encode(array_column($position_stats, 'avg_test_score')); ?>,
                    backgroundColor: '#FF6384',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Pozisyon Bazlı İstatistikler',
                        font: { size: 16 }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Başvuru Sayısı'
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Ortalama Test Puanı'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // POZİSYON SEÇİMİ İŞLEMLERİ
        let selectedStatusChart = null;
        let selectedPointsChart = null;
        const positionSelect = document.getElementById('positionSelect');
        const statsDiv = document.getElementById('selectedPositionStats');

        async function updatePositionStats(positionId) {
            if (!positionId) {
                statsDiv.style.display = 'none';
                return;
            }

            try {
                // Durum dağılımı verilerini al
                const statusResponse = await fetch(`recruitment_charts.php?type=status&position_id=${positionId}`);
                const statusData = await statusResponse.json();

                if (statusData.error) {
                    throw new Error(statusData.error);
                }

                // Puan dağılımı verilerini al
                const pointsResponse = await fetch(`recruitment_charts.php?type=points&position_id=${positionId}`);
                const pointsData = await pointsResponse.json();

                if (pointsData.error) {
                    throw new Error(pointsData.error);
                }

                statsDiv.style.display = 'block';

                // Mevcut grafikleri temizle
                if (selectedStatusChart) {
                    selectedStatusChart.destroy();
                }
                if (selectedPointsChart) {
                    selectedPointsChart.destroy();
                }

                // Durum dağılımı grafiği
                selectedStatusChart = new Chart(document.getElementById('selectedStatusChart'), {
                    type: 'pie',
                    data: {
                        labels: statusData.map(item => item.status),
                        datasets: [{
                            data: statusData.map(item => item.count),
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Pozisyon Başvuru Durumu Dağılımı',
                                font: { size: 16 }
                            },
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // Puan dağılımı grafiği
                selectedPointsChart = new Chart(document.getElementById('selectedPointsChart'), {
                    type: 'bar',
                    data: {
                        labels: pointsData.map(item => item.point_range),
                        datasets: [{
                            label: 'Başvuru Sayısı',
                            data: pointsData.map(item => item.count),
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Puan Dağılımı',
                                font: { size: 16 }
                            },
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Başvuru Sayısı'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Puan Aralığı'
                                }
                            }
                        }
                    }
                });

                // Özet bilgileri güncelle
                const totalApplications = statusData.reduce((sum, item) => sum + item.count, 0);
                document.getElementById('totalApplications').textContent = totalApplications;

                const avgScore = pointsData.reduce((sum, item) => {
                    const [min] = item.point_range.split('-');
                    return sum + (parseInt(min) * item.count);
                }, 0) / totalApplications;
                document.getElementById('avgTestScore').textContent = avgScore.toFixed(2);

                const applicationStatus = statusData.map(item =>
                    `${item.status}: ${item.count}`
                ).join(', ');
                document.getElementById('applicationStatus').textContent = applicationStatus;

            } catch (error) {
                console.error('Veri alma hatası:', error);
                alert('Veriler alınırken bir hata oluştu: ' + error.message);
            }
        }

        positionSelect.addEventListener('change', function () {
            updatePositionStats(this.value);
        });
    });
</script>