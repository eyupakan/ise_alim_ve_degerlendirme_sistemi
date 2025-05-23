<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Form verilerini al
$test_result_id = $_POST['test_result_id'] ?? '';
$application_id = $_POST['application_id'] ?? '';
$test_id = $_POST['test_id'] ?? '';
$answers = $_POST['answers'] ?? [];

try {
    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();

    // Test sonuç kaydını kontrol et
    $stmt = $db->prepare("
        SELECT tr.*, t.total_points as max_points, t.passing_score
        FROM test_results tr
        JOIN tests t ON tr.test_id = t.id
        WHERE tr.id = ? AND tr.application_id = ? AND tr.test_id = ?
    ");
    $stmt->execute([$test_result_id, $application_id, $test_id]);
    $test_result = $stmt->fetch();

    if (!$test_result) {
        die("Geçersiz test sonucu.");
    }

    // Test zaten tamamlanmışsa
    if ($test_result['status'] === 'completed') {
        header("Location: apply_step4.php?application_id=" . $application_id);
        exit;
    }

    // Veritabanı işlemleri için transaction başlat
    $db->beginTransaction();

    $total_points_earned = 0;
    $total_possible_points = 0;

    // Her soru için cevapları kontrol et ve kaydet
    foreach ($answers as $question_id => $answer) {
        // Soru bilgilerini al
        $stmt = $db->prepare("
            SELECT * FROM test_questions 
            WHERE id = ? AND test_id = ?
        ");
        $stmt->execute([$question_id, $test_id]);
        $question = $stmt->fetch();

        if (!$question) continue;

        $is_correct = 0; // Varsayılan olarak yanlış
        $points_earned = 0;

        // Soru tipine göre doğruluk kontrolü yap
        if ($question['question_type'] === 'multiple_choice') {
            // Seçenek doğruluğunu kontrol et
            $stmt = $db->prepare("
                SELECT is_correct FROM question_options 
                WHERE id = ? AND question_id = ?
            ");
            $stmt->execute([$answer, $question_id]);
            $option = $stmt->fetch();
            
            if ($option) {
                $is_correct = (int)$option['is_correct'];
            }
        } elseif ($question['question_type'] === 'true_false') {
            $is_correct = (int)($answer === $question['correct_answer']);
        } else {
            // Metin soruları için tam puan ver (manuel değerlendirme gerekir)
            $is_correct = 1;
        }

        // Doğru cevap verildiyse puanı ekle
        if ($is_correct) {
            $points_earned = $question['points'];
        }

        // Cevabı kaydet
        $stmt = $db->prepare("
            INSERT INTO test_answers (
                test_result_id, question_id, given_answer,
                is_correct, points_earned
            ) VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $test_result_id,
            $question_id,
            $answer,
            $is_correct,
            $points_earned
        ]);

        $total_points_earned += $points_earned;
        $total_possible_points += $question['points'];
    }

    // Yüzdelik başarı puanını hesapla
    $score_percentage = ($total_points_earned / $total_possible_points) * 100;

    // Test sonucunu güncelle
    $stmt = $db->prepare("
        UPDATE test_results 
        SET status = 'completed',
            end_time = NOW(),
            score = ?
        WHERE id = ?
    ");
    $stmt->execute([$score_percentage, $test_result_id]);

    // Başvurunun test puanlarını güncelle
    $stmt = $db->prepare("
        SELECT COALESCE(AVG(score), 0) as average_score
        FROM test_results
        WHERE application_id = ? AND status = 'completed'
    ");
    $stmt->execute([$application_id]);
    $average_score = $stmt->fetch()['average_score'];

    $stmt = $db->prepare("
        UPDATE applications 
        SET test_points = ?,
            total_points = portfolio_points + education_points + 
                          certificate_points + experience_points + 
                          reference_points + ?
        WHERE id = ?
    ");
    $stmt->execute([$average_score, $average_score, $application_id]);

    // Transaction'ı tamamla
    $db->commit();

    // Sonuç sayfasına yönlendir
    header("Location: test_result.php?test_result_id=" . $test_result_id);
    exit;

} catch(PDOException $e) {
    // Hata durumunda transaction'ı geri al
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Bir hata oluştu: " . $e->getMessage());
} 