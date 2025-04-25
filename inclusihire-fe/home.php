<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Fetch job listings
$query = "SELECT u.impairment_id, u.job_type_id 
        FROM tbl_user u
        WHERE u.user_id = ?";

$stmt = $conn->prepare($query); // ✅ Use $query instead of $sql
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_profile = $result->fetch_assoc();

if (!$user_profile) {
    die("User profile not found for user_id: " . htmlspecialchars($user_id));
}

// Get impairment and job type from the user profile
$user_impairment = $user_profile['impairment_id'] ?? null;
$user_job_type = $user_profile['job_type_id'] ?? null;

// Fetch jobs that match the user's impairment and job type (PRIORITY JOBS)
$priority_jobs_query = "SELECT j.*, jt.job_type_name, e.company_name, e.company_logo, e.company_address
                        FROM tbl_job_post j
                        LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
                        LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
                        WHERE j.job_impairment = ? 
                        AND j.job_type_id = ? 
                        ORDER BY j.posted_at DESC";

$stmt = $conn->prepare($priority_jobs_query);
$stmt->bind_param("ii", $user_impairment, $user_job_type);
$stmt->execute();
$priority_result = $stmt->get_result();

// Fetch all other jobs (NON-MATCHING JOBS)
$all_jobs_query = "SELECT j.*, jt.job_type_name, e.company_name, e.company_logo, e.company_address
                   FROM tbl_job_post j
                   LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
                   LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
                   WHERE (j.job_impairment != ? OR j.job_type_id != ?)
                   ORDER BY j.posted_at DESC";

$stmt = $conn->prepare($all_jobs_query);
$stmt->bind_param("ii", $user_impairment, $user_job_type);
$stmt->execute();
$all_jobs_result = $stmt->get_result();

// Combine results: first matching jobs, then non-matching jobs
$jobs = [];
while ($job = $priority_result->fetch_assoc()) {
    $jobs[$job['job_id']] = $job; // Store by job_id to prevent duplicates
}
while ($job = $all_jobs_result->fetch_assoc()) {
    if (!isset($jobs[$job['job_id']])) { // Add only if not already included
        $jobs[$job['job_id']] = $job;
    }
}

if (empty($jobs)) {
    echo "<p style='color:red'>❌ No jobs found matching your criteria.</p>";
}


// Set default profile picture
$profile_pic = "image/no-profile.png";

// Fetch user's profile picture
$profile_query = "
    SELECT p.profile_pic 
    FROM tbl_user a
    LEFT JOIN tbl_applicant_profile p ON a.user_id = p.applicant_id
    WHERE a.user_id = ?
";
$stmt = $conn->prepare($profile_query); // ✅ Use $profile_query instead of $sql
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!empty($row['profile_pic'])) {
        $profile_pic = $row['profile_pic']; // ✅ Correct path from the database
    }
}

// Fetch bookmarked jobs
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


// Count jobs by type
$job_type_counts = [
    'Remote Job' => 0,
    'Full Time' => 0,
    'Part Time' => 0,
];

$type_query = "
    SELECT jt.job_type_name, COUNT(*) as total
    FROM tbl_job_post j
    JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
    GROUP BY jt.job_type_name
";

$result = $conn->query($type_query);
while ($row = $result->fetch_assoc()) {
    $type = $row['job_type_name'];
    $count = $row['total'];

    if (isset($job_type_counts[$type])) {
        $job_type_counts[$type] = $count;
    }
}

?>



