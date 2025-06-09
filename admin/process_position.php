<?php
// Hata ayıklama için
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

// İstek metodunu kontrol et
$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    switch ($method) {
        case 'POST':
            if ($action === 'edit' && isset($_GET['id'])) {
                // Pozisyon düzenleme
                $position_id = (int)$_GET['id'];
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $requirements = $_POST['requirements'] ?? '';
                $status = isset($_POST['status']) ? 'active' : 'inactive';
                $selected_tests_data = isset($_POST['position_tests']) ? $_POST['position_tests'] : [];
                $portfolio_point = isset($_POST['portfolio_point']) ? (int)$_POST['portfolio_point'] : 0;
                $certificate_point = isset($_POST['certificate_point']) ? (int)$_POST['certificate_point'] : 0;
                $education_point = isset($_POST['education_point']) ? (int)$_POST['education_point'] : 0;
                $reference_point = isset($_POST['reference_point']) ? (int)$_POST['reference_point'] : 0;
                $experience_point = isset($_POST['experience_point']) ? (int)$_POST['experience_point'] : 0;

                if (empty($title)) {
                    throw new Exception('Pozisyon adı zorunludur');
                }

                // Pozisyonu güncelle
                $query = "UPDATE positions SET title = :title, description = :description, 
                         requirements = :requirements, status = :status,
                         portfolio_point = :portfolio_point, certificate_point = :certificate_point, education_point = :education_point, reference_point = :reference_point, experience_point = :experience_point
                         WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':requirements', $requirements);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':portfolio_point', $portfolio_point);
                $stmt->bindParam(':certificate_point', $certificate_point);
                $stmt->bindParam(':education_point', $education_point);
                $stmt->bindParam(':reference_point', $reference_point);
                $stmt->bindParam(':experience_point', $experience_point);
                $stmt->bindParam(':id', $position_id);
                
                if (!$stmt->execute()) {
                    throw new Exception('Pozisyon güncellenirken bir hata oluştu');
                }

                // Mevcut testleri sil
                $query = "DELETE FROM position_tests WHERE position_id = :position_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':position_id', $position_id);
                $stmt->execute();

                // Yeni testleri ekle (zorunluluk durumu ile birlikte)
                if (!empty($selected_tests_data)) {
                    $query = "INSERT INTO position_tests (position_id, test_id, required) VALUES (:position_id, :test_id, :required)";
                    $stmt = $db->prepare($query);
                    
                    foreach ($selected_tests_data as $test_data) {
                        $test_id = $test_data['test_id'];
                        // Checkbox değeri gelmezse (işaretli değilse) 0 olarak al
                        $required = isset($test_data['required']) ? 1 : 0;

                        $stmt->bindParam(':position_id', $position_id);
                        $stmt->bindParam(':test_id', $test_id);
                        $stmt->bindParam(':required', $required, PDO::PARAM_INT);
                        
                        if (!$stmt->execute()) {
                            throw new Exception('Test ilişkilendirilirken bir hata oluştu (Düzenleme)');
                        }
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Pozisyon başarıyla güncellendi']);
                exit;
            } else if ($action === 'delete' && isset($_POST['id'])) {
                $position_id = (int)$_POST['id'];
                $log = [];
                try {
                    // 1. Pozisyona bağlı başvuruları bul
                    $stmt = $db->prepare("SELECT id FROM applications WHERE position_id = ?");
                    $stmt->execute([$position_id]);
                    $application_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $log[] = 'Bulunan başvuru sayısı: ' . count($application_ids);

                    foreach ($application_ids as $application_id) {
                        // Test result id'lerini bul
                        $stmtRes = $db->prepare("SELECT id FROM test_results WHERE application_id = ?");
                        $stmtRes->execute([$application_id]);
                        $test_result_ids = $stmtRes->fetchAll(PDO::FETCH_COLUMN);
                        $log[] = "Başvuru $application_id için test_result_ids: " . json_encode($test_result_ids);

                        // Test cevaplarını sil (sadece test_result_ids boş değilse)
                        if (!empty($test_result_ids)) {
                            $in = implode(',', array_fill(0, count($test_result_ids), '?'));
                            $sql = "DELETE FROM test_answers WHERE test_result_id IN ($in)";
                            $stmtDel = $db->prepare($sql);
                            if (!$stmtDel->execute($test_result_ids)) {
                                throw new Exception('test_answers silinemedi: ' . json_encode($stmtDel->errorInfo()));
                            }
                            $log[] = 'test_answers silindi';
                        } else {
                            $log[] = 'test_answers silinecek test_result_id yok';
                        }

                        // Test sonuçlarını sil
                        $stmtDel = $db->prepare("DELETE FROM test_results WHERE application_id = ?");
                        if (!$stmtDel->execute([$application_id])) {
                            throw new Exception('test_results silinemedi: ' . json_encode($stmtDel->errorInfo()));
                        }
                        $log[] = 'test_results silindi';

                        // Başvuruyu sil
                        $stmtDel = $db->prepare("DELETE FROM applications WHERE id = ?");
                        if (!$stmtDel->execute([$application_id])) {
                            throw new Exception('applications silinemedi: ' . json_encode($stmtDel->errorInfo()));
                        }
                        $log[] = 'applications silindi';
                    }

                    // 2. Pozisyon-test ilişkilerini sil
                    $stmt = $db->prepare("DELETE FROM position_tests WHERE position_id = ?");
                    if (!$stmt->execute([$position_id])) {
                        throw new Exception('position_tests silinemedi: ' . json_encode($stmt->errorInfo()));
                    }
                    $log[] = 'position_tests silindi';

                    // 3. Pozisyonu sil
                    $stmt = $db->prepare("DELETE FROM positions WHERE id = ?");
                    if (!$stmt->execute([$position_id])) {
                        throw new Exception('positions silinemedi: ' . json_encode($stmt->errorInfo()));
                    }
                    $log[] = 'positions silindi';

                    echo json_encode(['success' => true, 'message' => 'Pozisyon başarıyla silindi', 'log' => $log]);
                } catch (Exception $e) {
                    $log[] = 'Hata: ' . $e->getMessage();
                    echo json_encode(['success' => false, 'error' => 'Pozisyon silinirken bir hata oluştu: ' . $e->getMessage(), 'log' => $log]);
                }
                exit;
            } else if ($action === 'create') {
                // Yeni pozisyon ekleme
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $requirements = $_POST['requirements'] ?? '';
                $status = isset($_POST['status']) ? $_POST['status'] : 'active';
                $selected_tests_data = isset($_POST['position_tests']) ? $_POST['position_tests'] : [];
                $portfolio_point = isset($_POST['portfolio_point']) ? (int)$_POST['portfolio_point'] : 0;
                $certificate_point = isset($_POST['certificate_point']) ? (int)$_POST['certificate_point'] : 0;
                $education_point = isset($_POST['education_point']) ? (int)$_POST['education_point'] : 0;
                $reference_point = isset($_POST['reference_point']) ? (int)$_POST['reference_point'] : 0;
                $experience_point = isset($_POST['experience_point']) ? (int)$_POST['experience_point'] : 0;

                // Pozisyonu ekle
                $query = "INSERT INTO positions (title, description, requirements, status, portfolio_point, certificate_point, education_point, reference_point, experience_point) VALUES (:title, :description, :requirements, :status, :portfolio_point, :certificate_point, :education_point, :reference_point, :experience_point)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':requirements', $requirements);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':portfolio_point', $portfolio_point);
                $stmt->bindParam(':certificate_point', $certificate_point);
                $stmt->bindParam(':education_point', $education_point);
                $stmt->bindParam(':reference_point', $reference_point);
                $stmt->bindParam(':experience_point', $experience_point);
                
                if (!$stmt->execute()) {
                    throw new Exception('Pozisyon eklenirken bir hata oluştu');
                }
                
                $position_id = $db->lastInsertId();

                // Seçilen testleri ekle (zorunluluk durumu ile birlikte)
                if (!empty($selected_tests_data)) {
                    $query = "INSERT INTO position_tests (position_id, test_id, required) VALUES (:position_id, :test_id, :required)";
                    $stmt = $db->prepare($query);
                    
                    foreach ($selected_tests_data as $test_data) {
                         $test_id = $test_data['test_id'];
                        // Checkbox değeri gelmezse (işaretli değilse) 0 olarak al
                        $required = isset($test_data['required']) ? 1 : 0;

                        $stmt->bindParam(':position_id', $position_id);
                        $stmt->bindParam(':test_id', $test_id);
                        $stmt->bindParam(':required', $required, PDO::PARAM_INT);
                        
                        if (!$stmt->execute()) {
                            throw new Exception('Test ilişkilendirilirken bir hata oluştu (Ekleme)');
                        }
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Pozisyon başarıyla eklendi']);
                exit;
            }
            break;

        case 'GET':
            if ($action === 'delete' && isset($_GET['id'])) {
                // Pozisyon silme (GET ile)
                $position_id = (int)$_GET['id'];
                $log = [];
                try {
                    // 1. Pozisyona bağlı başvuruları bul
                    $stmt = $db->prepare("SELECT id FROM applications WHERE position_id = ?");
                    $stmt->execute([$position_id]);
                    $application_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $log[] = 'Bulunan başvuru sayısı: ' . count($application_ids);

                    foreach ($application_ids as $application_id) {
                        // Test result id'lerini bul
                        $stmtRes = $db->prepare("SELECT id FROM test_results WHERE application_id = ?");
                        $stmtRes->execute([$application_id]);
                        $test_result_ids = $stmtRes->fetchAll(PDO::FETCH_COLUMN);
                        $log[] = "Başvuru $application_id için test_result_ids: " . json_encode($test_result_ids);

                        // Test cevaplarını sil (sadece test_result_ids boş değilse)
                        if (!empty($test_result_ids)) {
                            $in = implode(',', array_fill(0, count($test_result_ids), '?'));
                            $sql = "DELETE FROM test_answers WHERE test_result_id IN ($in)";
                            $stmtDel = $db->prepare($sql);
                            if (!$stmtDel->execute($test_result_ids)) {
                                throw new Exception('test_answers silinemedi: ' . json_encode($stmtDel->errorInfo()));
                            }
                            $log[] = 'test_answers silindi';
                        } else {
                            $log[] = 'test_answers silinecek test_result_id yok';
                        }

                        // Test sonuçlarını sil
                        $stmtDel = $db->prepare("DELETE FROM test_results WHERE application_id = ?");
                        if (!$stmtDel->execute([$application_id])) {
                            throw new Exception('test_results silinemedi: ' . json_encode($stmtDel->errorInfo()));
                        }
                        $log[] = 'test_results silindi';

                        // Başvuruyu sil
                        $stmtDel = $db->prepare("DELETE FROM applications WHERE id = ?");
                        if (!$stmtDel->execute([$application_id])) {
                            throw new Exception('applications silinemedi: ' . json_encode($stmtDel->errorInfo()));
                        }
                        $log[] = 'applications silindi';
                    }

                    // 2. Pozisyon-test ilişkilerini sil
                    $stmt = $db->prepare("DELETE FROM position_tests WHERE position_id = ?");
                    if (!$stmt->execute([$position_id])) {
                        throw new Exception('position_tests silinemedi: ' . json_encode($stmt->errorInfo()));
                    }
                    $log[] = 'position_tests silindi';

                    // 3. Pozisyonu sil
                    $stmt = $db->prepare("DELETE FROM positions WHERE id = ?");
                    if (!$stmt->execute([$position_id])) {
                        throw new Exception('positions silinemedi: ' . json_encode($stmt->errorInfo()));
                    }
                    $log[] = 'positions silindi';

                    echo json_encode(['success' => true, 'message' => 'Pozisyon başarıyla silindi', 'log' => $log]);
                } catch (Exception $e) {
                    $log[] = 'Hata: ' . $e->getMessage();
                    echo json_encode(['success' => false, 'error' => 'Pozisyon silinirken bir hata oluştu: ' . $e->getMessage(), 'log' => $log]);
                }
                exit;
            } else if ($action === 'get' && isset($_GET['id'])) {
                // Pozisyon bilgilerini getir
                $position_id = (int)$_GET['id'];

                // Pozisyon bilgilerini ve ilişkili testlerin ID'leri ile zorunluluk durumunu getir
                $query = "SELECT p.*, 
                         pt.test_id, pt.required, t.title as test_title
                         FROM positions p 
                         LEFT JOIN position_tests pt ON p.id = pt.position_id 
                         LEFT JOIN tests t ON pt.test_id = t.id
                         WHERE p.id = :id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $position_id);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$results) {
                     // Pozisyon bulunamazsa veya test ilişkisi yoksa boş test ve zorunluluk dizileri döndür
                     $query = "SELECT * FROM positions WHERE id = :id";
                     $stmt = $db->prepare($query);
                     $stmt->bindParam(':id', $position_id);
                     $stmt->execute();
                     $position = $stmt->fetch(PDO::FETCH_ASSOC);
                     if (!$position) {
                         throw new Exception('Pozisyon bulunamadı');
                     }
                     $position['test_ids'] = [];
                     $position['tests_with_required'] = [];
                     echo json_encode(['success' => true, 'data' => $position]);
                     exit;
                }

                // Pozisyon ana bilgilerini al (ilk satırdan)
                $position = $results[0];
                unset($position['test_id'], $position['required'], $position['test_title']); // Tekrarlanan test bilgilerini kaldır

                // Test ID'leri ve zorunluluk durumlarını ayrı dizilerde topla
                $test_ids = [];
                $tests_with_required = [];
                foreach ($results as $row) {
                    if ($row['test_id'] !== null) {
                         $test_ids[] = (int)$row['test_id'];
                         $tests_with_required[] = [
                             'test_id' => (int)$row['test_id'],
                             'test_title' => $row['test_title'],
                             'required' => (bool)$row['required'] // Boolean olarak saklayalım
                         ];
                    }
                }

                $position['test_ids'] = $test_ids;
                $position['tests_with_required'] = $tests_with_required;

                echo json_encode(['success' => true, 'data' => $position]);
                exit;
            } else {
                throw new Exception('Geçersiz işlem');
            }
            break;

        default:
            throw new Exception('Geçersiz istek metodu');
    }
} catch (Exception $e) {
    error_log("Error in process_position.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}