<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../vendor/setasign/fpdf/fpdf.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Geçersiz başvuru ID.');
}

$application_id = (int)$_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Başvuru ve aday bilgilerini çek
$query = "SELECT a.*, c.*, c.id as candidate_id, p.title as position_title
          FROM applications a
          JOIN candidates c ON a.candidate_id = c.id
          JOIN positions p ON a.position_id = p.id
          WHERE a.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $application_id);
$stmt->execute();
$application = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$application) {
    die('Başvuru bulunamadı.');
}

// Test sonuçlarını çek
$query = "SELECT t.title, tr.score, tr.status, tr.end_time
          FROM position_tests pt
          JOIN tests t ON pt.test_id = t.id
          LEFT JOIN test_results tr ON t.id = tr.test_id AND tr.application_id = :application_id
          WHERE pt.position_id = :position_id
          ORDER BY t.title";
$stmt = $db->prepare($query);
$stmt->bindParam(':application_id', $application_id);
$stmt->bindParam(':position_id', $application['position_id']);
$stmt->execute();
$test_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sertifikaları çek (recruitment_system.sql: certificates)
$query = "SELECT * FROM certificates WHERE candidate_id = :candidate_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':candidate_id', $application['candidate_id']);
$stmt->execute();
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// İş deneyimlerini çek (recruitment_system.sql: experiences)
$query = "SELECT * FROM experiences WHERE candidate_id = :candidate_id ORDER BY start_date DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':candidate_id', $application['candidate_id']);
$stmt->execute();
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Aday Basvuru Detayi', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Kisisel Bilgiler', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, 'Ad Soyad:', 0, 0); $pdf->Cell(0, 8, ($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? ''), 0, 1);
$pdf->Cell(60, 8, 'Email:', 0, 0); $pdf->Cell(0, 8, $application['email'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Telefon:', 0, 0); $pdf->Cell(0, 8, $application['phone'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Sehir:', 0, 0); $pdf->Cell(0, 8, $application['city'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'LinkedIn:', 0, 0); $pdf->Cell(0, 8, $application['linkedin_url'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Github:', 0, 0); $pdf->Cell(0, 8, $application['github_url'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Portfoy:', 0, 0); $pdf->Cell(0, 8, $application['portfolio_url'] ?? '', 0, 1);
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Basvuru Bilgileri', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, 'Pozisyon:', 0, 0); $pdf->Cell(0, 8, $application['position_title'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Basvuru Tarihi:', 0, 0); $pdf->Cell(0, 8, $application['created_at'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Durum:', 0, 0); $pdf->Cell(0, 8, $application['status'] ?? '', 0, 1);
$pdf->Cell(60, 8, 'Toplam Puan:', 0, 0); $pdf->Cell(0, 8, $application['total_points'] ?? '', 0, 1);
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Test Sonuclari', 0, 1);
$pdf->SetFont('Arial', '', 11);
foreach ($test_results as $test) {
    $pdf->Cell(60, 8, $test['title'] ?? '', 0, 0);
    $pdf->Cell(30, 8, isset($test['score']) && $test['score'] !== null ? round($test['score'], 1) . '%' : '-', 0, 0);
    $pdf->Cell(30, 8, ($test['status'] ?? '') === 'completed' ? 'Tamamlandi' : (($test['status'] ?? '') === 'skipped' ? 'Atlandi' : 'Bekliyor'), 0, 0);
    $pdf->Cell(0, 8, $test['end_time'] ?? '-', 0, 1);
}
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Sertifikalar', 0, 1);
$pdf->SetFont('Arial', '', 11);
foreach ($certificates as $cert) {
    $pdf->Cell(60, 8, $cert['name'] ?? '', 0, 0);
    $pdf->Cell(60, 8, $cert['issuing_organization'] ?? '', 0, 0);
    $pdf->Cell(0, 8, $cert['issue_date'] ?? '', 0, 1);
}
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Is Deneyimi', 0, 1);
$pdf->SetFont('Arial', '', 11);
foreach ($experiences as $exp) {
    $pdf->Cell(40, 8, $exp['position'] ?? '', 0, 0);
    $pdf->Cell(40, 8, $exp['company'] ?? '', 0, 0);
    $pdf->Cell(30, 8, $exp['start_date'] ?? '', 0, 0);
    $pdf->Cell(30, 8, $exp['end_date'] ?? '', 0, 0);
    $pdf->Cell(0, 8, $exp['description'] ?? '', 0, 1);
}

$pdf->Output('D', 'basvuru_detay.pdf');

function tr($text) {
    return iconv('UTF-8', 'windows-1254//TRANSLIT', $text);
} 