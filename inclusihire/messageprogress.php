<?php
session_start();
$accessibility = $_SESSION['accessibility'] ?? null;

include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if application_id is passed in the URL
$application_id = $_GET['application_id'] ?? null;

$row = null;

if ($application_id) {
    $sql = "SELECT 
                a.application_id,
                j.job_title,
                j.job_description,
                a.cv_path,
                a.apply_status_id,
                c.company_name,
                c.company_logo,
                s.apply_status
            FROM tbl_applications a
            LEFT JOIN tbl_job_post j ON a.job_id = j.job_id
            Left JOIN tbl_apply_status s ON a.apply_status_id = s.apply_status_id 
            LEFT JOIN tbl_employer_profile c ON j.employer_id = c.employer_id
            WHERE a.application_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .header-container {
            /* Rectangle 10 */

            position: absolute;
            width: 394px;
            height: 303px;
            left: -1px;
            top: 0px;

            background: #1E4461;


        }

        .title {
            /* Headline */

            position: absolute;
            width: 254px;
            height: 21px;
            left: 21px;
            top: 61px;

            /* Medium/14 px */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 500;
            font-size: 14px;
            line-height: 150%;
            /* identical to box height, or 21px */
            display: flex;
            align-items: center;
            letter-spacing: -0.01em;

            /* Grey / 60 */
            color: #95969D;


        }

        .heading {
            /* Headline */

            position: absolute;
            width: 254px;
            height: 26px;
            left: 21px;
            top: 84px;

            /* Bold/22 px */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            font-size: 22px;
            line-height: 120%;
            /* or 26px */
            letter-spacing: -0.015em;

            color: #F0F0FC;


        }

        .body-container {
            /* Rectangle 11 */

            position: absolute;
            width: 395px;
            height: 183px;
            left: -2px;
            top: 152px;

            background: white;
            border-radius: 26px 26px 0px 0px;

        }

        .featured-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
            overflow-y: auto;
            max-height: 83vh;
            /* Makes it scrollable */
            padding: 10px;
            padding-bottom: 70px;
            width: 100%;
            margin-top: 3px;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;

            /* IE/Edge */

        }

        .featured {
            width: 100%;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .messages {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 10px auto;
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .message-header img {
            border-radius: 50%;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .header-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .username {
            font-weight: bold;
            font-size: 16px;
            color: #1e1e1e;
        }

        .date {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }

        hr {
            margin: 12px 0;
            border: none;
            border-top: 1px solid #ddd;
        }

        .message-body {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .job-title {
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }

        .job-description,
        .cv {
            font-size: 14px;
            color: #555;
        }

        .job-status {
            font-size: 12px;
            background-color: #e0f4ff;
            color: #1E4461;
            padding: 3px 10px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
        }












        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 72px;
            background: #FFFFFF;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            /* Soft shadow on top */
        }

        footer a {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
            color: black;
        }

        footer div {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
        }

        footer button {
            background: none;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container">
            <div class="title">Welcome to InclusiHire!</div>
            <div class="heading">Application Progress</div>
        </div>
        <div class="body-container">
            <div class="featured-container">


                <?php if ($row): ?>
                    <div class="messages">
                        <div class="message-header">
                            <img src="<?= !empty($row['company_logo']) ? $row['company_logo'] : 'image/no-profile.png' ?>"
                                alt="Company Logo" width="50" height="50">
                            <div class="header-info">
                                <div class="username"><?= htmlspecialchars($row['company_name']) ?></div>
                                <div class="date"><?= date("F j, Y") ?></div>
                            </div>
                        </div>

                        <hr>

                        <div class="message-body">
                            <div class="job-title"><?= htmlspecialchars($row['job_title']) ?></div>
                            <div class="job-description"><?= htmlspecialchars($row['job_description']) ?></div>
                            <div class="cv">
                                <a href="<?= htmlspecialchars($row['cv_path']) ?>" target="_blank" download>
                                    CV/Resume
                                </a>
                            </div>

                            <div class="job-status">Status: <?= htmlspecialchars($row['apply_status']) ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No application found.</p>
                <?php endif; ?>


            </div>
        </div>

        <footer>
            <button id="getStartedNav3" data-action="navigate" data-destination="home.php">
                <ion-icon name="home-outline" style=" font-size: 28px"></ion-icon>
            </button>
            <button id="getStartedNav4" data-action="navigate" data-destination="messages.php">
                <ion-icon name="mail-outline" style="color: #1E4461; font-size: 28px"></ion-icon>
            </button>
            <button id="getStartedNav5" data-action="navigate" data-destination="bookmarks.php">
                <ion-icon name="bookmark-outline" style="font-size: 28px"></ion-icon>
            </button>
            <button id="getStartedNav6" data-action="navigate" data-destination="morejobs.php">
                <ion-icon name="grid-outline" style="font-size: 28px"></ion-icon>
            </button>
        </footer>
        <?php
        include 'access.php';
        ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

        <script src="accessibility.js"></script>
        <script>

        </script>


</body>

</html>