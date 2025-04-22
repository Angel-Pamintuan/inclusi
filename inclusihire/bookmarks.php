<?php
session_start(); // ✅ Ensure session variables are available

include 'database.php';

$accessibility = $_SESSION['accessibility'] ?? null;
$user_id = $_SESSION['user_id'] ?? null; // ✅ Ensure it exists

// ✅ Fetch job listings
$query = "SELECT j.*, jt.job_type_name, e.company_name, u.fullname, e.company_logo, e.company_address
          FROM tbl_job_post j
          LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
          LEFT JOIN tbl_user u ON e.employer_id = u.user_id
          LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
          ORDER BY j.posted_at DESC";

$result = mysqli_query($conn, $query);

// ✅ Fetch profile picture (default if not found)
$profile_pic = "image/no-profile.png";

if ($user_id) {
    $query = "SELECT j.*, jt.job_type_name, e.company_name, u.fullname, e.company_logo, e.company_address
              FROM tbl_job_post j
              LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
              LEFT JOIN tbl_user u ON e.employer_id = u.user_id
              LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
              INNER JOIN tbl_bookmarks b ON j.job_id = b.job_id
              WHERE b.applicant_id = ?
              ORDER BY j.posted_at DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
}


// ✅ Check if there are job listings
if (mysqli_num_rows($result) == 0) {
    echo "<p>No job listings available.</p>";
}

// ✅ Fetch bookmarked jobs
$bookmarked_jobs = [];
if ($user_id) {  // ✅ Use $user_id instead of undefined $applicant_id
    $bookmark_query = "SELECT job_id 
                       FROM tbl_bookmarks 
                       WHERE applicant_id = ?";

    $stmt = mysqli_prepare($conn, $bookmark_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $bookmark_result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($bookmark_result)) {
        $bookmarked_jobs[] = $row['job_id'];
    }

    mysqli_stmt_close($stmt);
}
?>




<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More Jobs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }

        .heading {
            font-size: 24px;
            font-weight: 600;
            color: #000;
            align-self: flex-start;
            margin-left: 10px;
            margin-top: 10px;
        }

        .featured-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
            max-height: 83vh;
            /* Makes it scrollable */
            padding: 10px;
            padding-bottom: 70px;
            width: 100%;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge */

        }

        .featured {
            width: 100%;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .logo img {
            position: absolute;
            width: 48px;
            /* Fixed size */
            height: 48px;
            /* Fixed size */
            border-radius: 5px;
            object-fit: cover;
            /* Ensures the image maintains its aspect ratio */
        }


        .job-title {
            position: absolute;
            left: 80px;
            top: 15px;
            font-family: 'SF Pro Display', sans-serif;
            font-weight: 500;
            font-size: 24px;
            color: #000;
        }

        .company-name {
            position: absolute;
            left: 80px;
            top: 45px;
            font-family: 'SF Pro Display', sans-serif;
            font-weight: 400;
            font-size: 14px;
            color: #666;
        }

        .bookmark {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: black;
        }

        .job-roles {
            position: absolute;
            left: 20px;
            top: 75px;
            background: #EBEBEB;
            border-radius: 30px;
            padding: 5px 10px;
            font-size: 12px;
        }

        .job-type {
            position: absolute;
            left: 20px;
            top: 75px;
            background: #EBEBEB;
            border-radius: 30px;
            padding: 5px 10px;
            font-size: 12px;
        }

        .short-description {
            display: -webkit-box;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-weight: 300;
            font-size: 12px;
            color: #000;
            white-space: normal;
            word-wrap: break-word;
            overflow: hidden;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            margin-top: 100px;
            margin-bottom: 10px;
        }

        .apply-btn {
            margin-bottom: 5px;
        }

        .salary {

            font-family: 'SF Pro Display', sans-serif;
            font-size: 12px;
            font-weight: bold;
            color: #28a745;
        }

        .location {
            position: absolute;
            right: 20px;
            bottom: 10px;
            font-family: 'SF Pro Display', sans-serif;
            font-size: 12px;
            color: #000;
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

        }

        footer div {
            font-size: 28px;
            cursor: pointer;
        }

        .footer-button {
            border: none;
            background: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="heading "><a href="home.php" style="color: black;"><ion-icon
                    name="arrow-back-outline"></ion-icon></a></div>

        <div class="featured-container">
            <div class="heading speak">My Bookmarked Job</div>
            <?php while ($job = $result->fetch_assoc()): ?>
                <div class="featured job-container" data-job-id="<?= $job['job_id']; ?>">
                    <div class="bookmark">
                        <ion-icon
                            name="<?= in_array($job['job_id'], $bookmarked_jobs) ? 'bookmark' : 'bookmark-outline'; ?>"
                            class="bookmark-icon" data-job-id="<?= $job['job_id']; ?>"></ion-icon>

                    </div>
                    <div class="logo">
                        <img src="<?= !empty($job['company_logo']) ? htmlspecialchars($job['company_logo']) : 'image/no-profile.png'; ?>"
                            alt="Company Logo">
                    </div>
                    <div class="job-title"><?= htmlspecialchars($job['job_title']); ?></div>
                    <div class="company-name"><?= htmlspecialchars($job['company_name']); ?></div>
                    <div class="job-type"><?= htmlspecialchars($job['job_type_name']); ?></div>
                    <div class="short-description"><?= htmlspecialchars($job['job_description']); ?></div>
                    <a href="job_post.php?job_id=<?= $job['job_id']; ?>" class="apply-btn">View Details</a>
                    <div class="salary_range">₱<?= htmlspecialchars($job['salary_range']); ?>/Month</div>
                    <div class="location"><?= htmlspecialchars($job['company_address']); ?></div>
                </div>
            <?php endwhile; ?>
        </div>


    </div>

    <footer>
        <button id="getStartedNav3" class="footer-button" data-action="navigate" data-destination="home.php">
            <ion-icon name="home-outline" style=" font-size: 28px"></ion-icon>
        </button>
        <button id="getStartedNav4" class="footer-button" data-action="navigate" data-destination="messages.php">
            <ion-icon name="mail-outline" style="font-size: 28px"></ion-icon>
        </button>
        <button id="getStartedNav5" class="footer-button" data-action="navigate" data-destination="bookmarks.php">
            <ion-icon name="bookmark-outline" style="color: #1E4461; font-size: 28px"></ion-icon>
        </button>
        <button id="getStartedNav6" class="footer-button" data-action="navigate" data-destination="morejobs.php">
            <ion-icon name="grid-outline" style="font-size: 28px"></ion-icon>
        </button>
    </footer>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!--<script src="accessibility.js"></script>-->
    <script>
        document.querySelectorAll(".bookmark-icon").forEach(icon => {
            icon.addEventListener("click", function () {
                let jobId = this.dataset.jobId;
                let iconElement = this;

                fetch("bookmark.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `job_id=${jobId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            iconElement.setAttribute("name", data.bookmarked ? "bookmark" : "bookmark-outline");
                        } else {
                            alert("Failed to update bookmark.");
                        }
                    });
            });
        });

    </script>
    <?php
    include 'access.php';
    ?>


</body>

</html>