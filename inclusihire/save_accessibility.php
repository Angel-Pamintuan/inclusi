<?php
session_start();

if (isset($_POST['accessibility'])) {
    $_SESSION['accessibility'] = $_POST['accessibility'];
}

header("Location: whoyouare.php");
exit();
?>