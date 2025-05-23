<?php
require_once 'auth_check.php';
require_once '../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Test ID kontrolü
if (!isset($_GET['test_id']) || !is_numeric($_GET['test_id'])) {
    header('Location: tests.php');
    exit;
}

$test_id = (int)$_GET['test_id'];
$database = new Database();
$db = $database->getConnection();

// Test bilgilerini getir
$query = "SELECT * FROM tests WHERE id = :test_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':test_id', $test_id);
$stmt->execute();
$test = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$test) {
    header('Location: tests.php');
    exit;
}

// Test sorularını getir
$query = "SELECT q.*, 
          GROUP_CONCAT(o.id, ':::', o.option_text SEPARATOR '|||') as options
          FROM test_questions q
          LEFT JOIN question_options o ON q.id = o.question_id
          WHERE q.test_id = :test_id
          GROUP BY q.id
          ORDER BY q.id ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(':test_id', $test_id);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Soruları - Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
            font-size: 15px;
            line-height: 1.6;
        }
        .question-card {
            margin-bottom: 20px;
        }
        .question-card .card-title {
            font-size: 15px;
            font-weight: normal;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #212529;
        }
        .question-card .text-muted {
            font-size: 15px;
            line-height: 1.6;
        }
        .options-container {
            margin-top: 10px;
        }
        .option-row {
            margin-bottom: 10px;
        }
        .form-check {
            margin-bottom: 8px;
        }
        .form-check-label {
            font-size: 15px;
            font-weight: normal;
            line-height: 1.6;
            color: #212529;
        }
        .input-group-text {
            font-size: 15px;
            font-weight: normal;
            line-height: 1.6;
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #212529;
        }
        .form-control {
            font-size: 15px;
            line-height: 1.6;
        }
        .btn {
            font-size: 15px;
            line-height: 1.6;
        }
        .modal-body {
            font-size: 15px;
            line-height: 1.6;
        }
        .modal-title {
            font-size: 16px;
            line-height: 1.6;
        }
        .form-label {
            font-size: 15px;
            line-height: 1.6;
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
                            <a class="nav-link" href="positions.php">
                                <i class="bi bi-briefcase"></i> Pozisyonlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="tests.php">
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
                    <div>
                        <h2><?php echo htmlspecialchars($test['title']); ?> - Sorular</h2>
                        <p class="text-muted">
                            Süre: <?php echo $test['time_limit']; ?> dakika | 
                            Geçme Notu: <span id="passing_score"><?php echo $test['passing_score']; ?></span>
                            <button class="btn btn-sm btn-link text-primary" onclick="editPassingScore()">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </p>
                    </div>
                    <div>
                        <a href="tests.php" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Geri
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="bi bi-plus-circle"></i> Yeni Soru Ekle
                        </button>
                    </div>
                </div>

                <!-- Soru Listesi -->
                <div class="questions-container">
                    <?php foreach ($questions as $question): ?>
                    <div class="card question-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?php echo htmlspecialchars($question['question_text']); ?></h5>
                                <div>
                                    <button class="btn btn-sm btn-warning me-2 btn-edit-question" data-id="<?php echo $question['id']; ?>">
                                        <i class="bi bi-pencil"></i> Düzenle
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete-question" data-id="<?php echo $question['id']; ?>">
                                        <i class="bi bi-trash"></i> Sil
                                    </button>
                                </div>
                            </div>
                            <p class="text-muted">
                                Tür: <?php 
                                    switch($question['question_type']) {
                                        case 'multiple_choice':
                                            echo 'Çoktan Seçmeli';
                                            break;
                                        case 'true_false':
                                            echo 'Doğru/Yanlış';
                                            break;
                                        case 'text':
                                            echo 'Metin';
                                            break;
                                    }
                                ?> | 
                                Puan: <?php echo $question['points']; ?>
                            </p>
                            
                            <?php if ($question['question_type'] == 'multiple_choice' && !empty($question['options'])): ?>
                                <div class="options-container">
                                    <?php 
                                    $options = explode('|||', $question['options']);
                                    $letters = range('A', 'Z');
                                    foreach ($options as $index => $option):
                                        list($option_id, $option_text) = explode(':::', $option);
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled
                                               <?php echo $question['correct_answer'] == $option_text ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            <?php echo $letters[$index] . ') ' . htmlspecialchars($option_text); ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($question['question_type'] == 'true_false'): ?>
                                <div class="options-container">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled
                                               <?php echo $question['correct_answer'] == 'true' ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Doğru</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled
                                               <?php echo $question['correct_answer'] == 'false' ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Yanlış</label>
                                    </div>
                                </div>
                            <?php elseif ($question['question_type'] == 'text'): ?>
                                <div class="mt-2">
                                    <strong>Doğru Cevap:</strong> <?php echo htmlspecialchars($question['correct_answer']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Soru Ekleme Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Soru Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addQuestionForm">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Soru Metni</label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="question_type" class="form-label">Soru Tipi</label>
                            <select class="form-select" id="question_type" name="question_type" required>
                                <option value="">Seçiniz</option>
                                <option value="multiple_choice">Çoktan Seçmeli</option>
                                <option value="true_false">Doğru/Yanlış</option>
                                <option value="text">Metin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="points" class="form-label">Puan</label>
                            <input type="number" class="form-control" id="points" name="points" value="1" min="1" required>
                        </div>

                        <!-- Çoktan Seçmeli Seçenekler -->
                        <div id="multiple_choice_container" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Seçenekler</label>
                                <div id="options_container">
                                    <div class="option-row d-flex mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">A)</span>
                                            <input type="text" class="form-control me-2" name="options[]" placeholder="Seçenek">
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input" type="radio" name="correct_option" value="0">
                                            <label class="form-check-label ms-2">Doğru Cevap</label>
                                        </div>
                                        <button type="button" class="btn btn-danger ms-2 btn-remove-option">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary mt-2" id="btn_add_option">
                                    <i class="bi bi-plus"></i> Seçenek Ekle
                                </button>
                            </div>
                        </div>

                        <!-- Doğru/Yanlış Seçenekler -->
                        <div id="true_false_container" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Doğru Cevap</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="true_false_answer" value="true" id="true_option">
                                    <label class="form-check-label" for="true_option">Doğru</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="true_false_answer" value="false" id="false_option">
                                    <label class="form-check-label" for="false_option">Yanlış</label>
                                </div>
                            </div>
                        </div>

                        <!-- Metin Cevap -->
                        <div id="text_container" style="display: none;">
                            <div class="mb-3">
                                <label for="text_answer" class="form-label">Doğru Cevap</label>
                                <input type="text" class="form-control" id="text_answer" name="text_answer">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" form="addQuestionForm" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Geçme Notu Düzenleme Modal -->
    <div class="modal fade" id="editPassingScoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Geçme Notunu Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editPassingScoreForm">
                        <input type="hidden" name="action" value="update_passing_score">
                        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
                        <div class="mb-3">
                            <label for="passing_score" class="form-label">Geçme Notu</label>
                            <input type="number" class="form-control" id="passing_score_input" name="passing_score" 
                                   value="<?php echo $test['passing_score']; ?>" min="0" max="100" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" form="editPassingScoreForm" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Soru Düzenleme Modal -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Soruyu Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editQuestionForm">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="question_id" id="edit_question_id">
                        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">

                        <div class="mb-3">
                            <label for="edit_question_text" class="form-label">Soru Metni</label>
                            <textarea class="form-control" id="edit_question_text" name="question_text" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_question_type" class="form-label">Soru Tipi</label>
                            <select class="form-select" id="edit_question_type" name="question_type" required readonly>
                                <option value="multiple_choice">Çoktan Seçmeli</option>
                                <option value="true_false">Doğru/Yanlış</option>
                                <option value="text">Metin</option>
                            </select>
                            <input type="hidden" name="question_type" id="edit_question_type_hidden">
                        </div>

                        <div class="mb-3">
                            <label for="edit_points" class="form-label">Puan</label>
                            <input type="number" class="form-control" id="edit_points" name="points" min="1" required>
                        </div>

                        <!-- Çoktan Seçmeli Seçenekler -->
                        <div id="edit_multiple_choice_container" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Seçenekler</label>
                                <div id="edit_options_container">
                                    <!-- Seçenekler JavaScript ile doldurulacak -->
                                </div>
                            </div>
                        </div>

                        <!-- Doğru/Yanlış Seçenekler -->
                        <div id="edit_true_false_container" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Doğru Cevap</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="edit_true_false_answer" value="true" id="edit_true_option">
                                    <label class="form-check-label" for="edit_true_option">Doğru</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="edit_true_false_answer" value="false" id="edit_false_option">
                                    <label class="form-check-label" for="edit_false_option">Yanlış</label>
                                </div>
                            </div>
                        </div>

                        <!-- Metin Cevap -->
                        <div id="edit_text_container" style="display: none;">
                            <div class="mb-3">
                                <label for="edit_text_answer" class="form-label">Doğru Cevap</label>
                                <input type="text" class="form-control" id="edit_text_answer" name="text_answer">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" form="editQuestionForm" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Modal -->
    <div class="modal fade" id="chatbotModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Soru Asistanı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="chat-container" style="height: 400px; overflow-y: auto; padding: 15px;">
                        <div id="chatMessages" class="mb-3">
                            <!-- Mesajlar buraya gelecek -->
                            <div class="chat-message assistant">
                                <div class="message-content">
                                    Merhaba! Ben Soru Asistanınızım. Size test soruları oluşturma konusunda yardımcı olabilirim. Nasıl yardımcı olabilirim?
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
    <button type="button" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px;" data-bs-toggle="modal" data-bs-target="#chatbotModal">
        <i class="bi bi-chat-dots"></i> Soru Asistanı
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Soru tipi değiştiğinde ilgili container'ı göster/gizle
        document.getElementById('question_type').addEventListener('change', function() {
            document.getElementById('multiple_choice_container').style.display = 'none';
            document.getElementById('true_false_container').style.display = 'none';
            document.getElementById('text_container').style.display = 'none';

            const selectedType = this.value;
            if (selectedType) {
                document.getElementById(selectedType + '_container').style.display = 'block';
            }
        });

        // Seçenek ekleme
        document.getElementById('btn_add_option').addEventListener('click', function() {
            const container = document.getElementById('options_container');
            const optionCount = container.children.length;
            const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            
            const optionRow = document.createElement('div');
            optionRow.className = 'option-row d-flex mb-2';
            optionRow.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text">${letters[optionCount]})</span>
                    <input type="text" class="form-control me-2" name="options[]" placeholder="Seçenek">
                </div>
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input" type="radio" name="correct_option" value="${optionCount}">
                    <label class="form-check-label ms-2">Doğru Cevap</label>
                </div>
                <button type="button" class="btn btn-danger ms-2 btn-remove-option">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(optionRow);
        });

        // Seçenek silme
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-option')) {
                const row = e.target.closest('.option-row');
                if (document.getElementById('options_container').children.length > 1) {
                    row.remove();
                }
            }
        });

        // Form gönderimi
        document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const questionType = formData.get('question_type');

            // Soru tipine göre doğru cevabı ayarla
            if (questionType === 'multiple_choice') {
                const correctOptionIndex = formData.get('correct_option');
                const options = formData.getAll('options[]');
                formData.append('correct_answer', options[correctOptionIndex]);
            } else if (questionType === 'true_false') {
                formData.append('correct_answer', formData.get('true_false_answer'));
            } else if (questionType === 'text') {
                formData.append('correct_answer', formData.get('text_answer'));
            }

            fetch('process_question.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Soru başarıyla eklendi.',
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
        });

        // Soru silme
        document.querySelectorAll('.btn-delete-question').forEach(button => {
            button.addEventListener('click', function() {
                const questionId = this.dataset.id;
                
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu soruyu silmek istediğinizden emin misiniz?",
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
                        formData.append('id', questionId);

                        fetch('process_question.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: 'Soru başarıyla silindi.',
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

        // Geçme notu düzenleme modalını aç
        function editPassingScore() {
            const modal = new bootstrap.Modal(document.getElementById('editPassingScoreModal'));
            modal.show();
        }

        // Geçme notu düzenleme formu gönderimi
        document.getElementById('editPassingScoreForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);

            fetch('process_question.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('passing_score').textContent = formData.get('passing_score');
                    bootstrap.Modal.getInstance(document.getElementById('editPassingScoreModal')).hide();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Geçme notu başarıyla güncellendi.',
                        showConfirmButton: false,
                        timer: 1500
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
        });

        // Soru düzenleme
        document.querySelectorAll('.btn-edit-question').forEach(button => {
            button.addEventListener('click', function() {
                const questionId = this.dataset.id;
                const questionCard = this.closest('.question-card');
                const questionType = questionCard.querySelector('.text-muted').textContent.includes('Çoktan Seçmeli') ? 'multiple_choice' :
                                   questionCard.querySelector('.text-muted').textContent.includes('Doğru/Yanlış') ? 'true_false' : 'text';
                
                // Form alanlarını doldur
                document.getElementById('edit_question_id').value = questionId;
                document.getElementById('edit_question_text').value = questionCard.querySelector('.card-title').textContent.trim();
                document.getElementById('edit_question_type').value = questionType;
                document.getElementById('edit_question_type_hidden').value = questionType;
                document.getElementById('edit_points').value = questionCard.querySelector('.text-muted').textContent.match(/Puan: (\d+)/)[1];

                // Soru tipine göre container'ları göster/gizle ve değerleri doldur
                document.getElementById('edit_multiple_choice_container').style.display = 'none';
                document.getElementById('edit_true_false_container').style.display = 'none';
                document.getElementById('edit_text_container').style.display = 'none';

                if (questionType === 'multiple_choice') {
                    document.getElementById('edit_multiple_choice_container').style.display = 'block';
                    const optionsContainer = document.getElementById('edit_options_container');
                    optionsContainer.innerHTML = '';

                    questionCard.querySelectorAll('.form-check').forEach((option, index) => {
                        const optionText = option.querySelector('.form-check-label').textContent.trim();
                        const isCorrect = option.querySelector('.form-check-input').checked;
                        
                        const optionRow = document.createElement('div');
                        optionRow.className = 'option-row d-flex mb-2';
                        optionRow.innerHTML = `
                            <div class="input-group">
                                <span class="input-group-text">${String.fromCharCode(65 + index)})</span>
                                <input type="text" class="form-control me-2" name="options[]" value="${optionText}" required>
                            </div>
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input" type="radio" name="correct_option" value="${index}" ${isCorrect ? 'checked' : ''}>
                                <label class="form-check-label ms-2">Doğru Cevap</label>
                            </div>
                        `;
                        optionsContainer.appendChild(optionRow);
                    });
                } else if (questionType === 'true_false') {
                    document.getElementById('edit_true_false_container').style.display = 'block';
                    const correctAnswer = questionCard.querySelector('.form-check-input:checked').closest('.form-check').querySelector('.form-check-label').textContent.trim() === 'Doğru' ? 'true' : 'false';
                    document.querySelector(`input[name="edit_true_false_answer"][value="${correctAnswer}"]`).checked = true;
                } else if (questionType === 'text') {
                    document.getElementById('edit_text_container').style.display = 'block';
                    document.getElementById('edit_text_answer').value = questionCard.querySelector('.mt-2').textContent.replace('Doğru Cevap:', '').trim();
                }

                // Modalı göster
                const modal = new bootstrap.Modal(document.getElementById('editQuestionModal'));
                modal.show();
            });
        });

        // Soru düzenleme formu gönderimi
        document.getElementById('editQuestionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const questionType = formData.get('question_type');

            // Soru tipine göre doğru cevabı ayarla
            if (questionType === 'multiple_choice') {
                const correctOptionIndex = formData.get('correct_option');
                const options = formData.getAll('options[]');
                formData.append('correct_answer', options[correctOptionIndex]);
            } else if (questionType === 'true_false') {
                formData.append('correct_answer', formData.get('edit_true_false_answer'));
            } else if (questionType === 'text') {
                formData.append('correct_answer', formData.get('text_answer'));
            }

            fetch('process_question.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Soru başarıyla güncellendi.',
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
        });

        // Chatbot işlemleri
        document.getElementById('chatForm').addEventListener('submit', async function(e) {
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
                    body: JSON.stringify({ 
                        message: userMessage,
                        context: 'question_creation', // Soru oluşturma bağlamı
                        test_id: <?php echo $test_id; ?> // Test ID'sini gönder
                    })
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
</body>
</html> 