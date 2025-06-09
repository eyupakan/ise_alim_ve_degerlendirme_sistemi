<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_POST['application_ids']) || !is_array($_POST['application_ids']) || count($_POST['application_ids']) === 0) {
    header('Location: applications.php');
    exit;
}

$ids = array_map('intval', $_POST['application_ids']);
$id_placeholders = implode(',', array_fill(0, count($ids), '?'));

// Toplu silme
if (isset($_POST['bulk_delete'])) {
    $stmt = $db->prepare("DELETE FROM applications WHERE id IN ($id_placeholders)");
    $stmt->execute($ids);
    header('Location: applications.php?success=bulk_deleted');
    exit;
}

// Toplu durum değiştirme
if (!empty($_POST['bulk_status'])) {
    $new_status = $_POST['bulk_status'];
    $stmt = $db->prepare("UPDATE applications SET status = ? WHERE id IN ($id_placeholders)");
    $params = array_merge([$new_status], $ids);
    $stmt->execute($params);
    header('Location: applications.php?success=bulk_status');
    exit;
}

header('Location: applications.php');
exit; 