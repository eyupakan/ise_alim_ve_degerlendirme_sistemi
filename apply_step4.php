<?php
require_once 'config/database.php';

// Başvuru ID kontrolü
if (!isset($_GET['application_id'])) {
    header('Location: index.php');
    exit;
}

$application_id = $_GET['application_id'];

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Başvuru ve pozisyon bilgilerini al
    $stmt = $db->prepare("
        SELECT a.*, p.title as position_title, c.first_name, c.last_name 
        FROM applications a 
        JOIN positions p ON a.position_id = p.id 
        JOIN candidates c ON a.candidate_id = c.id 
        WHERE a.id = ? AND a.current_step = 4
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        header('Location: index.php');
        exit;
    }

    // Pozisyona ait testleri al
    $stmt = $db->prepare("
        SELECT t.*, pt.required,
        (SELECT COUNT(*) FROM test_results tr 
         WHERE tr.test_id = t.id 
         AND tr.application_id = ? 
         AND tr.status = 'completed') as is_completed
        FROM tests t 
        JOIN position_tests pt ON t.id = pt.test_id 
        WHERE pt.position_id = ? AND t.status = 'active'
        ORDER BY pt.required DESC, t.title ASC
    ");
    $stmt->execute([$application_id, $application['position_id']]);
    $tests = $stmt->fetchAll();

    // Zorunlu testlerin tamamlanıp tamamlanmadığını kontrol et
    $all_required_completed = true;
    $all_tests_completed = true;
    foreach ($tests as $test) {
        if ($test['required'] && !$test['is_completed']) {
            $all_required_completed = false;
        }
        if (!$test['is_completed']) {
            $all_tests_completed = false;
        }
    }

    $page_title = "Başvuru - " . $application['position_title'] . " (Adım 4/4)";
    require_once 'includes/header.php';
} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <?php echo htmlspecialchars($application['position_title']); ?> - Testler
                </h3>
            </div>
            <div class="card-body">
                <!-- Progress Steps -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center stepper">
                        <div class="step">
                            <div class="step-circle">1</div>
                            <div class="step-label">Kişisel Bilgiler</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">2</div>
                            <div class="step-label">Eğitim</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">3</div>
                            <div class="step-label">Deneyim</div>
                        </div>
                        <div class="step active">
                            <div class="step-circle">4</div>
                            <div class="step-label">Testler</div>
                        </div>
                    </div>
                </div>
                <style>
                .stepper { gap: 0.5rem; }
                .step { text-align: center; flex: 1; position: relative; }
                .step-circle {
                    width: 36px; height: 36px; border-radius: 50%;
                    background: #0d6efd; color: #fff; display: flex; align-items: center; justify-content: center;
                    margin: 0 auto 6px auto; font-weight: bold; font-size: 18px; box-shadow: 0 2px 8px #0d6efd22;
                    border: 2px solid #0d6efd;
                    position: relative;
                    z-index: 1;
                }
                .step:not(.active) .step-circle {
                    background: #e9ecef; color: #6c757d; border: 2px solid #ced4da;
                }
                .step-label { font-size: 13px; color: #6c757d; }
                .step.active .step-label { color: #0d6efd; font-weight: 600; }
                .step:not(:last-child)::after {
                    content: ""; position: absolute; top: 18px; left: 50%; height: 4px;
                    width: calc(100% - 36px); background: #ced4da; z-index: 0;
                }
                .step.active:not(:last-child)::after {
                    background: #0d6efd;
                }
                </style>

                <?php if (empty($tests)): ?>
                    <div class="alert alert-info">
                        Bu pozisyon için test bulunmamaktadır. Başvurunuzu tamamlayabilirsiniz.
                    </div>
                    <form action="process_application.php" method="POST">
                        <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Başvuruyu Tamamla</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i> Test Bilgileri:
                        <ul class="mb-0">
                            <li>Zorunlu testleri tamamlamanız gerekmektedir.</li>
                            <li>İsteğe bağlı testleri çözmek istemiyorsanız "Çözmek İstemiyorum" butonunu kullanabilirsiniz.</li>
                            <li>Her test için belirtilen süre ve geçme puanı vardır.</li>
                        </ul>
                    </div>

                    <div class="test-list">
                        <?php foreach ($tests as $test): ?>
                            <div class="test-item border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($test['title']); ?></h5>
                                        <p class="mb-2"><?php echo htmlspecialchars($test['description']); ?></p>
                                        <div class="text-muted small">
                                            <span class="me-3"><i class="fas fa-clock"></i> <?php echo $test['time_limit']; ?> dakika</span>
                                            <span><i class="fas fa-check-circle"></i> Geçme puanı: <?php echo $test['passing_score']; ?></span>
                                        </div>
                                    </div>
                                    <span class="badge bg-<?php echo $test['required'] ? 'danger' : 'secondary'; ?>">
                                        <?php echo $test['required'] ? 'Zorunlu' : 'İsteğe Bağlı'; ?>
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <?php if ($test['is_completed']): ?>
                                        <span class="badge bg-success">Tamamlandı</span>
                                    <?php else: ?>
                                        <a href="take_test.php?application_id=<?php echo $application_id; ?>&test_id=<?php echo $test['id']; ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-play"></i> Teste Başla
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!$test['required']): ?>
                                        <form action="process_application.php" method="POST" class="d-inline">
                                            <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                                            <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                                            <input type="hidden" name="action" value="skip_test">
                                            <button type="submit" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i> Çözmek İstemiyorum
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!$all_required_completed): ?>
                        <div class="alert alert-warning">
                            Başvurunuzu tamamlamak için tüm zorunlu testleri tamamlamanız gerekmektedir.
                        </div>
                    <?php else: ?>
                        <form action="process_application.php" method="POST">
                            <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <?php if ($all_tests_completed): ?>
                                        Başvuruyu Tamamla
                                    <?php else: ?>
                                        Başvuruyu Tamamla (Opsiyonel testler kaldı)
                                    <?php endif; ?>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 