<!-- ✅ Corrected HTML -->




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .container {
            display: flex;
            flex-direction: column;
            /* Ensures everything stacks properly */
            /*align-items: center;*/
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px;
            margin: 20px 0;
        }

        .title {
            font-size: 18px;
            color: #95969D;
        }

        .profile {
            height: 55px;
            width: 55px;
            border-radius: 50%;
        }

        .search {
            width: 237px;
            height: 35px;
            border: none;
            background: #F1F1F1;
            border-radius: 10px;
            padding: 5px 10px;
            margin-bottom: 10px;
        }

        .find-job {
            width: 109px;
            height: 35px;
            border: none;
            color: white;
            background: linear-gradient(90.18deg, rgba(89, 156, 244, 0.8) 5.64%, rgba(3, 49, 108, 0.8) 98.35%);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        button {
            border: none;
            background: none;
        }

        .heading {
            font-size: 24px;
            font-weight: 600;
            color: #000;
            align-self: flex-start;
            margin-left: 10px;
        }

        .featured-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 10px;
            white-space: nowrap;
            width: 100%;
        }

        .featured {
            width: 325px;
            min-width: 325px;
            height: 220px;
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
            /* Allows text to wrap */
            word-wrap: break-word;
            /* Breaks long words */
            overflow: hidden;
            -webkit-line-clamp: 3;
            /* Limit to 3 lines */
            -webkit-box-orient: vertical;
            margin-top: 100px;
            /* Prevent overlap with other elements */
        }



        .salary {
            position: absolute;
            left: 20px;
            bottom: 10px;
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

        .show-more {
            /* Rectangle 119 */

            position: absolute;
            width: 131px;
            height: 31px;
            left: calc(50% - 131px/2);
            top: 472px;

            background: #1E4461;
            border-radius: 5px;

        }

        .resume {
            /* Rectangle 120 */

            position: absolute;
            width: 345px;
            height: 147px;
            left: 24px;
            top: 558px;

            /* vluuuu */
            background: linear-gradient(90.18deg, rgba(89, 156, 244, 0.8) 5.64%, rgba(3, 49, 108, 0.8) 98.35%);
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            color: white;
            font-size: 24px;

        }

        footer {
            margin-top: 200px;
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

        .heading-1 {
            font-size: 24px;
            font-weight: 600;
            color: #000;

            margin-top: 40px;
            text-align: left;
            margin-left: 10px;
        }

        .orange {
            /* Rectangle 232 */

            position: absolute;
            width: 150px;
            height: 170px;
            left: 30px;
            top: 550px;

            background: #FCA34D;
            border-radius: 6px;

        }

        .darkblue {
            /* Rectangle 233 */

            position: absolute;
            width: 156px;
            height: 75px;
            left: 200px;
            top: 550px;

            background: #2C72A8;
            border-radius: 6px;

        }

        .lightblue {
            /* Rectangle 234 */

            position: absolute;
            width: 156px;
            height: 75px;
            left: 200px;
            top: 645px;

            background: #ADD3FF;
            border-radius: 6px;

        }

        .row {
            display: flex;
            flex-wrap: wrap;
            text-align: center;
            justify-content: center;
            font-family: 'DM Sans';
        }

        .total {
            font-weight: bold;
        }

        .orange,
        .darkblue,
        .lightblue {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Centers horizontally */
            justify-content: center;
            /* Centers vertically */
            text-align: center;
            /* Ensures text alignment */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container" id="jobContainer">
            <div class="title speak">Welcome to InclusiHire!</div>
            <button id="getStartedNav1"><img class="profile speak" src="<?= htmlspecialchars($profile_pic); ?>"
                    alt="Profile Picture">
            </button>
        </div>
        <div>
            <input type="search" class="search" id="jobSearchInput" placeholder=" Search" aria-label="Search">
            <button type="button" class="find-job" id="searchBtn">Find Job<ion-icon
                    name="sparkles-outline"></ion-icon></button>
        </div>
        <div class="heading speak">Featured Jobs</div>

        <div class="featured-container">

            <?php
            if (!empty($jobs)): // ✅ Use $jobs instead of re-running the query
                foreach ($jobs as $job): ?>
                    <div class="featured">
                        <div class="bookmark" id="getStartedForm2">
                            <ion-icon
                                name="<?= in_array($job['job_id'], $bookmarked_jobs) ? 'bookmark' : 'bookmark-outline'; ?>"
                                class="bookmark-icon" data-job-id="<?= $job['job_id']; ?>"></ion-icon>
                        </div>
                        <div class="logo">
                            <img src="<?= !empty($job['company_logo']) ? $job['company_logo'] : 'image/pics.png'; ?>"
                                alt="Company Logo">
                        </div>
                        <div class="job-title speak"><?= htmlspecialchars($job['job_title']); ?></div>
                        <div class="company-name speak"><?= htmlspecialchars($job['company_name']); ?></div>
                        <div class="job-type speak"><?= htmlspecialchars($job['job_type_name']); ?></div>
                        <div class="short-description speak"><?= htmlspecialchars($job['job_description']); ?></div>
                        <div class="salary speak">₱<?= htmlspecialchars($job['salary_range']); ?>/Month</div>
                        <div class="location speak"><?= htmlspecialchars($job['company_address']); ?></div>

                        <a href="job_post.php?job_id=<?= $job['job_id']; ?>" id="getStartedNav7" class="apply-btn">View
                            Details</a>
                    </div>
                <?php endforeach;
            else:
                echo "❌ No job listings found.";
            endif;
            ?>









            <div class="featured">
                <div class="bookmark"><ion-icon name="bookmark-outline"></ion-icon></div>
                <div class="logo"></div>
                <div class="job-title">Cashier</div>
                <div class="company-name">Jollibee</div>
                <div class="job-roles">Cashier</div>
                <div class="job-type">Part-time</div>
                <div class="short-description">Responsible for handling transactions, assisting customers, and
                    maintaining a
                    clean workspace.</div>
                <div class="salary">₱180,000/Month</div>
                <div class="location">San Fernando, Pampanga</div>
            </div>

            <div class="featured">
                <div class="bookmark"><ion-icon name="bookmark-outline"></ion-icon></div>
                <div class="logo"></div>
                <div class="job-title">Barista</div>
                <div class="company-name">Starbucks</div>
                <div class="job-roles">Beverage</div>
                <div class="job-type">Full-time</div>
                <div class="short-description">Prepares drinks, serves customers, and ensures high-quality customer
                    service.</div>
                <div class="salary">₱220,000/Month</div>
                <div class="location">Angeles, Pampanga</div>
            </div>
        </div>

        <button type="button" class="show-more" id="getStartedNav2" style="color:white;">More</button>

        <div id="dashboard">
            <div class="heading-1 speak">Featured Jobs</div>
            <div class="row">
                <div class="orange col-lg-6 speak">
                    <div class="total"><?= number_format($job_type_counts['Remote Job']) ?></div>
                    <div>Remote Job</div>
                </div>
                <div class="col-lg-6 speak row">
                    <div class="darkblue col-lg-6">
                        <div class="total"><?= number_format($job_type_counts['Full Time']) ?></div>
                        <div>Full Time</div>
                    </div>
                    <div class="lightblue col-lg-6">
                        <div class="total"><?= number_format($job_type_counts['Part Time']) ?></div>
                        <div>Part Time</div>
                    </div>
                </div>
            </div>

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
        document.getElementById("searchBtn").addEventListener("click", function () {
            const input = document.getElementById("jobSearchInput").value.toLowerCase().replace(/\s/g, '');
            const jobCards = document.querySelectorAll(".featured");

            jobCards.forEach(card => {
                const title = card.querySelector(".job-title")?.textContent.toLowerCase().replace(/\s/g, '') || "";
                const company = card.querySelector(".company-name")?.textContent.toLowerCase().replace(/\s/g, '') || "";
                const jobType = card.querySelector(".job-type")?.textContent.toLowerCase().replace(/\s/g, '') || "";
                const address = card.querySelector(".location")?.textContent.toLowerCase().replace(/\s/g, '') || "";

                const combinedText = `${title} ${company} ${jobType} ${address}`;
                card.style.display = combinedText.includes(input) ? "block" : "none";
            });
        });


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


</body>

</html>