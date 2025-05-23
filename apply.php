<?php
require_once 'config/database.php';

// Pozisyon ID kontrolü
if (!isset($_GET['position_id'])) {
    header('Location: index.php');
    exit;
}

$position_id = $_GET['position_id'];

try {
    // Pozisyon bilgilerini al
    $stmt = $conn->prepare("SELECT * FROM positions WHERE id = ? AND status = 'active'");
    $stmt->execute([$position_id]);
    $position = $stmt->fetch();

    if (!$position) {
        header('Location: index.php');
        exit;
    }

    $page_title = "Başvuru - " . $position['title'];
    require_once 'includes/header.php';
} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0"><?php echo htmlspecialchars($position['title']); ?></h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Pozisyon Detayları</h5>
                    <p><?php echo nl2br(htmlspecialchars($position['description'])); ?></p>
                    
                    <?php if (!empty($position['requirements'])): ?>
                    <h5>Gereksinimler</h5>
                    <p><?php echo nl2br(htmlspecialchars($position['requirements'])); ?></p>
                    <?php endif; ?>
                </div>

                <form action="process_application.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="position_id" value="<?php echo $position_id; ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <label for="cv" class="form-label">CV (PDF)</label>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="cover_letter" class="form-label">Ön Yazı</label>
                        <textarea class="form-control" id="cover_letter" name="cover_letter" rows="5"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Başvuruyu Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 