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

<div class="row justify-content-center" style="background-color: #F7F9FC; min-height: 100vh; padding: 2rem 0;">
    <div class="col-md-8">
        <div class="card" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none;">
            <div class="card-header" style="border-bottom: 1px solid #E9ECEF; border-radius: 12px 12px 0 0;">
                <h3 class="card-title mb-0"><?php echo htmlspecialchars($position['title']); ?></h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5 style="font-weight: 600; margin-bottom: 1rem;">Pozisyon Detayları</h5>
                    <p style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($position['description'])); ?></p>
                    
                    <?php if (!empty($position['requirements'])): ?>
                    <h5 style="font-weight: 600; margin-top: 2rem; margin-bottom: 1rem;">Gereksinimler</h5>
                    <p style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($position['requirements'])); ?></p>
                    <?php endif; ?>
                </div>

                <form action="process_application.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="position_id" value="<?php echo $position_id; ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label" style="font-weight: 500;">Ad Soyad</label>
                        <input type="text" class="form-control" id="name" name="name" required style="border-radius: 8px; border: 1px solid #E2E8F0;">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label" style="font-weight: 500;">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email" required style="border-radius: 8px; border: 1px solid #E2E8F0;">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label" style="font-weight: 500;">Telefon</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required style="border-radius: 8px; border: 1px solid #E2E8F0;">
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label" style="font-weight: 500;">Cinsiyet</label>
                        <select class="form-select" id="gender" name="gender" required style="border-radius: 8px; border: 1px solid #E2E8F0;">
                            <option value="">Seçiniz</option>
                            <option value="male">Erkek</option>
                            <option value="female">Kadın</option>
                            <option value="other">Diğer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cv" class="form-label" style="font-weight: 500;">CV (PDF)</label>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required style="border-radius: 8px; border: 1px solid #E2E8F0;">
                    </div>

                    <div class="mb-3">
                        <label for="cover_letter" class="form-label" style="font-weight: 500;">Ön Yazı</label>
                        <textarea class="form-control" id="cover_letter" name="cover_letter" rows="5" style="border-radius: 8px; border: 1px solid #E2E8F0;"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 0.75rem; font-weight: 500; background-color: #4299E1; border: none; transition: background-color 0.2s;">Başvuruyu Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 