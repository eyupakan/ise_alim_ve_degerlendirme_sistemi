<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen verileri al
    $position_id = $_POST['position_id'] ?? null;
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $city = $_POST['city'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $linkedin_url = $_POST['linkedin_url'] ?? '';
    $github_url = $_POST['github_url'] ?? '';
    $portfolio_url = $_POST['portfolio_url'] ?? '';
    $birth_date = $_POST['birth_date'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';

    // Cinsiyet kontrolü
    if (!in_array($gender, ['male', 'female', 'other'])) {
        die("Geçersiz cinsiyet seçimi.");
    }

    $photo = null;

    // Fotoğraf yükleme işlemi
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('photo_') . '.' . $ext;
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $photo = $target_path;
        }
    }

    try {
        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();

        // Aday kaydı oluştur
        $stmt = $db->prepare("INSERT INTO candidates (first_name, last_name, city, email, phone, linkedin_url, github_url, portfolio_url, photo_path, birth_date, gender, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$first_name, $last_name, $city, $email, $phone, $linkedin_url, $github_url, $portfolio_url, $photo, $birth_date, $gender, $address]);
        $candidate_id = $db->lastInsertId();

        // Başvuru kaydı oluştur
        $stmt = $db->prepare("INSERT INTO applications (candidate_id, position_id, status, current_step, created_at) VALUES (?, ?, 'draft', 2, NOW())");
        $stmt->execute([$candidate_id, $position_id]);
        $application_id = $db->lastInsertId();

        $db->commit();
        // 2. adıma yönlendir
        header('Location: apply_step2.php?application_id=' . $application_id);
        exit;
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        die('Bir hata oluştu: ' . $e->getMessage());
    }
}

// Pozisyon ID kontrolü
if (!isset($_GET['position_id'])) {
    header('Location: index.php');
    exit;
}

$position_id = $_GET['position_id'];

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Pozisyon bilgilerini al
    $stmt = $db->prepare("SELECT * FROM positions WHERE id = ? AND status = 'active'");
    $stmt->execute([$position_id]);
    $position = $stmt->fetch();

    if (!$position) {
        header('Location: index.php');
        exit;
    }

    $page_title = "Başvuru - " . $position['title'] . " (Adım 1/4)";
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
                    <?php echo htmlspecialchars($position['title']); ?> - Kişisel Bilgiler
                </h3>
            </div>
            <div class="card-body">
                <!-- Progress bar -->
                <div class="progress mb-4" style="height: 2px;">
                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-center mb-4">
                    <small class="text-muted">Adım 1/4 - Kişisel Bilgiler</small>
                </div>

                <form action="process_step1.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="position_id" value="<?php echo $position_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Ad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">Şehir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <label for="linkedin_url" class="form-label">LinkedIn Profili</label>
                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" placeholder="https://www.linkedin.com/in/...">
                    </div>

                    <div class="mb-3">
                        <label for="github_url" class="form-label">GitHub Profili</label>
                        <input type="url" class="form-control" id="github_url" name="github_url" placeholder="https://github.com/...">
                    </div>

                    <div class="mb-3">
                        <label for="portfolio_url" class="form-label">Portfolyo Sitesi</label>
                        <input type="url" class="form-control" id="portfolio_url" name="portfolio_url" placeholder="https://...">
                    </div>

                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Doğum Tarihi</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date">
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Cinsiyet <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Seçiniz</option>
                            <option value="male">Erkek</option>
                            <option value="female">Kadın</option>
                            <option value="other">Belirtmek İstemiyorum</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Adres</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Fotoğraf</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Maksimum dosya boyutu: 2MB. İzin verilen formatlar: JPG, PNG</small>
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

<?php require_once 'includes/footer.php'; ?> 