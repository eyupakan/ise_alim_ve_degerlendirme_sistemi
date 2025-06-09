<?php
require_once 'config/database.php';

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

<div class="row justify-content-center" style="min-height: 100vh; padding: 2rem 0;">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="border-radius: 12px 12px 0 0;">
                <h3 class="card-title mb-0"><?php echo htmlspecialchars($position['title']); ?> - Kişisel Bilgiler</h3>
            </div>
            <div class="card-body">
                <!-- Progress Steps -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center stepper">
                        <div class="step active">
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

                <form action="process_step1.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="position_id" value="<?php echo $position_id; ?>">
                    
                    <div class="mb-3">
                        <label for="first_name" class="form-label" style="font-weight: 500;">Ad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label" style="font-weight: 500;">Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label" style="font-weight: 500;">E-posta <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label" style="font-weight: 500;">Telefon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label" style="font-weight: 500;">Şehir <span class="text-danger">*</span></label>
                        <select class="form-select" id="city" name="city" required>
                            <option value="">Şehir Seçiniz</option>
                            <option value="İstanbul">İstanbul</option>
                            <option value="Ankara">Ankara</option>
                            <option value="İzmir">İzmir</option>
                            <option value="Bursa">Bursa</option>
                            <option value="Antalya">Antalya</option>
                            <option value="Adana">Adana</option>
                            <option value="Adıyaman">Adıyaman</option>
                            <option value="Afyonkarahisar">Afyonkarahisar</option>
                            <option value="Ağrı">Ağrı</option>
                            <option value="Amasya">Amasya</option>
                            <option value="Artvin">Artvin</option>
                            <option value="Aydın">Aydın</option>
                            <option value="Balıkesir">Balıkesir</option>
                            <option value="Bartın">Bartın</option>
                            <option value="Batman">Batman</option>
                            <option value="Bayburt">Bayburt</option>
                            <option value="Bilecik">Bilecik</option>
                            <option value="Bingöl">Bingöl</option>
                            <option value="Bitlis">Bitlis</option>
                            <option value="Bolu">Bolu</option>
                            <option value="Burdur">Burdur</option>
                            <option value="Çanakkale">Çanakkale</option>
                            <option value="Çankırı">Çankırı</option>
                            <option value="Çorum">Çorum</option>
                            <option value="Denizli">Denizli</option>
                            <option value="Diyarbakır">Diyarbakır</option>
                            <option value="Düzce">Düzce</option>
                            <option value="Edirne">Edirne</option>
                            <option value="Elazığ">Elazığ</option>
                            <option value="Erzincan">Erzincan</option>
                            <option value="Erzurum">Erzurum</option>
                            <option value="Eskişehir">Eskişehir</option>
                            <option value="Gaziantep">Gaziantep</option>
                            <option value="Giresun">Giresun</option>
                            <option value="Gümüşhane">Gümüşhane</option>
                            <option value="Hakkari">Hakkari</option>
                            <option value="Hatay">Hatay</option>
                            <option value="Iğdır">Iğdır</option>
                            <option value="Isparta">Isparta</option>
                            <option value="Kahramanmaraş">Kahramanmaraş</option>
                            <option value="Karabük">Karabük</option>
                            <option value="Karaman">Karaman</option>
                            <option value="Kars">Kars</option>
                            <option value="Kastamonu">Kastamonu</option>
                            <option value="Kayseri">Kayseri</option>
                            <option value="Kilis">Kilis</option>
                            <option value="Kırıkkale">Kırıkkale</option>
                            <option value="Kırklareli">Kırklareli</option>
                            <option value="Kırşehir">Kırşehir</option>
                            <option value="Kocaeli">Kocaeli</option>
                            <option value="Konya">Konya</option>
                            <option value="Kütahya">Kütahya</option>
                            <option value="Malatya">Malatya</option>
                            <option value="Manisa">Manisa</option>
                            <option value="Mardin">Mardin</option>
                            <option value="Mersin">Mersin</option>
                            <option value="Muğla">Muğla</option>
                            <option value="Muş">Muş</option>
                            <option value="Nevşehir">Nevşehir</option>
                            <option value="Niğde">Niğde</option>
                            <option value="Ordu">Ordu</option>
                            <option value="Osmaniye">Osmaniye</option>
                            <option value="Rize">Rize</option>
                            <option value="Sakarya">Sakarya</option>
                            <option value="Samsun">Samsun</option>
                            <option value="Siirt">Siirt</option>
                            <option value="Sinop">Sinop</option>
                            <option value="Sivas">Sivas</option>
                            <option value="Şanlıurfa">Şanlıurfa</option>
                            <option value="Şırnak">Şırnak</option>
                            <option value="Tekirdağ">Tekirdağ</option>
                            <option value="Tokat">Tokat</option>
                            <option value="Trabzon">Trabzon</option>
                            <option value="Tunceli">Tunceli</option>
                            <option value="Uşak">Uşak</option>
                            <option value="Van">Van</option>
                            <option value="Yalova">Yalova</option>
                            <option value="Yozgat">Yozgat</option>
                            <option value="Zonguldak">Zonguldak</option>
                        </select>
                    </div>

                    <!-- Yeni Alanlar -->
                    <div class="mb-3">
                        <label for="birth_date" class="form-label" style="font-weight: 500;">Doğum Tarihi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label" style="font-weight: 500;">Cinsiyet <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Seçiniz</option>
                            <option value="male">Erkek</option>
                            <option value="female">Kadın</option>
                            <option value="other">Belirtmek İstemiyorum</option>
                        </select>
                    </div>

                     <div class="mb-3">
                        <label for="address" class="form-label" style="font-weight: 500;">Adres <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <!-- Yeni Alanlar Sonu -->

                    <div class="mb-3">
                        <label for="photo" class="form-label" style="font-weight: 500;">Fotoğraf <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                        <small class="text-muted" style="font-size: 12px;">Maksimum dosya boyutu: 2MB. İzin verilen formatlar: JPG, PNG</small>
                    </div>

                    <div class="mb-3">
                        <label for="linkedin_url" class="form-label" style="font-weight: 500;">LinkedIn Profili</label>
                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url">
                    </div>

                    <div class="mb-3">
                        <label for="github_url" class="form-label" style="font-weight: 500;">GitHub Profili</label>
                        <input type="url" class="form-control" id="github_url" name="github_url">
                    </div>

                    <div class="mb-3">
                        <label for="portfolio_url" class="form-label" style="font-weight: 500;">Portföy URL</label>
                        <input type="url" class="form-control" id="portfolio_url" name="portfolio_url">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500; transition: all 0.2s ease;">
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

<?php require_once 'includes/footer.php'; ?> 