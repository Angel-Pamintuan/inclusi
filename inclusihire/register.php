<?php
session_start();
include 'database.php';

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $gmail = $_POST['gmail'];
    $contactNumber = $_POST['contactNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $_SESSION['status'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Hash the password (use bcrypt)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email_query = "SELECT gmail FROM tbl_user WHERE gmail = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['status'] = "Email already exists!";
        header("Location: register.php");
        exit();
    }
    $stmt->close();

    // Insert user into tbl_user
    $insert_query = "INSERT INTO tbl_user (fullname, gmail, contactNumber, password) 
                     VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssss", $fullname, $gmail, $contactNumber, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Registration successful!";
        header("Location: home.php");
        exit();
    } else {
        $_SESSION['status'] = "Registration failed: " . $stmt->error;
        header("Location: register.php");
        exit();
    }

}
?>