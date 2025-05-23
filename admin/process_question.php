<?php
require_once 'auth_check.php';
require_once '../config/database.php';

// Hata raporlamayı aktifleştir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// POST method kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Action kontrolü
if (!isset($_POST['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No action specified']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    switch ($_POST['action']) {
        case 'update':
            // Gerekli alanların kontrolü
            if (!isset($_POST['question_id']) || !is_numeric($_POST['question_id'])) {
                throw new Exception('Geçersiz soru ID.');
            }

            $required_fields = ['question_text', 'question_type', 'points', 'correct_answer'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("$field alanı gereklidir.");
                }
            }

            $db->beginTransaction();

            try {
                // Soruyu güncelle
                $query = "UPDATE test_questions SET 
                         question_text = :question_text,
                         points = :points,
                         correct_answer = :correct_answer
                         WHERE id = :question_id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':question_text', $_POST['question_text']);
                $stmt->bindParam(':points', $_POST['points']);
                $stmt->bindParam(':correct_answer', $_POST['correct_answer']);
                $stmt->bindParam(':question_id', $_POST['question_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Soru güncellenirken bir hata oluştu.');
                }

                // Çoktan seçmeli soru ise seçenekleri güncelle
                if ($_POST['question_type'] === 'multiple_choice' && isset($_POST['options']) && is_array($_POST['options'])) {
                    // Önce eski seçenekleri sil
                    $query = "DELETE FROM question_options WHERE question_id = :question_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':question_id', $_POST['question_id']);
                    $stmt->execute();

                    // Yeni seçenekleri ekle
                    $query = "INSERT INTO question_options (question_id, option_text) VALUES (:question_id, :option_text)";
                    $stmt = $db->prepare($query);

                    foreach ($_POST['options'] as $option) {
                        if (!empty($option)) {
                            $stmt->bindParam(':question_id', $_POST['question_id']);
                            $stmt->bindParam(':option_text', $option);
                            if (!$stmt->execute()) {
                                throw new Exception('Seçenek güncellenirken bir hata oluştu.');
                            }
                        }
                    }
                }

                $db->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Soru başarıyla güncellendi.'
                ]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'update_passing_score':
            // Test ID ve geçme notu kontrolü
            if (!isset($_POST['test_id']) || !is_numeric($_POST['test_id'])) {
                throw new Exception('Geçersiz test ID.');
            }
            
            if (!isset($_POST['passing_score']) || !is_numeric($_POST['passing_score'])) {
                throw new Exception('Geçersiz geçme notu.');
            }
            
            $passing_score = (int)$_POST['passing_score'];
            if ($passing_score < 0 || $passing_score > 100) {
                throw new Exception('Geçme notu 0-100 arasında olmalıdır.');
            }

            // Geçme notunu güncelle
            $query = "UPDATE tests SET passing_score = :passing_score WHERE id = :test_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':passing_score', $passing_score);
            $stmt->bindParam(':test_id', $_POST['test_id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Geçme notu güncellenirken bir hata oluştu.');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Geçme notu başarıyla güncellendi.'
            ]);
            break;

        case 'create':
            // Gerekli alanların kontrolü
            $required_fields = ['test_id', 'question_text', 'question_type', 'points'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("$field alanı gereklidir.");
                }
            }

            // Soru tipine göre doğru cevap kontrolü
            if (!isset($_POST['correct_answer']) || empty($_POST['correct_answer'])) {
                throw new Exception('Doğru cevap belirtilmelidir.');
            }

            $db->beginTransaction();

            try {
                // Soruyu ekle
                $query = "INSERT INTO test_questions (test_id, question_text, question_type, points, correct_answer) 
                         VALUES (:test_id, :question_text, :question_type, :points, :correct_answer)";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':test_id', $_POST['test_id']);
                $stmt->bindParam(':question_text', $_POST['question_text']);
                $stmt->bindParam(':question_type', $_POST['question_type']);
                $stmt->bindParam(':points', $_POST['points']);
                $stmt->bindParam(':correct_answer', $_POST['correct_answer']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Soru eklenirken bir hata oluştu.');
                }

                $question_id = $db->lastInsertId();

                // Çoktan seçmeli soru ise seçenekleri ekle
                if ($_POST['question_type'] === 'multiple_choice' && isset($_POST['options']) && is_array($_POST['options'])) {
                    $query = "INSERT INTO question_options (question_id, option_text) VALUES (:question_id, :option_text)";
                    $stmt = $db->prepare($query);

                    foreach ($_POST['options'] as $option) {
                        if (!empty($option)) {
                            $stmt->bindParam(':question_id', $question_id);
                            $stmt->bindParam(':option_text', $option);
                            if (!$stmt->execute()) {
                                throw new Exception('Seçenek eklenirken bir hata oluştu.');
                            }
                        }
                    }
                }

                $db->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Soru başarıyla eklendi.',
                    'question_id' => $question_id
                ]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'delete':
            // ID kontrolü
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
                throw new Exception('Geçersiz soru ID.');
            }

            $db->beginTransaction();

            try {
                // Önce seçenekleri sil
                $query = "DELETE FROM question_options WHERE question_id = :question_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':question_id', $_POST['id']);
                $stmt->execute();

                // Sonra soruyu sil
                $query = "DELETE FROM test_questions WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Soru silinirken bir hata oluştu.');
                }

                $db->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Soru başarıyla silindi.'
                ]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        default:
            throw new Exception('Geçersiz işlem.');
    }
} catch (Exception $e) {
    error_log('Error in process_question.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?> 