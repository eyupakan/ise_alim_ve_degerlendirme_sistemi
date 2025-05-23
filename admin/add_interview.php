<?php
require_once 'auth_check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reports.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // Form verilerini al
    $application_id = $_POST['application_id'];
    $interview_date = $_POST['interview_date'];
    $interview_type = $_POST['interview_type'];
    $meeting_link = $_POST['meeting_link'] ?? null;
    $location = $_POST['location'] ?? null;
    $notes = $_POST['notes'] ?? null;

    // Mülakat ekle
    $query = "INSERT INTO interviews (application_id, interview_date, interview_type, meeting_link, location, notes) 
              VALUES (:application_id, :interview_date, :interview_type, :meeting_link, :location, :notes)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':application_id', $application_id);
    $stmt->bindParam(':interview_date', $interview_date);
    $stmt->bindParam(':interview_type', $interview_type);
    $stmt->bindParam(':meeting_link', $meeting_link);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':notes', $notes);
    
    if ($stmt->execute()) {
        // Başvuru durumunu güncelle
        $query = "UPDATE applications SET status = 'in_review' WHERE id = :application_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':application_id', $application_id);
        $stmt->execute();
        
        header('Location: reports.php?success=1');
    } else {
        header('Location: reports.php?error=1');
    }

} catch (PDOException $e) {
    header('Location: reports.php?error=' . urlencode($e->getMessage()));
}
exit; 