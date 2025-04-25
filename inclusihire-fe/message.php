<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$application_query = "
    SELECT 
        a.application_id,
        a.application_text,
        a.applied_at, 
        jp.job_title,
        ep.company_name,
        ep.company_logo,
        p.profile_pic,
        s.apply_status
    FROM tbl_applications a
    JOIN tbl_job_post jp ON a.job_id = jp.job_id
    JOIN tbl_employer_profile ep ON jp.employer_id = ep.employer_id
    LEFT JOIN tbl_applicant_profile p ON a.user_id = p.applicant_id
    JOIN tbl_apply_status s ON a.apply_status_id = s.apply_status_id 
    WHERE a.user_id = ?
    ORDER BY a.applied_at DESC
";

$stmt = $conn->prepare($application_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result();




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

            background: #FAFAFD;
            border-radius: 26px 26px 0px 0px;

        }

        .featured-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
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
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            max-width: 400px;
            margin: 0 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .messages img {
            height: 60px;
            width: 60px;
            border-radius: 50%;
            /*object-fit: cover;*/
            margin-right: 15px;
        }

        .message-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-info .username {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }

        .message-info a {
            text-decoration: none;
            color: #333;
        }

        .message-info .job-status {
            font-size: 12px;

            color: #1E4461;
            padding: 2px 8px;
            border-radius: 12px;
            white-space: nowrap;
        }

        .message-info .date {
            font-size: 10px;
            color: #888;
        }

        .message-info .job-title {
            font-size: 14px;
            color: #555;
        }


        .status-pending {
            background-color: #e0f4ff;
            color: #1E4461;
        }

        .status-interview {
            background-color: #fff4d1;
            color: #806000;
        }

        .status-approved {
            background-color: #d1f7d6;
            color: #1f7a1f;
        }

        .status-rejected {
            background-color: #ffd1d1;
            color: #a10000;
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
                <?php
                if ($applications->num_rows == 0) {
                    echo "<p style='color: red;'>No applications found for this user.</p>";
                }
                ?>

                <?php while ($row = $applications->fetch_assoc()): ?>

                    <?php
                    $status = strtolower($row['apply_status']);
                    switch ($status) {
                        case 'pending':
                            $statusClass = 'status-pending';
                            break;
                        case 'for interview':
                            $statusClass = 'status-interview';
                            break;
                        case 'approved':
                            $statusClass = 'status-approved';
                            break;
                        case 'rejected':
                            $statusClass = 'status-rejected';
                            break;
                        default:
                            $statusClass = 'status-default';
                    }
                    ?>
                    <div class="messages">
                        <img src="<?= !empty($row['company_logo']) ? $row['company_logo'] : 'image/no-profile.png' ?>"
                            alt="Company Logo">

                        <div class="message-info">
                            <div class="message-header">
                                <div class="username">
                                    <!-- Set the href to link to messageprogress.php with application_id -->
                                    <a href="messageprogress.php?application_id=<?= $row['application_id'] ?>">
                                        <?= htmlspecialchars($row['company_name']) ?>
                                    </a>

                                </div>

                                <div class="job-status <?= $statusClass ?>">
                                    <?= htmlspecialchars($row['apply_status']) ?>
                                </div>
                            </div>
                            <div class="date"><?= date("F j, Y, g:i A", strtotime($row['applied_at'])) ?></div>
                            <div class="job-title"><?= htmlspecialchars($row['job_title']) ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>



                <div class="messages">
                    <img src="image/no-profile.png">
                    <div class="message-info">
                        <div class="username">Username</div>
                        <div class="date">Date</div>
                    </div>
                </div>

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