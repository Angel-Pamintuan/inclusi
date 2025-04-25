<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;
include 'database.php';


// Before redirecting to company profile


// Get the job ID from the URL
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;

$_SESSION['job_id'] = $job_id;

if ($job_id) {
    // Fetch job details
    $query = "SELECT j.*, e.company_name, u.fullname, e.company_logo 
              FROM tbl_job_post j
              JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
              JOIN tbl_user u ON e.user_id = u.user_id
              WHERE j.job_id = '$job_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $job = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Job not found!'); window.location.href='home.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('No job selected!'); window.location.href='home.php';</script>";
    exit;
}


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
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .backarrow {
            position: absolute;
            width: 24px;
            height: 24px;
            left: 21px;
            top: 51px;
        }

        .job-box {
            background-color: #1E4461;
            margin-top: 95px;
            height: 250px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            /* Stack items vertically */
            justify-content: center;
            /* Center content vertically */
            align-items: center;
            /* Center content horizontally */
            padding: 10px;
        }

        .job-box img {
            height: 190px;
            border-radius: 10px;
        }

        .job-box .job-text {
            width: 100%;
            display: flex;
            justify-content: space-between;
            /* Place h5 to the left and h6 to the right */
            padding: 0 10px;
            /* Add some spacing */
        }

        .job-box h5 {
            color: white;
            font-style: bold;
            margin: 10px 0;
        }

        .job-box h6 {
            color: white;
            margin: 10px 0;

        }

        .company {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Pushes vacancy to the right */
            margin-top: 10px;
            padding: 10px;

            border-radius: 5px;
        }

        .company img {
            height: 60px;
            width: 60px;
            border-radius: 5px;
        }

        .company-details {
            display: flex;
            flex-direction: column;
            /* Stack company name and fullname */
            margin-left: 10px;
        }

        .company h5 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .company h6 {
            margin: 0;
            font-size: 14px;
            color: gray;
        }

        .vacancy {
            margin-left: auto;
            /* Pushes it to the right */

        }

        .job-quali,
        .job-des {
            margin-left: 10px;
            max-width: 100%;
            /* Prevents overflow */
        }

        .quali,
        .des {
            font-weight: bold;
        }

        .quali-list,
        .des-list {
            margin-left: 0px;
            white-space: pre-wrap;
            /* Ensures text wraps */
            word-wrap: break-word;
            /* Break long words */
            overflow-wrap: break-word;
            width: 100%;
            /* Adjust width */
            max-width: 100%;
            /* Ensures it doesn't exceed container */
            font-family: inherit;
            overflow-x: auto;
            /* Allows scrolling only if necessary */
        }


        .button-container {
            display: flex;
            justify-content: center;
            /* Centers horizontally */
            align-items: center;
            /* Centers vertically (if needed) */
            margin: 20px 0px;
            /* Adjust spacing as needed */
        }


        .apply {
            background-color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Positions icon on the left and button on the right */
            padding: 15px;
            /* Adds spacing inside */
            border-radius: 5px;
            /* Optional: Adds rounded corners */
            width: 100%;
            /* Adjust width if necessary */
            margin-bottom: 20px;
        }

        .apply div {
            display: flex;
            align-items: center;
        }

        .apply button {
            border: none;
            background: none;
            padding: 12px 90px;
            cursor: pointer;
            font-weight: bold;
            background-color: #1E4461;
            color: white;
            border-radius: 10px;

        }

        .apply ion-icon {
            background-color: #F8F9FA;
            padding: 10px;
            font-size: 24px;
            color: #FCA34D;
            border-radius: 10px;
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
        <a href="home.php">
            <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
        </a>

        <div class="job-box">
            <img src="<?= !empty($job['job_image']) ? $job['job_image'] : 'image/no-profile.png'; ?>" alt="Job Image">
            <div class="job-text">
                <h5><?= htmlspecialchars($job['job_title']); ?></h5>

                <h6><?= date('F d, Y', strtotime($job['posted_at'])); ?></h6>
            </div>
        </div>

        <div class="company">
            <img src="<?= !empty($job['company_logo']) ? $job['company_logo'] : 'image/no-profile.png'; ?>"
                alt="Company Logo">
            <div class="company-details">
                <!--<h5><?= htmlspecialchars($job['company_name']); ?></h5>-->
                <h5><a href="company_profile.php?employer_id=<?= $job['employer_id']; ?>" class="apply-btn"
                        style="text-decoration: none; color:black;"><?= htmlspecialchars($job['company_name']); ?></a>
                </h5>
                <h6><?= htmlspecialchars($job['fullname']); ?></h6>
            </div>
            <div class="vacancy"><?= htmlspecialchars($job['vacancy']); ?> Vacancies</div>
        </div>

        <div class="job-quali">
            <div class="quali">Job Highlights:</div>
            <pre class="quali-list"><?= htmlspecialchars($job['job_qualification']); ?></pre>
        </div>

        <div class="job-des">
            <div class="des">Job Description:</div>
            <pre class="des-list"><?= htmlspecialchars($job['job_description']); ?></pre>
        </div>

        <div class="apply">
            <div><ion-icon name="bookmark-outline"></ion-icon></div>
            <button
                onclick="window.location.href='applynow.php?job_id=<?= $job['job_id']; ?>&employer_id=<?= $job['employer_id']; ?>'">
                Apply Now
            </button>
        </div>

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

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        function triggerFileInput() {
            document.getElementById('job_image').click();
        }

        document.getElementById('job_image').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>