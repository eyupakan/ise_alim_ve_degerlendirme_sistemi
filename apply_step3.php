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

<div class="row justify-content-center" style="min-height: 100vh; padding: 2rem 0;">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="border-radius: 12px 12px 0 0;">
                <h3 class="card-title mb-0">
                    <?php echo htmlspecialchars($application['position_title']); ?> - Deneyim & Referanslar
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
                        <div class="step active">
                            <div class="step-circle">3</div>
                            <div class="step-label">Deneyim</div>
                        </div>
                        <div class="step">
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

                <form action="process_step3.php" method="POST" id="experienceForm">
                    <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                    
                    <!-- İş Deneyimi -->
                    <h5 class="mb-3">İş Deneyimi</h5>
                    <div id="experienceContainer">
                        <div class="experience-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="row flex-grow-1">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Şirket Adı <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="experience[0][company_name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pozisyon <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="experience[0][position]" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-experience-item ms-3">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="experience[0][start_date]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bitiş Tarihi</label>
                                    <input type="date" class="form-control end-date" name="experience[0][end_date]">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input is-current" type="checkbox" name="experience[0][is_current]" id="is_current_exp_0">
                                        <label class="form-check-label" for="is_current_exp_0">
                                            Devam ediyorum
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Açıklama</label>
                                    <textarea class="form-control" name="experience[0][description]" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary" id="addExperience">
                            <i class="fas fa-plus"></i> Deneyim Ekle
                        </button>
                    </div>

                    <!-- Referanslar -->
                    <h5 class="mb-3">Referanslar</h5>
                    <div id="referenceContainer">
                        <div class="reference-item border rounded p-3 mb-3">
                             <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="row flex-grow-1">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference[0][name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Şirket <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference[0][company]" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-reference-item ms-3">
                                    <i class="fas fa-times"></i>
                                </button>
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
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary" id="addReference">
                            <i class="fas fa-plus"></i> Referans Ekle
                        </button>
                    </div>
                    <!-- KVKK Onayı -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="kvkk" name="kvkk_accepted" required>
                        <label class="form-check-label" for="kvkk">
                            Kişisel verilerimin işlenmesine izin veriyorum. <span class="text-danger">*</span>
                        </label>
                    </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="apply_step2.php?application_id=<?php echo $application_id; ?>" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; transition: all 0.2s ease;">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                        <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; transition: all 0.2s ease;">
                            Devam Et<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let experienceCount = 1;
let referenceCount = 1;

// Helper function to add delete functionality
function addDeleteFunctionality(itemElement, itemType) {
    const deleteButton = itemElement.querySelector(`.delete-${itemType}-item`);
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            // Don't allow deleting the last item for experience and reference
            const container = document.getElementById(`${itemType}Container`);
            if (container.querySelectorAll(`.${itemType}-item`).length > 1) {
                itemElement.remove();
            } else {
                 alert(`En az bir ${itemType === 'experience' ? 'deneyim' : 'referans'} bilgisi girmelisiniz.`);
            }
        });
    }
}

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

    // Event listener'ları yeniden ekle (is-current)
    const isCurrentCheckbox = template.querySelector('.is-current');
    if (isCurrentCheckbox) {
         // Clone'lanan checkbox'ın ID'sini ve for niteliğini güncelle
        isCurrentCheckbox.id = `is_current_exp_${experienceCount}`;
        template.querySelector(`label[for^='is_current_exp_']`).setAttribute('for', `is_current_exp_${experienceCount}`);
        isCurrentCheckbox.addEventListener('change', toggleEndDate);
    }
    
     // Silme butonu functionality ekle
    addDeleteFunctionality(template, 'experience');

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
    
     // Silme butonu functionality ekle
    addDeleteFunctionality(template, 'reference');

    container.appendChild(template);
    referenceCount++;
});

// Devam ediyorum checkbox'ı için event listener
function toggleEndDate(e) {
    const experienceItem = e.target.closest('.experience-item');
    const endDateInput = experienceItem.querySelector('.end-date');
    
    if (e.target.checked) {
        endDateInput.value = '';
        endDateInput.disabled = true;
        endDateInput.required = false;
    } else {
        endDateInput.disabled = false;
        endDateInput.required = true;
    }
}

// Sayfa yüklendiğinde mevcut item'lara silme fonksiyonu ekle
document.querySelectorAll('.experience-item').forEach(item => addDeleteFunctionality(item, 'experience'));
document.querySelectorAll('.reference-item').forEach(item => addDeleteFunctionality(item, 'reference'));

// İlk deneyim formu için event listener ekle (varsa)
const firstIsCurrentCheckboxExp = document.querySelector('.experience-item .is-current');
if (firstIsCurrentCheckboxExp) {
    firstIsCurrentCheckboxExp.addEventListener('change', toggleEndDate);
}
</script>

<?php require_once 'includes/footer.php'; ?> 