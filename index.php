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
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="mb-4"><?php echo $page_heading; ?></h2>
        </div>
    </div>
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
                        <div class="card h-100 position-card" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none;">
                            <div class="card-body d-flex flex-column" style="padding: 1.5rem;">
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
                                    <small>
                                        <i class="bi bi-file-text"></i> <?php echo $position['test_count']; ?> test
                                    </small>
                                </div>
                                <a href="apply_step1.php?position_id=<?php echo $position['id']; ?>" 
                                   class="btn btn-primary mt-3" style="border-radius: 8px; padding: 0.75rem; font-weight: 500; background-color: #4299E1; border: none; transition: all 0.2s ease;">
                                    <i class="fas fa-paper-plane me-2"></i>Başvur
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info" style="border-radius: 8px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">Şu anda açık pozisyon bulunmamaktadır.</div></div>';
            }
        } catch(PDOException $e) {
            echo '<div class="col-12"><div class="alert alert-danger" style="border-radius: 8px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">Bir hata oluştu: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 