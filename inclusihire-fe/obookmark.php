<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$applicant_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

if ($job_id === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid job ID."]);
    exit;
}

// Check if job is already bookmarked
$query = "SELECT * FROM tbl_bookmarks WHERE applicant_id = ? AND job_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $job_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // Job is already bookmarked, remove it
    $deleteQuery = "DELETE FROM tbl_bookmarks WHERE applicant_id = ? AND job_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $job_id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "removed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to remove bookmark."]);
    }
} else {
    // Job is not bookmarked, add it
    $insertQuery = "INSERT INTO tbl_bookmarks (applicant_id, job_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $job_id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "added"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add bookmark."]);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>