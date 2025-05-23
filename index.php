<?php
$page_title = "İş İlanları - Kariyer Portalı";
$page_heading = "Açık Pozisyonlar";
require_once 'includes/header.php';
require_once 'config/database.php';

// Veritabanı bağlantısı
$database = new Database();
$db = $database->getConnection();
?>

<div class="container mt-4">
    <h2 class="mb-4"><?php echo $page_heading; ?></h2>
    <div class="row g-4" id="positions-container">
        <?php
        try {
            // Aktif pozisyonları getir
            $query = "SELECT p.*, 
                    (SELECT COUNT(*) FROM position_tests pt WHERE pt.position_id = p.id) as test_count
                    FROM positions p 
                    WHERE p.status = 'active' 
                    ORDER BY p.created_at DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($positions) > 0) {
                foreach ($positions as $position) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 position-card">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($position['title']); ?></h5>
                                <p class="card-text flex-grow-1">
                                    <?php 
                                    // Açıklama metnini kısalt
                                    $description = $position['description'];
                                    if (strlen($description) > 150) {
                                        $description = substr($description, 0, 147) . '...';
                                    }
                                    echo htmlspecialchars($description); 
                                    ?>
                                </p>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-file-text"></i> <?php echo $position['test_count']; ?> test
                                    </small>
                                </div>
                                <a href="apply_step1.php?position_id=<?php echo $position['id']; ?>" 
                                   class="btn btn-primary mt-3">
                                    <i class="bi bi-send"></i> Başvur
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info">Şu anda açık pozisyon bulunmamaktadır.</div></div>';
            }
        } catch(PDOException $e) {
            echo '<div class="col-12"><div class="alert alert-danger">Bir hata oluştu: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 