<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Debug için hataları göster
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pozisyonları getir
$query = "SELECT p.*, GROUP_CONCAT(t.title) as test_names, 
          GROUP_CONCAT(t.id) as test_ids
          FROM positions p 
          LEFT JOIN position_tests pt ON p.id = pt.position_id
          LEFT JOIN tests t ON pt.test_id = t.id
          GROUP BY p.id
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Testleri getir
$query = "SELECT * FROM tests ORDER BY title ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug için test verilerini kontrol et
echo "<!-- Debug: Test Sayısı: " . count($tests) . " -->";
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pozisyonlar - Admin Paneli</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 Bootstrap 5 Theme -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            color: #f8f9fa;
        }

        .main-content {
            padding: 20px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection--multiple {
            border: 1px solid #ced4da !important;
            padding: 5px !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd !important;
            color: white !important;
            border: none !important;
            padding: 2px 8px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ff4444 !important;
        }

        .chat-message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .chat-message .message-content {
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 5px;
            font-size: 15px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .chat-message.user {
            align-items: flex-end;
        }

        .chat-message.assistant {
            align-items: flex-start;
        }

        .chat-message.user .message-content {
            background-color: #007bff;
            color: white;
            border-bottom-right-radius: 5px;
        }

        .chat-message.assistant .message-content {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 5px;
            color: #212529;
        }

        .chat-message.assistant .message-content ul,
        .chat-message.assistant .message-content ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .chat-message.assistant .message-content li {
            margin: 5px 0;
        }

        .chat-message.assistant .message-content p {
            margin: 10px 0;
        }

        .chat-message.assistant .message-content code {
            background-color: #e9ecef;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }

        .chat-message.assistant .message-content pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow-x: auto;
            margin: 10px 0;
        }

        .chat-message.assistant .message-content pre code {
            background-color: transparent;
            padding: 0;
        }

        .chat-input {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        #chatMessages {
            display: flex;
            flex-direction: column;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin: 0 15px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4>Admin Paneli</h4>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="positions.php">
                                <i class="bi bi-briefcase"></i> Pozisyonlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tests.php">
                                <i class="bi bi-file-text"></i> Testler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="candidates.php">
                                <i class="bi bi-people"></i> Adaylar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="applications.php">
                                <i class="bi bi-file-earmark-text"></i> Başvurular
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="interviews.php">
                                <i class="bi bi-calendar-event"></i> Mülakatlar
                            </a>
                        </li>                       
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Pozisyonlar</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addPositionModal">
                        <i class="bi bi-plus-circle"></i> Yeni Pozisyon Ekle
                    </button>
                </div>

                <!-- Pozisyon Listesi -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Pozisyon Adı</th>
                                        <th>Açıklama</th>
                                        <th>Testler</th>
                                        <th>Durum</th>
                                        <th>Oluşturulma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positions as $position): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($position['title']); ?></td>
                                            <td><?php echo htmlspecialchars($position['description']); ?></td>
                                            <td>
                                                <?php
                                                if ($position['test_names']) {
                                                    $test_names = explode(',', $position['test_names']);
                                                    foreach ($test_names as $test_name) {
                                                        echo '<span class="badge bg-info me-1">' . htmlspecialchars($test_name) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-secondary">Test atanmamış</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo $position['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo $position['status'] === 'active' ? 'Aktif' : 'Pasif'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($position['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning btn-edit-position"
                                                    data-id="<?php echo $position['id']; ?>">
                                                    <i class="bi bi-pencil"></i> Düzenle
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-delete-position"
                                                    data-id="<?php echo $position['id']; ?>">
                                                    <i class="bi bi-trash"></i> Sil
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pozisyon Ekleme Modal -->
    <div class="modal fade" id="addPositionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Pozisyon Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addPositionForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Pozisyon Adı</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="requirements" class="form-label">Gereksinimler</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tests" class="form-label">Testler</label>
                            <select class="form-select" id="tests" name="tests[]" multiple>
                                <?php foreach ($tests as $test): ?>
                                    <option value="<?php echo $test['id']; ?>">
                                        <?php echo htmlspecialchars($test['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Seçilen testlerin zorunluluk durumunu belirleme alanı -->
                        <div id="add-selected-tests-container" class="mb-3"></div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="portfolio_point" class="form-label">Portfolyo Puanı</label>
                            <input type="number" class="form-control" id="portfolio_point" name="portfolio_point"
                                min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="certificate_point" class="form-label">Sertifika Puanı</label>
                            <input type="number" class="form-control" id="certificate_point" name="certificate_point"
                                min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="education_point" class="form-label">Eğitim Puanı</label>
                            <input type="number" class="form-control" id="education_point" name="education_point"
                                min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="reference_point" class="form-label">Referans Puanı</label>
                            <input type="number" class="form-control" id="reference_point" name="reference_point"
                                min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="experience_point" class="form-label">Deneyim Puanı</label>
                            <input type="number" class="form-control" id="experience_point" name="experience_point"
                                min="0" value="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="addPosition()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Position Modal -->
    <div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPositionModalLabel">Pozisyon Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPositionForm">
                        <input type="hidden" id="edit_position_id">
                        <div class="form-group">
                            <label for="edit_position_name">Pozisyon Adı</label>
                            <input type="text" class="form-control" id="edit_position_name" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Açıklama</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_requirements">Gereksinimler</label>
                            <textarea class="form-control" id="edit_requirements" name="requirements"
                                rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_tests">Testler</label>
                            <select class="form-control" id="edit_tests" name="tests[]" multiple>
                                <?php foreach ($tests as $test): ?>
                                    <option value="<?php echo $test['id']; ?>">
                                        <?php echo htmlspecialchars($test['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Seçilen testlerin zorunluluk durumunu belirleme alanı -->
                        <div id="edit-selected-tests-container" class="mb-3"></div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit_status" name="status">
                                <label class="custom-control-label" for="edit_status">Aktif</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_portfolio_point">Portfolyo Puanı</label>
                            <input type="number" class="form-control" id="edit_portfolio_point" name="portfolio_point"
                                min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_certificate_point">Sertifika Puanı</label>
                            <input type="number" class="form-control" id="edit_certificate_point"
                                name="certificate_point" min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_education_point">Eğitim Puanı</label>
                            <input type="number" class="form-control" id="edit_education_point" name="education_point"
                                min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_reference_point">Referans Puanı</label>
                            <input type="number" class="form-control" id="edit_reference_point" name="reference_point"
                                min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_experience_point">Deneyim Puanı</label>
                            <input type="number" class="form-control" id="edit_experience_point" name="experience_point"
                                min="0" value="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="updatePosition()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Modal -->
    <div class="modal fade" id="chatbotModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">İK Asistanı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="chat-container" style="height: 400px; overflow-y: auto; padding: 15px;">
                        <div id="chatMessages" class="mb-3">
                            <!-- Mesajlar buraya gelecek -->
                            <div class="chat-message assistant">
                                <div class="message-content">
                                    Merhaba! Ben İK Asistanınızım. Size pozisyonlar, işe alım süreçleri veya adaylar
                                    hakkında nasıl yardımcı olabilirim?
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-input">
                        <form id="chatForm" class="d-flex gap-2">
                            <input type="text" id="userMessage" class="form-control" placeholder="Mesajınızı yazın...">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Trigger Button -->
    <button type="button" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px;"
        data-bs-toggle="modal" data-bs-target="#chatbotModal">
        <i class="bi bi-chat-dots"></i> İK Asistanı
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Seçilen testler için HTML oluşturma fonksiyonu
        function createSelectedTestHtml(testId, testName, isRequired = false, formPrefix = 'add') {
            const checked = isRequired ? 'checked' : '';
            return `
                <div class="selected-test-item d-flex justify-content-between align-items-center border rounded p-2 mb-2" data-test-id="${testId}">
                    <span>${testName}</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="${formPrefix}-required-test-${testId}" name="position_tests[${testId}][required]" value="1" ${checked}>
                        <label class="form-check-label" for="${formPrefix}-required-test-${testId}">Zorunlu</label>
                    </div>
                    <input type="hidden" name="position_tests[${testId}][test_id]" value="${testId}">
                </div>
            `;
        }

        // Seçilen testleri güncelleme fonksiyonu
        function updateSelectedTestsContainer(selectElementId, containerId, formPrefix) {
            const selectedTestsContainer = document.getElementById(containerId);
            selectedTestsContainer.innerHTML = ''; // Konteyneri temizle

            // Select2'de seçili olan testler
            const selectedTests = $(`#${selectElementId}`).select2('data');

            selectedTests.forEach(test => {
                // Düzenleme modalında mevcut zorunluluk durumunu al (varsa)
                let isRequired = false; // Varsayılan olarak isteğe bağlı
                // TODO: Düzenleme modalı için mevcut pozisyonun testlerinin zorunluluk durumunu backend'den çekip buraya aktarmamız gerekecek.
                // Şu an için, düzenleme modalı açıldığında bu fonksiyonun dışında ele alınacak.

                const testHtml = createSelectedTestHtml(test.id, test.text, isRequired, formPrefix);
                selectedTestsContainer.innerHTML += testHtml;
            });
        }

        $(document).ready(function () {
            // Select2'yi başlat
            $('#tests, #edit_tests').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Test seçin',
                allowClear: true
            });

            // Yeni Pozisyon Ekle modalı için Select2 olayları
            $('#tests').on('select2:select', function (e) {
                const test = e.params.data;
                const container = document.getElementById('add-selected-tests-container');
                const testHtml = createSelectedTestHtml(test.id, test.text, false, 'add'); // Yeni eklenen varsayılan olarak isteğe bağlı
                container.innerHTML += testHtml;
            }).on('select2:unselect', function (e) {
                const testId = e.params.data.id;
                const container = document.getElementById('add-selected-tests-container');
                const itemToRemove = container.querySelector(`.selected-test-item[data-test-id=\"${testId}\"]`);
                if (itemToRemove) {
                    itemToRemove.remove();
                }
            });

            // Pozisyon Düzenle modalı için Select2 olayları
            $('#edit_tests').on('select2:select', function (e) {
                 const test = e.params.data;
                const container = document.getElementById('edit-selected-tests-container');
                // Düzenleme modalında yeni eklenen test varsayılan olarak isteğe bağlı
                const testHtml = createSelectedTestHtml(test.id, test.text, false, 'edit');
                container.innerHTML += testHtml;
            }).on('select2:unselect', function (e) {
                const testId = e.params.data.id;
                const container = document.getElementById('edit-selected-tests-container');
                 const itemToRemove = container.querySelector(`.selected-test-item[data-test-id=\"${testId}\"]`);
                if (itemToRemove) {
                    itemToRemove.remove();
                }
            });
        });

        // Yeni pozisyon ekleme fonksiyonu
        function addPosition() {
            const form = document.getElementById('addPositionForm');
            const formData = new FormData(form);
            // EKLENDİ ------
            formData.append('action', 'create');
            // EKLENDİ ------

            // Form verilerini konsola yazdır (debug için)
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch('process_position.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Server response:', text);
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Pozisyon başarıyla eklendi.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error || 'Bir hata oluştu');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: error.message
                    });
                });
        }

        // Form gönderimi için JavaScript
        function updatePosition() {
            const form = document.getElementById('editPositionForm');
            const formData = new FormData(form);
            const positionId = document.getElementById('edit_position_id').value;

            // Form verilerini konsola yazdır (debug için)
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch(`process_position.php?action=edit&id=${positionId}`, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Server response:', text);
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Pozisyon başarıyla güncellendi.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error || 'Bir hata oluştu');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: error.message
                    });
                });
        }

        // Düzenleme modalını açma işlemi
        document.querySelectorAll('.btn-edit-position').forEach(button => {
            button.addEventListener('click', function () {
                const positionId = this.dataset.id;

                // Pozisyon verilerini getir
                fetch(`process_position.php?action=get&id=${positionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const position = data.data;

                            // Form alanlarını doldur
                            document.getElementById('edit_position_id').value = position.id;
                            document.getElementById('edit_position_name').value = position.title;
                            document.getElementById('edit_description').value = position.description;
                            document.getElementById('edit_requirements').value = position.requirements;
                            document.getElementById('edit_status').checked = position.status === 'active';

                            // Puan alanlarını doldur
                            document.getElementById('edit_portfolio_point').value = position.portfolio_point;
                            document.getElementById('edit_certificate_point').value = position.certificate_point;
                            document.getElementById('edit_education_point').value = position.education_point;
                            document.getElementById('edit_reference_point').value = position.reference_point;
                            document.getElementById('edit_experience_point').value = position.experience_point;

                            // Test seçimlerini güncelle
                            const testIds = position.test_ids || [];
                            $('#edit_tests').val(testIds).trigger('change');

                            // Mevcut testlerin zorunluluk durumunu doldur
                            const selectedTestsContainer = document.getElementById('edit-selected-tests-container');
                            selectedTestsContainer.innerHTML = ''; // Önceki içeriği temizle

                            if (position.tests_with_required) { // Backend'den testlerin zorunluluk durumu ile geldi
                                position.tests_with_required.forEach(test => {
                                    // createSelectedTestHtml fonksiyonunu kullanarak her test için HTML oluştur ve ekle
                                    const testHtml = createSelectedTestHtml(test.test_id, test.test_title, test.required, 'edit');
                                    selectedTestsContainer.innerHTML += testHtml;
                                });
                            }

                            // Modalı göster
                            const modal = new bootstrap.Modal(document.getElementById('editPositionModal'));
                            modal.show();
                        } else {
                            throw new Error(data.error || 'Pozisyon bilgileri alınamadı');
                        }
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: error.message
                        });
                    });
            });
        });

        // Pozisyon silme
        document.querySelectorAll('.btn-delete-position').forEach(button => {
            button.addEventListener('click', function () {
                const positionId = this.dataset.id;

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu pozisyonu silmek istediğinizden emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, sil',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', positionId);

                        fetch('process_position.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Başarılı!',
                                        text: 'Pozisyon başarıyla silindi.',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    throw new Error(data.error || 'Bir hata oluştu');
                                }
                            })
                            .catch(error => {
                                console.error('Hata:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: error.message
                                });
                            });
                    }
                });
            });
        });

        document.getElementById('chatForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const userMessage = document.getElementById('userMessage').value.trim();
            if (!userMessage) return;

            // Kullanıcı mesajını ekle
            addMessage('user', userMessage);
            document.getElementById('userMessage').value = '';

            try {
                // API isteği
                const response = await fetch('../api/chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: userMessage })
                });

                if (!response.ok) {
                    throw new Error('API yanıt vermedi');
                }

                const data = await response.json();
                addMessage('assistant', data.response);

            } catch (error) {
                addMessage('assistant', 'Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.');
                console.error('Hata:', error);
            }
        });

        function addMessage(type, content) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${type}`;

            const time = new Date().toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });

            messageDiv.innerHTML = `
                <div class="message-content">${content}</div>
                <div class="message-time">${time}</div>
            `;

            chatMessages.appendChild(messageDiv);
            messageDiv.scrollIntoView({ behavior: 'smooth' });
        }
    </script>

    <?php if (isset($_GET['success'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Başarılı</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <?php
                    switch ($_GET['success']) {
                        case 'created':
                            echo 'Pozisyon başarıyla oluşturuldu.';
                            break;
                        case 'updated':
                            echo 'Pozisyon başarıyla güncellendi.';
                            break;
                        case 'deleted':
                            echo 'Pozisyon başarıyla silindi.';
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="toast show" role="alert">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Hata</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>