<?php
require_once 'config/database.php';

// Test sonuç ID'sini kontrol et
$test_result_id = $_GET['test_result_id'] ?? '';
if (!$test_result_id) {
    header('Location: index.php');
    exit;
}

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Test sonuç bilgilerini al
    $stmt = $db->prepare("
        SELECT tr.*, t.title as test_name, t.passing_score,
               a.id as application_id
        FROM test_results tr
        JOIN tests t ON tr.test_id = t.id
        JOIN applications a ON tr.application_id = a.id
        WHERE tr.id = ?
    ");
    $stmt->execute([$test_result_id]);
    $test_result = $stmt->fetch();

    if (!$test_result) {
        die("Test sonucu bulunamadı.");
    }

} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}

// Başlık ve stil dosyalarını ekle
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="card-title mb-4"><?php echo htmlspecialchars($test_result['test_name']); ?></h2>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>Test Başarıyla Tamamlandı!</h4>
                        <p class="mb-0">Testiniz sistem tarafından değerlendirilecektir.</p>
                    </div>
                    <div class="mt-4">
                        <a href="apply_step4.php?application_id=<?php echo $test_result['application_id']; ?>" 
                           class="btn btn-primary btn-lg">
                            Başvuru Sayfasına Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 