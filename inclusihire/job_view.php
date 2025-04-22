<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: applicantlogin.php");
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$accessibility = $_SESSION['accessibility'] ?? null;

// Get employer_id from URL
if (!isset($_GET['employer_id'])) {
    echo "No employer data found.";
    exit();
}
$employer_id = mysqli_real_escape_string($conn, $_GET['employer_id']);

// Fetch employer profile
$query = "
        SELECT ep.*, ct.company_type AS company_type_name 
        FROM tbl_employer_profile ep
        LEFT JOIN tbl_company_type ct ON ep.company_type = ct.company_type_id
        WHERE ep.employer_id = '$employer_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $company_name = $row['company_name'];
    $company_address = $row['company_address'];
    $since = $row['since'];
    $company_logo = !empty($row['company_logo']) ? $row['company_logo'] : 'image/no-profile.png';
} else {
    echo "No employer data found for this employer.";
    exit();
}

// Get job info if job_id is provided
if (isset($_GET['job_id'])) {
    $job_id = mysqli_real_escape_string($conn, $_GET['job_id']);
    $job_query = "SELECT * FROM tbl_job_post WHERE job_id = '$job_id'";
    $job_result = mysqli_query($conn, $job_query);
    if (mysqli_num_rows($job_result) > 0) {
        $job = mysqli_fetch_assoc($job_result);
        $job_title = $job['job_title'];
    } else {
        echo "Job not found!";
        exit();
    }
} else {
    echo "Job ID not specified.";
    exit();
}

$application_id = $_GET['application_id'] ?? null;


if ($application_id) {
    $app_query = "SELECT * FROM tbl_applications WHERE application_id = '$application_id'";
    $app_result = mysqli_query($conn, $app_query);

    if ($app_result && mysqli_num_rows($app_result) > 0) {
        $app = mysqli_fetch_assoc($app_result);
        $cv_path = $app['cv_path']; // Now this will be available
    } else {
        echo "Application not found!";
        exit();
    }
} else {
    echo "Application ID not specified.";
    exit();
}



// Handle form submission

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Post</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F8F9FA;
        }

        .backarrow {
            position: absolute;
            width: 24px;
            height: 24px;
            left: 21px;
            top: 51px;
        }

        .logo {
            /* Logo google */

            position: absolute;
            width: 100px;
            height: 100px;
            left: 154px;
            top: 61px;

            z-index: 1;


        }

        .logo img {
            height: 64px;
            width: 64px;
            border-radius: 50%;
        }

        .info {
            /* Rectangle 235 */

            position: absolute;
            width: 375px;
            height: 114px;
            left: 9px;
            top: 104px;

            background: #F2F2F2;

            text-align: center;
            justify-content: center;
            flex-wrap: wrap;
            font-family: "DM Sans";
            color: #0D0140;
            justify-content: center;
        }

        .info .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }

        .info span {
            padding: 10px;
            font-size: 18px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 230px;
            gap: 10px;
            /* Space between buttons */
        }

        .upload {
            margin-top: 220px;
            padding: 10px;
        }

        .sub-title {
            font-weight: bold;
            font-size: 18px;
            padding: 10px 0;
        }

        #cv {
            display: flex;
            align-items: center;
            /* Centers vertically */
            justify-content: center;
            /* Centers horizontally */
            border: 1px dotted black;
            margin-top: 10px;
            height: 100px;
            border-radius: 10px;
            text-align: center;
            color: #150B3D;
            font-size: 18px;
            /* Ensures text alignment */
        }

        #cv ion-icon {
            margin-right: 10px;
            font-size: 18px;
        }

        .info-box {
            padding: 10px;
            text-align: center;
            justify-content: center;
        }

        .info-box img {
            width: 150px;
        }

        .info-text .hg {
            font-weight: bold;
        }

        .apply {

            justify-content: center;
            /* Centers horizontally */
            align-items: center;
            /* Centers vertically (if needed) */
        }

        .app {
            width: 370px;
            border: none;
            background-color: #1E4461;
            color: white;
            font-size: 16px;
            text-align: center;
            padding: 15px 110px;
            font-weight: bold;
            justify-content: center;
            border-radius: 10px;

        }

        .app-1 {
            width: 370px;
            border: none;
            background-color: #90C1E5;
            color: black;
            font-size: 16px;
            text-align: center;
            padding: 15px 110px;
            font-weight: bold;
            justify-content: center;
            border-radius: 10px;
            margin: 10px 0;
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

        footer div {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
        }

        footer button {
            border: none;
            background: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="job_post.php">
            <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
        </a>

        <div class="header">
            <div class="logo">
                <img src="<?= htmlspecialchars($company_logo); ?>" alt="Company Logo">
            </div>
            <div class="info">
                <div class="company-name"><?= $job_title; ?></div>
                <div>
                    <span><?= $company_name; ?></span>
                    <span>&#8226;</span>
                    <span><?= $company_address; ?></span>
                    <span>&#8226;</span>
                    <span><?= $since; ?></span>
                </div>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="upload">

                <label for="cv-upload" id="cv">
                    <?php if (isset($cv_path) && !empty($cv_path)): ?>
                        <div class="cv-preview" style="margin-top: 20px;">
                            <div><?= basename($cv_path); ?></div>
                            <a href="<?= $cv_path; ?>" target="_blank" style="color: blue; text-decoration: underline;">View
                                CV</a>
                        </div>
                    <?php endif; ?>

                </label>
                <input type="file" name="cv" id="cv-upload" style="display: none;" required>


            </div>

            <div class="info-box">
                <div class="img">
                    <img src="image/image.png">
                </div>
                <div class="info-text">
                    <h6 class="hg">Successful</h6>
                    <h6>Congratulations, your application has been sent</h6>
                </div>
            </div>

            <div class="apply">
                <button type="button" class="app-1" onclick="window.location.href='morejobs.php'">FIND SIMILAR
                    JOB</button>
                <button type="button" class="app" onclick="window.location.href='home.php'">BACK TO HOME</button>
            </div>
        </form>
    </div>

    <footer>
        <button id="getStartedNav3" data-action="navigate" data-destination="home.php">
            <ion-icon name="home-outline" style="color: #1E4461; font-size: 28px"></ion-icon>
        </button>
        <button id="getStartedNav4" data-action="navigate" data-destination="messages.php">
            <ion-icon name="mail-outline" style="font-size: 28px"></ion-icon>
        </button>
        <button id="getStartedNav5" data-action="navigate" data-destination="bookmarks.php">
            <ion-icon name="bookmark-outline" style="font-size: 28px"></ion-icon>
        </button>
        <button id="getStartedNav6" data-action="navigate" data-destination="morejobs.php">
            <ion-icon name="grid-outline" style="font-size: 28px"></ion-icon>
        </button>
    </footer>

    <script>
        const fileInput = document.getElementById('cv-upload');
        const previewBox = document.getElementById('cv-preview');
        const fileNameDisplay = document.getElementById('cv-file-name');
        const removeBtn = document.getElementById('remove-cv');

        fileInput.addEventListener('change', function () {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
                previewBox.style.display = 'block';
            }
        });

        removeBtn.addEventListener('click', function () {
            fileInput.value = ''; // Clear the file input
            previewBox.style.display = 'none';
        });
    </script>


    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>

</html>