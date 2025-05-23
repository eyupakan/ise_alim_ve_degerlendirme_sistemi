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
        WHERE a.id = ? AND a.current_step = 2
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        header('Location: index.php');
        exit;
    }

    $page_title = "Başvuru - " . $application['position_title'] . " (Adım 2/4)";
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
                    <?php echo htmlspecialchars($application['position_title']); ?> - Eğitim & Sertifikalar
                </h3>
            </div>
            <div class="card-body">
                <!-- Progress bar -->
                <div class="progress mb-4" style="height: 2px;">
                    <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-center mb-4">
                    <small class="text-muted">Adım 2/4 - Eğitim & Sertifikalar</small>
                </div>

                <form action="process_step2.php" method="POST" id="educationForm">
                    <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                    
                    <!-- Eğitim Bilgileri -->
                    <h5 class="mb-3">Eğitim Bilgileri</h5>
                    <div id="educationContainer">
                        <div class="education-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Okul Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="education[0][school_name]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bölüm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="education[0][field_of_study]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Derece <span class="text-danger">*</span></label>
                                    <select class="form-select" name="education[0][degree]" required>
                                        <option value="">Seçiniz</option>
                                        <option value="high_school">Lise</option>
                                        <option value="associate">Ön Lisans</option>
                                        <option value="bachelor">Lisans</option>
                                        <option value="master">Yüksek Lisans</option>
                                        <option value="doctorate">Doktora</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="education[0][start_date]" required>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input is-current" type="checkbox" name="education[0][is_current]">
                                        <label class="form-check-label">Halen devam ediyorum</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label end-date-label">Bitiş Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control end-date" name="education[0][end_date]" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-4" id="addEducation">
                        <i class="fas fa-plus"></i> Eğitim Ekle
                    </button>

                    <!-- Sertifikalar -->
                    <h5 class="mb-3">Sertifikalar</h5>
                    <div id="certificateContainer">
                        <div class="certificate-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sertifika Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="certificate[0][name]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Veren Kurum <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="certificate[0][issuing_organization]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alınma Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="certificate[0][issue_date]" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-4" id="addCertificate">
                        <i class="fas fa-plus"></i> Sertifika Ekle
                    </button>

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
let educationCount = 1;
let certificateCount = 1;

// Eğitim ekleme fonksiyonu
document.getElementById('addEducation').addEventListener('click', function() {
    const container = document.getElementById('educationContainer');
    const template = document.querySelector('.education-item').cloneNode(true);
    
    // Form elemanlarının isimlerini güncelle
    template.querySelectorAll('input, select').forEach(input => {
        if (input.name) {
            input.name = input.name.replace('[0]', `[${educationCount}]`);
            if (input.tagName === 'SELECT') {
                input.value = ''; // Select elemanının değerini sıfırla
            } else {
                input.value = '';
            }
        }
    });

    // Event listener'ları yeniden ekle
    template.querySelector('.is-current').addEventListener('change', toggleEndDate);
    
    container.appendChild(template);
    educationCount++;
});

// Sertifika ekleme fonksiyonu
document.getElementById('addCertificate').addEventListener('click', function() {
    const container = document.getElementById('certificateContainer');
    const template = document.querySelector('.certificate-item').cloneNode(true);
    
    // Form elemanlarının isimlerini güncelle
    template.querySelectorAll('input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace('[0]', `[${certificateCount}]`);
            input.value = '';
        }
    });
    
    container.appendChild(template);
    certificateCount++;
});

// Devam ediyorum checkbox'ı için event listener
function toggleEndDate(e) {
    const endDateInput = e.target.closest('.education-item').querySelector('.end-date');
    const endDateLabel = e.target.closest('.education-item').querySelector('.end-date-label');
    
    if (e.target.checked) {
        endDateInput.value = '';
        endDateInput.disabled = true;
        endDateInput.required = false;
        endDateLabel.querySelector('.text-danger').style.display = 'none';
    } else {
        endDateInput.disabled = false;
        endDateInput.required = true;
        endDateLabel.querySelector('.text-danger').style.display = 'inline';
    }
}

// İlk eğitim formu için event listener ekle
document.querySelector('.is-current').addEventListener('change', toggleEndDate);
</script>

<?php require_once 'includes/footer.php'; ?> 