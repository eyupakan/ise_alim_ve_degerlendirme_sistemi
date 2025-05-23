<?php
require_once 'config/database.php';

// Başvuru ve test ID kontrolü
if (!isset($_GET['application_id']) || !isset($_GET['test_id'])) {
    header('Location: index.php');
    exit;
}

$application_id = $_GET['application_id'];
$test_id = $_GET['test_id'];

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru ve test bilgilerini al
    $stmt = $db->prepare("
        SELECT a.*, p.title as position_title, t.*, pt.required
        FROM applications a 
        JOIN positions p ON a.position_id = p.id 
        JOIN position_tests pt ON p.id = pt.position_id
        JOIN tests t ON pt.test_id = t.id
        WHERE a.id = ? AND t.id = ? AND t.status = 'active'
    ");
    $stmt->execute([$application_id, $test_id]);
    $test_info = $stmt->fetch();

    if (!$test_info) {
        die("Geçersiz test veya başvuru.");
    }

    // Test sonuç kaydını kontrol et
    $stmt = $db->prepare("
        SELECT * FROM test_results 
        WHERE application_id = ? AND test_id = ?
    ");
    $stmt->execute([$application_id, $test_id]);
    $test_result = $stmt->fetch();

    // Eğer test daha önce tamamlanmışsa
    if ($test_result && $test_result['status'] === 'completed') {
        $_SESSION['error'] = "Bu testi daha önce tamamladınız.";
        header("Location: apply_step4.php?application_id=" . $application_id);
        exit;
    }

    // Test sorularını al
    $stmt = $db->prepare("
        SELECT q.*, GROUP_CONCAT(
            CONCAT(o.id, ':::', o.option_text)
            ORDER BY o.order_number
            SEPARATOR '|||'
        ) as options
        FROM test_questions q
        LEFT JOIN question_options o ON q.id = o.question_id
        WHERE q.test_id = ?
        GROUP BY q.id
        ORDER BY q.order_number
    ");
    $stmt->execute([$test_id]);
    $questions = $stmt->fetchAll();

    // Yeni test başlatılıyorsa
    if (!$test_result) {
        $stmt = $db->prepare("
            INSERT INTO test_results (
                application_id, test_id, status, start_time
            ) VALUES (?, ?, 'in_progress', NOW())
        ");
        $stmt->execute([$application_id, $test_id]);
        $test_result_id = $db->lastInsertId();
    } else {
        $test_result_id = $test_result['id'];
    }

    $page_title = "Test - " . $test_info['title'];
    require_once 'includes/header.php';
} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><?php echo htmlspecialchars($test_info['title']); ?></h3>
                <div id="timer" class="text-danger fw-bold"></div>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <ul class="mb-0">
                        <li>Test süresi: <?php echo $test_info['time_limit']; ?> dakika</li>
                        <li>Toplam soru sayısı: <?php echo count($questions); ?></li>
                        <li>Geçme puanı: <?php echo $test_info['passing_score']; ?>%</li>
                    </ul>
                </div>

                <form id="testForm" action="process_test.php" method="POST">
                    <input type="hidden" name="test_result_id" value="<?php echo $test_result_id; ?>">
                    <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                    <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
                    
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question-item border rounded p-3 mb-4">
                            <h5 class="mb-3">
                                Soru <?php echo $index + 1; ?>
                            </h5>
                            <p class="mb-3"><?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>

                            <?php if ($question['question_type'] === 'multiple_choice'): ?>
                                <?php 
                                $options = explode('|||', $question['options']);
                                foreach ($options as $option):
                                    list($option_id, $option_text) = explode(':::', $option);
                                ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?php echo $question['id']; ?>]" 
                                               value="<?php echo $option_id; ?>" required>
                                        <label class="form-check-label">
                                            <?php echo htmlspecialchars($option_text); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>

                            <?php elseif ($question['question_type'] === 'true_false'): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" 
                                           name="answers[<?php echo $question['id']; ?>]" 
                                           value="true" required>
                                    <label class="form-check-label">Doğru</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" 
                                           name="answers[<?php echo $question['id']; ?>]" 
                                           value="false" required>
                                    <label class="form-check-label">Yanlış</label>
                                </div>

                            <?php else: ?>
                                <textarea class="form-control" 
                                          name="answers[<?php echo $question['id']; ?>]" 
                                          rows="3" required></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Testi Tamamla</button>
                        <a href="apply_step4.php?application_id=<?php echo $application_id; ?>" 
                           class="btn btn-light" 
                           onclick="return confirm('Testi iptal etmek istediğinize emin misiniz?')">
                            İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Test süresini ayarla
const timeLimit = <?php echo $test_info['time_limit']; ?> * 60; // dakikayı saniyeye çevir
let timeLeft = timeLimit;

// Timer'ı güncelle
function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer').textContent = 
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeLeft === 0) {
        document.getElementById('testForm').submit();
    } else {
        timeLeft--;
    }
}

// Her saniye timer'ı güncelle
setInterval(updateTimer, 1000);
updateTimer();

// Sayfa yenilenirse veya kapatılırsa uyarı ver
window.onbeforeunload = function() {
    return "Sayfadan ayrılırsanız test iptal edilecektir. Devam etmek istiyor musunuz?";
};

// Form gönderilirken uyarıyı kaldır
document.getElementById('testForm').onsubmit = function() {
    window.onbeforeunload = null;
};
</script>

<?php require_once 'includes/footer.php'; ?> 