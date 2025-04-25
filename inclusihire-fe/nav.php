<?php

include 'database.php';

// Check if user is logged in
if (!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // Assuming user_id is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query was successful and returned any result
    if ($result && mysqli_num_rows($result) > 0) {
        $row = $result->fetch_assoc(); // Corrected to use object-oriented syntax
        // Now you can access user details, e.g., $row['username']
    } else {
        echo "User not found.";
        exit();
    }
} else {
    // Redirect to login page or handle accordingly
    header("Location: login.php");
    exit();
}


?>