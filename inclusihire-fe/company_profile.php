<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: applicantlogin.php");
    exit();
}




$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']); // Prevent SQL injection

$job_id = $_SESSION['job_id'];

// Check if employer_id is passed in the URL
if (isset($_GET['employer_id'])) {
    $employer_id = mysqli_real_escape_string($conn, $_GET['employer_id']);
} else {
    echo "No employer data found.";
    exit();
}

// Fetch employer profile using employer_id (not user_id)
$query = "
    SELECT ep.*, 
           ct.company_type AS company_type_name 
    FROM tbl_employer_profile ep
    LEFT JOIN tbl_company_type ct ON ep.company_type = ct.company_type_id
    WHERE ep.employer_id = '$employer_id'";  // Using employer_id here

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Assign database values to variables
    $company_name = $row['company_name'];
    $company_address = $row['company_address'];
    $since = $row['since'];
    $about = $row['about'] ?? "No description available.";
    $website = !empty($row['website']) ? $row['website'] : "#";
    $company_logo = !empty($row['company_logo']) ? $row['company_logo'] : 'image/no-profile.png'; // Default profile image
    $industry = $row['industry'] ?? "Not Specified";
    $specialization = $row['specialization'] ?? "Not Specified";
    $company_type = $row['company_type_name'] ?? "Unknown"; // Get the actual type name
    $company_size = $row['company_size'] ?? "Not Specified";
    $head_office = $row['head_office'] ?? "Not Available";
    $employer_id = $row['employer_id'] ?? null; // Fetch employer_id for gallery images
} else {
    echo "No employer data found for this employer.";
    exit();
}

// Fetch images from tbl_company_gallery using employer_id
$gallery_images = [];
if ($employer_id) {
    $query = "SELECT image_path FROM tbl_company_gallery WHERE employer_id = '$employer_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $gallery_images[] = $row['image_path'];
        }
    }
}
?>




<!-- Display the image properly -->




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

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
        }

        .info .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
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

        .buttons button {
            border: none;
            padding: 10px 20px;
            background-color: #FFE5CB;
            color: #FCA34D;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 170px;
            /* Ensures all buttons have the same width */
            height: 50px;
            /* Ensures all buttons have the same height */
            cursor: pointer;
        }

        .buttons button ion-icon {
            margin-right: 5px;
            /* Adds spacing between icon and text */
            font-size: 20px;
        }

        .nav {

            margin-top: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            gap: 10px;
            display: flex;
            justify-content: space-evenly;
            /* Evenly space buttons */
        }

        .nav button {
            border: none;
            align-items: center;
            flex: 1;
            /* Make buttons take equal space */
            padding: 10px;
            /* Add padding for better appearance */
            text-align: center;
            /* Center text inside button */
            background: none;
            /* Removes background color */
            box-shadow: none;
            /* Ensures no shadow effect */
            outline: none;
            font-weight: bold;
            font-size: 16px;
        }

        .nav .active {
            background-color: #FCA34D;
            border-radius: 10px;
        }

        .information {
            padding: 10px;
            margin-bottom: 90px;
        }

        .information .sub-title {
            font-size: 18px;
            font-weight: bold;
        }

        .information .division {
            padding: 5px 0;
        }

        .information .div-text {
            color: 524B6B;
        }

        /*.apply {
            background-color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;
            
            padding: 15px;
           
            border-radius: 5px;
           
            width: 100%;
            
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
        }*/

        .image {
            gap: 20px;
            display: flex;
            justify-content: space-evenly;
        }

        .image img {
            height: 150px;
            width: 160px;
            border-radius: 10px;
            gap: 10px;
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
            background: none;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="job_post.php?job_id=<?= $job_id ?>&employer_id=<?= $employer_id ?>">
            <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
        </a>

        <div class="header">
            <div class="logo">
                <img src="<?= htmlspecialchars($company_logo); ?>" alt="Company Logo">
            </div>
            <div class="info">
                <div class="company-name">UI/UX Designer</div>
                <div>
                    <span><?= $company_name; ?></span>
                    <span>&#8226;</span>
                    <span><?= $company_address; ?></span>
                    <span>&#8226;</span>
                    <span><?= $since; ?></span>
                </div>
            </div>
        </div>

        <div class="buttons">
            <button><ion-icon name="add-outline"></ion-icon> Follow</button>
            <a href="<?= htmlspecialchars($website); ?>" target="_blank"
                style="text-decoration: none;"><button><ion-icon name="enter-outline"></ion-icon> Visit
                    website</button></a>
        </div>

        <div class="nav">
            <button class="active">About Us</button>
            <button>Post</button>
            <button>Jobs</button>
        </div>

        <div class="information">
            <div class="division">
                <div class="sub-title">About Company</div>
                <div><?= $about; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Website</div>
                <div class="div-text"><?= $website; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Industry</div>
                <div class="div-text"><?= $industry; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Employee Size</div>
                <div class="div-text"><?= $company_size; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Head Office</div>
                <div class="div-text"><?= $head_office; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Type</div>
                <div class="div-text"><?= $company_type; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Since</div>
                <div class="div-text"><?= $since; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Speciaization</div>
                <div class="div-text"><?= $specialization; ?></div>
            </div>

            <div class="division">
                <div class="sub-title">Company Gallery</div>
                <div class="image">
                    <?php if (!empty($gallery_images)): ?>
                        <?php foreach ($gallery_images as $gallery_path): ?>
                            <img src="<?= htmlspecialchars($gallery_path); ?>" alt="Company Image" width="150">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No images available.</p>
                    <?php endif; ?>
                </div>

            </div>

        </div>

        <!--<div class="apply">
            <div><ion-icon name="bookmark-outline"></ion-icon></div>
            <button onclick="window.location.href='applynow.php'" ;>Apply Now</button>
        </div>-->





    </div>


    <footer>
        <button id="getStartedNav3" data-action="navigate" data-destination="home.php">
            <ion-icon name="home-outline" style="font-size: 28px"></ion-icon>
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

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></scrip >
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <?php
    include 'access.php';
    ?>

</body>

</html>