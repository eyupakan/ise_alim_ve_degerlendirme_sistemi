<?php
require_once 'auth_check.php';
require_once '../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = new Database();
$db = $database->getConnection();

// Testleri getir
$query = "SELECT t.*, 
          COUNT(DISTINCT q.id) as question_count
          FROM tests t
          LEFT JOIN test_questions q ON t.id = q.test_id
          GROUP BY t.id
          ORDER BY t.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testler - Admin Paneli</title>
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
                    <h2>Testler</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTestModal">
                        <i class="bi bi-plus-circle"></i> Yeni Test Oluştur
                    </button>
                </div>

                <!-- Test Listesi -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Test Adı</th>
                                        <th>Açıklama</th>
                                        <th>Süre (dk)</th>
                                        <th>Soru Sayısı</th>
                                        <th>Oluşturulma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tests as $test): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($test['title']); ?></td>
                                        <td><?php echo htmlspecialchars($test['description']); ?></td>
                                        <td><?php echo $test['time_limit']; ?> dk</td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $test['question_count']; ?> Soru
                                            </span>
                                        </td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($test['created_at'])); ?></td>
                                        <td>
                                            <a href="test_questions.php?test_id=<?php echo $test['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-list-check"></i> Soruları Düzenle
                                            </a>
                                            <button class="btn btn-sm btn-danger btn-delete-test" data-id="<?php echo $test['id']; ?>">
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

    <!-- Test Oluşturma Modal -->
    <div class="modal fade" id="createTestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Test Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createTestForm" action="process_test.php" method="POST">
                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="title" class="form-label">Test Adı</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Süre (dakika)</label>
                            <input type="number" class="form-control" id="time_limit" name="time_limit" value="30" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="passing_score" class="form-label">Geçme Notu</label>
                            <input type="number" class="form-control" id="passing_score" name="passing_score" value="70" min="0" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" form="createTestForm" class="btn btn-primary">Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chatbot Modal -->
    <div class="modal fade" id="chatbotModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Asistanı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="chat-container" style="height: 400px; overflow-y: auto; padding: 15px;">
                        <div id="chatMessages" class="mb-3">
                            <!-- Mesajlar buraya gelecek -->
                            <div class="chat-message assistant">
                                <div class="message-content">
                                    Merhaba! Ben Test Asistanınızım. Size test oluşturma ve soru hazırlama konusunda yardımcı olabilirim. Nasıl yardımcı olabilirim?
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
        <i class="bi bi-chat-dots"></i> Test Asistanı
    </button>

    <style>
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

    <script>
        // Test silme işlemi
        document.querySelectorAll('.btn-delete-test').forEach(button => {
            button.addEventListener('click', function() {
                const testId = this.dataset.id;
                
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu testi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!",
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
                        formData.append('id', testId);

                        fetch('process_test.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: 'Test başarıyla silindi.',
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

        // Form submit işlemi
        document.getElementById('createTestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('process_test.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Test başarıyla oluşturuldu.',
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
                        context: 'test_creation' // Test oluşturma bağlamı
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