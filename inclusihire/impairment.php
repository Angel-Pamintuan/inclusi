<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user has completed registration
if (!isset($_SESSION['user_id'])) {
    $_SESSION['status'] = "Please complete registration first.";
    header("Location: applicantregister.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle impairment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $impairment_id = $_POST['impairment'];

    // Update tbl_applicant (correct table)
    $update_query = "UPDATE tbl_user SET impairment_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $impairment_id, $user_id);

    if ($stmt->execute()) {
        // Move to skills selection page
        header("Location: jobskills.php");
        exit();
    } else {
        $_SESSION['status'] = "Failed to save impairment.";
    }
}

// Fetch impairment options
$impairments = [];
$sql = "SELECT impairment_id, impairment FROM tbl_impairment";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $impairments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Scrollable content wrapper */
        .content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;

        }

        .title {
            width: 100%;
            max-width: 400px;
            font-weight: 600;
            font-size: 24px;
            line-height: 140%;
            letter-spacing: -0.015em;
            color: #0D0D26;
            margin: 60px auto 20px;
            padding-left: 20px;
        }

        .form-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 327px;
            height: 62px;
            margin: 10px auto;
            background: #F2F2F2;
            box-shadow: 0px 2px 10px -2px rgba(13, 21, 38, 0.02);
            border-radius: 12px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .form-check-label {
            font-size: 18px;
            color: #333;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        /* Fixed button at bottom */
        .button-container {
            position: fixed;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 400px;
            display: flex;
            justify-content: center;
        }

        button {
            width: 327px;
            height: 56px;
            background: #1E4461;
            border-radius: 5px;
            color: white;
            border: none;
            padding: 16px 48px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="title">Do you have any of the following conditions that may affect your job search?</div>

        <!-- Form Start -->
        <form action="" method="POST">
            <?php if (!empty($impairments)): ?>
                <?php foreach ($impairments as $row): ?>
                    <div class="form-check">
                        <label class="form-check-label" for="radio<?= $row['impairment_id'] ?>">
                            <?= htmlspecialchars($row['impairment']) ?>
                        </label>
                        <input class="form-check-input" type="radio" name="impairment" value="<?= $row['impairment_id'] ?>"
                            id="radio<?= $row['impairment_id'] ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No impairments found.</p>
            <?php endif; ?>

            <!-- Fixed button INSIDE the form -->
            <div class="button-container">
                <button type="submit" onclick="stopReading();">Save</button>
            </div>
        </form>
        <!-- Form End -->

        <?php
        $accessibility = 'screen_reader'; // Example value, you can change this based on your logic.
        include 'access.php';
        ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="accessibility.js"></script>
        <script>
            function stopReading() {
                speechSynthesis.cancel();
            }
        </script>
</body>

</html>