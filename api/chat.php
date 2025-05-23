<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// CORS ayarları
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, HTTP-Referer, X-Title');
header('Access-Control-Max-Age: 86400'); // 24 saat

// Hata raporlamayı etkinleştir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata log dosyası
error_log("Chat API request started");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// JSON verisini al
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Bağlam bilgisini al
$context = $data['context'] ?? 'general';
$test_id = $data['test_id'] ?? null;

// Sistem bilgilerini hazırla
$systemInfo = "Siz bir İK asistanısınız. ";

if ($context === 'test_creation') {
    $systemInfo .= "Şu anda test oluşturma sürecinde yardımcı oluyorsunuz. ";
    $systemInfo .= "Kullanıcıya test oluşturma, test yapısı, soru tipleri ve değerlendirme kriterleri konusunda yardımcı olabilirsiniz. ";
    $systemInfo .= "Örnek test soruları oluşturabilir ve test yapısı önerilerinde bulunabilirsiniz.";
} 
else if ($context === 'question_creation') {
    // Test bilgilerini getir
    $query = "SELECT * FROM tests WHERE id = :test_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':test_id', $test_id);
    $stmt->execute();
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($test) {
        $systemInfo .= "Şu anda '{$test['title']}' testi için soru oluşturma sürecinde yardımcı oluyorsunuz. ";
        $systemInfo .= "Test açıklaması: {$test['description']}. ";
        $systemInfo .= "Test süresi: {$test['time_limit']} dakika. ";
        $systemInfo .= "Geçme notu: {$test['passing_score']}. ";
        $systemInfo .= "Kullanıcıya bu test için uygun sorular oluşturma konusunda yardımcı olabilirsiniz. ";
        $systemInfo .= "Farklı soru tipleri (çoktan seçmeli, doğru/yanlış, metin) önerebilir ve örnek sorular oluşturabilirsiniz.";
    }
} 
else {
    // Genel bağlam için pozisyon ve istatistik bilgilerini al
    try {
        // Pozisyonları al
        $query = "SELECT title, description, requirements FROM positions WHERE status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // İstatistikleri al
        $query = "SELECT 
                  COUNT(*) as total_applications,
                  COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted,
                  AVG(CASE WHEN status = 'accepted' THEN TIMESTAMPDIFF(DAY, created_at, updated_at) END) as avg_processing_time
                  FROM applications";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $systemInfo .= "Aşağıdaki bilgilere göre yanıt verin:\n\n";
        $systemInfo .= "Aktif Pozisyonlar:\n";
        foreach ($positions as $position) {
            $systemInfo .= "- {$position['title']}\n";
            $systemInfo .= "  Gereksinimler: {$position['requirements']}\n";
        }
        $systemInfo .= "\nİstatistikler:\n";
        $systemInfo .= "- Toplam Başvuru: {$stats['total_applications']}\n";
        $systemInfo .= "- Kabul Edilen: {$stats['accepted']}\n";
        $systemInfo .= "- Ortalama İşlem Süresi: " . round($stats['avg_processing_time']) . " gün\n";

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// OpenRouter API'ye istek
$apiKey = "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";
$url = "https://openrouter.ai/api/v1/chat/completions";

$headers = [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json',
    'HTTP-Referer: http://localhost/project_a/admin/positions.php',
    'X-Title: İK Asistanı'
];

$postData = [
    'model' => 'meta-llama/llama-4-scout:free',
    'messages' => [
        [
            'role' => 'system',
            'content' => $systemInfo
        ],
        [
            'role' => 'user',
            'content' => $data['message']
        ]
    ],
    'temperature' => 0.7,
    'max_tokens' => 500
];

error_log("Sending request to OpenRouter API with data: " . json_encode($postData));

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

error_log("API Response Code: " . $httpCode);
error_log("API Response: " . $response);
error_log("CURL Info: " . json_encode($info));
if ($error) error_log("CURL Error: " . $error);

if ($httpCode === 0) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API connection failed',
        'details' => [
            'curl_error' => $error,
            'curl_info' => $info
        ]
    ]);
    exit();
}

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API error',
        'details' => [
            'http_code' => $httpCode,
            'response' => $response,
            'curl_error' => $error,
            'curl_info' => $info
        ]
    ]);
    exit();
}

$responseData = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    error_log("Raw response: " . $response);
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid JSON response from API',
        'raw_response' => $response
    ]);
    exit();
}

if (!isset($responseData['choices'][0]['message']['content'])) {
    error_log("Unexpected API response structure: " . json_encode($responseData));
    http_response_code(500);
    echo json_encode([
        'error' => 'Unexpected API response structure',
        'response_data' => $responseData
    ]);
    exit();
}

echo json_encode([
    'response' => $responseData['choices'][0]['message']['content']
]); 