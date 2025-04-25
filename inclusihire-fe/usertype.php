<?php
session_start();

if (isset($_POST['usertype'])) {
    $usertype = $_POST['usertype'];

    // Store user type in session
    if ($usertype == "1") {
        $_SESSION['usertype'] = 'Applicant';
        header("Location: applicantregister.php");
        exit;
    } elseif ($usertype == "2") {
        $_SESSION['usertype'] = 'Employer';
        header("Location: employerregister.php");
        exit;
    } else {
        $_SESSION['status'] = "Invalid user type.";
        header("Location: whoyouare.php");
        exit;
    }
} else {
    $_SESSION['status'] = "Please select who you are.";
    header("Location: whoyouare.php");
    exit;
}
