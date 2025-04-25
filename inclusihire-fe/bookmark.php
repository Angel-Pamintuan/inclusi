<?php
session_start();
require 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$applicant_id = $_SESSION['user_id']; // ✅ Ensure consistency
$job_id = $_POST['job_id'] ?? null;

if (!$job_id) {
    echo json_encode(["success" => false, "message" => "Invalid job ID"]);
    exit;
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Check if the bookmark already exists
    $query = "SELECT * FROM tbl_bookmarks WHERE applicant_id = ? AND job_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($exists) {
        // Remove bookmark
        $query = "DELETE FROM tbl_bookmarks WHERE applicant_id = ? AND job_id = ?";
    } else {
        // Add bookmark
        $query = "INSERT INTO tbl_bookmarks (applicant_id, job_id) VALUES (?, ?)";
    }

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $job_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$success) {
        throw new Exception("Query execution failed: " . mysqli_error($conn));
    }

    echo json_encode(["success" => true, "bookmarked" => !$exists]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>