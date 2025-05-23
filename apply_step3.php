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
        WHERE a.id = ? AND a.current_step = 3
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        header('Location: index.php');
        exit;
    }

    $page_title = "Başvuru - " . $application['position_title'] . " (Adım 3/4)";
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
                    <?php echo htmlspecialchars($application['position_title']); ?> - Deneyim & Referanslar
                </h3>
            </div>
            <div class="card-body">
                <!-- Progress bar -->
                <div class="progress mb-4" style="height: 2px;">
                    <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-center mb-4">
                    <small class="text-muted">Adım 3/4 - Deneyim & Referanslar</small>
                </div>

                <form action="process_step3.php" method="POST" id="experienceForm">
                    <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                    
                    <!-- İş Deneyimi -->
                    <h5 class="mb-3">İş Deneyimi</h5>
                    <div id="experienceContainer">
                        <div class="experience-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Şirket Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="experience[0][company_name]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pozisyon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="experience[0][position]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control start-date" name="experience[0][start_date]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input is-current" type="checkbox" name="experience[0][is_current]">
                                        <label class="form-check-label">Halen çalışıyorum</label>
                                    </div>
                                    <input type="date" class="form-control end-date" name="experience[0][end_date]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Sorumluluklar ve Başarılar</label>
                                    <textarea class="form-control" name="experience[0][responsibilities]" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-4" id="addExperience">
                        <i class="fas fa-plus"></i> Deneyim Ekle
                    </button>

                    <!-- Referanslar -->
                    <h5 class="mb-3">Referanslar</h5>
                    <div id="referenceContainer">
                        <div class="reference-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="reference[0][name]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Şirket <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="reference[0][company]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pozisyon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="reference[0][position]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">E-posta <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="reference[0][email]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefon</label>
                                    <input type="tel" class="form-control" name="reference[0][phone]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-4" id="addReference">
                        <i class="fas fa-plus"></i> Referans Ekle
                    </button>

                    <!-- KVKK Onayı -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="kvkk" name="kvkk_accepted" required>
                            <label class="form-check-label" for="kvkk">
                                Kişisel verilerimin işlenmesine izin veriyorum. <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>


                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Devam Et</button>
                        <a href="index.php" class="btn btn-light">İptal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let experienceCount = 1;
let referenceCount = 1;

// Deneyim ekleme fonksiyonu
document.getElementById('addExperience').addEventListener('click', function() {
    const container = document.getElementById('experienceContainer');
    const template = document.querySelector('.experience-item').cloneNode(true);
    
    // Form elemanlarının isimlerini güncelle
    template.querySelectorAll('input, textarea').forEach(input => {
        if (input.name) {
            input.name = input.name.replace('[0]', `[${experienceCount}]`);
            input.value = '';
        }
    });

    // Event listener'ları yeniden ekle
    template.querySelector('.is-current').addEventListener('change', toggleEndDate);
    
    container.appendChild(template);
    experienceCount++;
});

// Referans ekleme fonksiyonu
document.getElementById('addReference').addEventListener('click', function() {
    const container = document.getElementById('referenceContainer');
    const template = document.querySelector('.reference-item').cloneNode(true);
    
    // Form elemanlarının isimlerini güncelle
    template.querySelectorAll('input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace('[0]', `[${referenceCount}]`);
            input.value = '';
        }
    });
    
    container.appendChild(template);
    referenceCount++;
});

// Devam ediyorum checkbox'ı için event listener
function toggleEndDate(e) {
    const endDateInput = e.target.closest('.experience-item').querySelector('.end-date');
    
    if (e.target.checked) {
        endDateInput.value = '';
        endDateInput.disabled = true;
        endDateInput.required = false;
    } else {
        endDateInput.disabled = false;
        endDateInput.required = true;
    }
}

// İlk deneyim formu için event listener ekle
document.querySelector('.is-current').addEventListener('change', toggleEndDate);
</script>

<?php require_once 'includes/footer.php'; ?> 