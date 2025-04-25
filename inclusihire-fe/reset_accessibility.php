<?php
session_start();
unset($_SESSION['accessibility']);  // Remove accessibility setting
header("Location: index.php");  // Redirect back to homepage or any page
exit();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="reset_accessibility.php" method="POST">
        <button type="submit">Reset Accessibility Settings</button>
    </form>

</body>

</html>