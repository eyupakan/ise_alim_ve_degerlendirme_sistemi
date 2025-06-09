<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Fetch active candidates for the modal dropdown
    $query = "SELECT 
                c.id, 
                CONCAT(c.first_name, ' ', c.last_name) as full_name 
              FROM candidates c
              JOIN applications a ON c.id = a.candidate_id
              WHERE a.status = 'accepted'
              GROUP BY c.id, full_name
              ORDER BY full_name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mülakatlar - Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
    <!-- Add your custom admin styles here if needed -->
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
        #calendar {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
         .fc-event {
            cursor: pointer;
        }
        .interview-pending {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        }
        .interview-confirmed {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }
        .interview-completed {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }
        .interview-cancelled {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
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
                            <a class="nav-link active" href="interviews.php">
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
                    <h2 class="mb-0">Mülakat Yönetimi</h2>
                     <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInterviewModal">
                        <i class="bi bi-plus-circle"></i> Mülakat Ekle
                    </button>
                </div>

                <!-- Interview Calendar Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Mülakat Takvimi</h5>
                        <div id="calendar"></div>
                    </div>
                </div>

                <!-- Add Interview Modal -->
                <div class="modal fade" id="addInterviewModal" tabindex="-1" aria-labelledby="addInterviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addInterviewModalLabel">Yeni Mülakat Ekle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="add_interview.php" method="POST">
                                    <div class="mb-3">
                                        <label for="application_id" class="form-label">Aday Seçin</label>
                                        <select class="form-select" id="application_id" name="application_id" required>
                                            <option value="">Başvuru Seçin</option>
                                            <?php foreach ($candidates as $candidate): ?>
                                                <option value="<?php echo $candidate['id']; ?>">
                                                    <?php echo htmlspecialchars($candidate['full_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="interview_date" class="form-label">Mülakat Tarihi ve Saati</label>
                                        <input type="datetime-local" class="form-control" id="interview_date" name="interview_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="interview_type" class="form-label">Mülakat Türü</label>
                                        <select class="form-select" id="interview_type" name="interview_type" required>
                                            <option value="online">Online</option>
                                            <option value="in_person">Yüz yüze</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="meetingLinkDiv">
                                        <label for="meeting_link" class="form-label">Toplantı Linki</label>
                                        <input type="url" class="form-control" id="meeting_link" name="meeting_link">
                                    </div>
                                    <div class="mb-3" id="locationDiv" style="display:none;">
                                        <label for="location" class="form-label">Konum</label>
                                        <input type="text" class="form-control" id="location" name="location">
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notlar</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                        <button type="submit" class="btn btn-primary">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'tr',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: 'get_interviews.php', // AJAX ile mülakatları çekecek endpoint
                eventClick: function(info) {
                    // Mülakat detaylarını göster
                    window.location.href = 'interview_detail.php?id=' + info.event.id;
                },
                eventClassNames: function(arg) {
                    return ['interview-' + arg.event.extendedProps.status];
                }
            });
            calendar.render();

            // Mülakat türü değiştiğinde ilgili alanları göster/gizle
            document.querySelector('select[name="interview_type"]').addEventListener('change', function() {
                const meetingLinkDiv = document.getElementById('meetingLinkDiv');
                const locationDiv = document.getElementById('locationDiv');
                
                if (this.value === 'online') {
                    meetingLinkDiv.style.display = 'block';
                    locationDiv.style.display = 'none';
                    // Update required attributes
                    document.querySelector('#locationDiv input').removeAttribute('required');
                    document.querySelector('#meetingLinkDiv input').setAttribute('required', 'required');
                } else {
                    meetingLinkDiv.style.display = 'none';
                    locationDiv.style.display = 'block';
                    // Update required attributes
                    document.querySelector('#meetingLinkDiv input').removeAttribute('required');
                    document.querySelector('#locationDiv input').setAttribute('required', 'required');
                }
            });

            // Initial check on page load
            const interviewTypeSelect = document.querySelector('select[name="interview_type"]');
            const meetingLinkDiv = document.getElementById('meetingLinkDiv');
            const locationDiv = document.getElementById('locationDiv');
            
            if (interviewTypeSelect.value === 'online') {
                meetingLinkDiv.style.display = 'block';
                locationDiv.style.display = 'none';
                document.querySelector('#meetingLinkDiv input').setAttribute('required', 'required');
            } else {
                meetingLinkDiv.style.display = 'none';
                locationDiv.style.display = 'block';
                document.querySelector('#locationDiv input').setAttribute('required', 'required');
            }
        });
    </script>
</body>
</html>