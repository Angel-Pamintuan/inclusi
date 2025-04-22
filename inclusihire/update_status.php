<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $new_status_id = $_POST['apply_status_id'];

    $stmt = $conn->prepare("UPDATE tbl_applications SET apply_status_id = ? WHERE application_id = ?");
    $stmt->bind_param("ii", $new_status_id, $application_id);
    $stmt->execute();

    // Redirect back with success message (optional)
    header("Location: applicationprogress.php?application_id=$application_id");
    exit();
}
?>