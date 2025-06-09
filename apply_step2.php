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

<div class="row justify-content-center" style="min-height: 100vh; padding: 2rem 0;">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="border-radius: 12px 12px 0 0;">
                <h3 class="card-title mb-0"><?php echo $page_title; ?></h3>
            </div>
            <div class="card-body">
                <!-- Progress Steps -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center stepper">
                        <div class="step">
                            <div class="step-circle">1</div>
                            <div class="step-label">Kişisel Bilgiler</div>
                        </div>
                        <div class="step active">
                            <div class="step-circle">2</div>
                            <div class="step-label">Eğitim</div>
                        </div>
                        <div class="step">
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

                <form action="process_step2.php" method="POST" id="educationForm">
                    <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                    
                    <!-- Eğitim Bilgileri -->
                    <h5 class="mb-3">Eğitim Bilgileri</h5>
                    <div id="educationContainer">
                        <div class="education-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="row flex-grow-1">
                                    <div class="col-md-6 mb-3">
                                        <label for="school_name" class="form-label">Okul Adı <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="school_name" name="education[0][school_name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="field_of_study" class="form-label">Bölüm <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="field_of_study" name="education[0][field_of_study]" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-education-item ms-3">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="degree" class="form-label">Derece <span class="text-danger">*</span></label>
                                    <select class="form-select" id="degree" name="education[0][degree]" required>
                                        <option value="">Seçiniz</option>
                                        <option value="high_school">Lise</option>
                                        <option value="associate">Ön Lisans</option>
                                        <option value="bachelor">Lisans</option>
                                        <option value="master">Yüksek Lisans</option>
                                        <option value="doctorate">Doktora</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="education[0][start_date]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label end-date-label">Bitiş Tarihi</label>
                                    <input type="date" class="form-control end-date" id="end_date" name="education[0][end_date]">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input is-current" type="checkbox" id="is_current_0" name="education[0][is_current]">
                                        <label class="form-check-label" for="is_current_0">
                                            Devam ediyorum
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gpa" class="form-label">Not Ortalaması (GPA)</label>
                                    <div class="mb-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input gpa-system" type="radio" name="education[0][gpa_system]" id="gpa_system_100_0" value="100" checked>
                                            <label class="form-check-label" for="gpa_system_100_0">100'lük Sistem</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input gpa-system" type="radio" name="education[0][gpa_system]" id="gpa_system_4_0" value="4">
                                            <label class="form-check-label" for="gpa_system_4_0">4'lük Sistem</label>
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control gpa-input" id="gpa" name="education[0][gpa]" placeholder="Örn: 85.50">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary" id="addEducation">
                            <i class="fas fa-plus"></i> Eğitim Ekle
                        </button>
                    </div>

                    <!-- Sertifikalar -->
                    <h5 class="mb-3">Sertifikalar</h5>
                    <div id="certificateContainer">
                        <div class="certificate-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="row flex-grow-1">
                                    <div class="col-md-6 mb-3">
                                        <label for="certificate_name" class="form-label">Sertifika Adı <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="certificate_name" name="certificate[0][name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="issuing_organization" class="form-label">Kurum <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="issuing_organization" name="certificate[0][issuing_organization]" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-certificate-item ms-3">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="issue_date" class="form-label">Alınma Tarihi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="issue_date" name="certificate[0][issue_date]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expiry_date" class="form-label">Geçerlilik Tarihi</label>
                                    <input type="date" class="form-control" id="expiry_date" name="certificate[0][expiry_date]">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="credential_id" class="form-label">Sertifika ID</label>
                                    <input type="text" class="form-control" id="credential_id" name="certificate[0][credential_id]">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="credential_url" class="form-label">Sertifika URL</label>
                                    <input type="url" class="form-control" id="credential_url" name="certificate[0][credential_url]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary" id="addCertificate">
                            <i class="fas fa-plus"></i> Sertifika Ekle
                        </button>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="apply_step1.php?position_id=<?php echo $application['position_id']; ?>" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; transition: all 0.2s ease;">
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
let educationCount = 1;
let certificateCount = 1;

// Helper function to add delete functionality
function addDeleteFunctionality(itemElement, itemType) {
    const deleteButton = itemElement.querySelector(`.delete-${itemType}-item`);
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            // Don't allow deleting the last item for education and certificate
            const container = document.getElementById(`${itemType}Container`);
            if (container.querySelectorAll(`.${itemType}-item`).length > 1) {
                itemElement.remove();
            } else {
                alert(`En az bir ${itemType === 'education' ? 'eğitim' : 'sertifika'} bilgisi girmelisiniz.`);
            }
        });
    }
}

// GPA sistem değişikliği için event listener
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('gpa-system')) {
        const educationItem = e.target.closest('.education-item');
        const gpaInput = educationItem.querySelector('.gpa-input');
        const system = e.target.value;
        
        if (system === '100') {
            gpaInput.max = '100';
            gpaInput.placeholder = 'Örn: 85.50';
        } else {
            gpaInput.max = '4';
            gpaInput.placeholder = 'Örn: 3.50';
        }
    }
});

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

    // Radio button ID'lerini güncelle
    template.querySelectorAll('.gpa-system').forEach(radio => {
        const newId = radio.id.replace('_0', `_${educationCount}`);
        radio.id = newId;
        radio.nextElementSibling.setAttribute('for', newId);
    });

    // Event listener'ları yeniden ekle (is-current)
    const isCurrentCheckbox = template.querySelector('.is-current');
    if (isCurrentCheckbox) {
        isCurrentCheckbox.id = `is_current_${educationCount}`;
        template.querySelector(`label[for^='is_current_']`).setAttribute('for', `is_current_${educationCount}`);
        isCurrentCheckbox.addEventListener('change', toggleEndDate);
    }
    
    // Silme butonu functionality ekle
    addDeleteFunctionality(template, 'education');
    
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
    
    // Silme butonu functionality ekle
    addDeleteFunctionality(template, 'certificate');

    container.appendChild(template);
    certificateCount++;
});

// Devam ediyorum checkbox'ı için event listener
function toggleEndDate(e) {
    const educationItem = e.target.closest('.education-item');
    const endDateInput = educationItem.querySelector('.end-date');
    const endDateLabel = educationItem.querySelector('.end-date-label');
    
    if (e.target.checked) {
        endDateInput.value = '';
        endDateInput.disabled = true;
        endDateInput.required = false;
        const requiredSpan = endDateLabel.querySelector('.text-danger');
        if (requiredSpan) {
            requiredSpan.style.display = 'none';
        }
    } else {
        endDateInput.disabled = false;
        endDateInput.required = true;
        const requiredSpan = endDateLabel.querySelector('.text-danger');
        if (requiredSpan) {
            requiredSpan.style.display = 'inline';
        }
    }
}

// Sayfa yüklendiğinde mevcut item'lara silme fonksiyonu ekle
document.querySelectorAll('.education-item').forEach(item => addDeleteFunctionality(item, 'education'));
document.querySelectorAll('.certificate-item').forEach(item => addDeleteFunctionality(item, 'certificate'));

// İlk eğitim formu için event listener ekle
const firstIsCurrentCheckbox = document.querySelector('.education-item .is-current');
if (firstIsCurrentCheckbox) {
    firstIsCurrentCheckbox.addEventListener('change', toggleEndDate);
}
</script>

<?php require_once 'includes/footer.php'; ?> 