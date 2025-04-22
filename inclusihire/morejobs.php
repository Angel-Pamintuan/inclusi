<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;
include 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Fetch applicant's impairment and job type
$sql = "SELECT u.impairment_id, u.job_type_id 
        FROM tbl_user u
        WHERE u.user_id = ?";

$stmt = $conn->prepare($sql);
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


$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// SQL query to fetch jobs based on the search query
$sql = "SELECT j.*, jt.job_type_name, e.company_name, e.company_logo, e.company_address
        FROM tbl_job_post j
        LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
        LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
        WHERE j.job_title LIKE ? OR e.company_name LIKE ? 
        ORDER BY j.posted_at DESC";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%"; // For partial matching
$stmt->bind_param("ss", $search_param, $search_param); // Bind the parameters

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
?>






<!DOCTYPE html>
<html lang="en">

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

        footer button {
            border: none;
            background: none;
        }

        .search {
            width: 225px;
            height: 35px;
            border: none;
            background: #F1F1F1;
            border-radius: 10px;
            padding: 5px 10px;
            margin-bottom: 10px;
        }

        .find-job {
            width: 100px;
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
    </style>
</head>

<body>

    <div class="container">
        <div class="heading"><a href="home.php" style="color: black;"><ion-icon
                    name="arrow-back-outline"></ion-icon></a></div>

        <div class="featured-container">
            <div class="heading">Discover Jobs</div>

            <form method="GET" action="morejobs.php">
                <div>
                    <input type="search" class="search" name="search_query" placeholder="Search for jobs"
                        aria-label="Search">
                    <button type="submit" class="find-job" id="getStartedForm1">
                        Find Job
                        <ion-icon name="sparkles-outline"></ion-icon>
                    </button>
                </div>
            </form>


            <?php
            // Assuming the query has been executed and results are available in $job_result
            if (mysqli_num_rows($result) > 0) {
                while ($job = mysqli_fetch_assoc($result)) {
                    echo "
                    <div class='featured'>
                        <div class='bookmark'>
                            <ion-icon
                                name='" . (in_array($job['job_id'], $bookmarked_jobs) ? "bookmark" : "bookmark-outline") . "'
                                class='bookmark-icon' data-job-id='" . htmlspecialchars($job['job_id']) . "'>
                            </ion-icon>
                        </div>
        
                        <div class='logo'>
                            <img src='" . (!empty($job['company_logo']) ? $job['company_logo'] : "image/pic.png") . "' alt='Company Logo'>
                        </div>
                        <div class='job-title'>" . htmlspecialchars($job['job_title']) . "</div>
                        <div class='company-name'>" . htmlspecialchars($job['company_name']) . "</div>
                        <div class='job-type'>" . htmlspecialchars($job['job_type_name']) . "</div>
                        <div class='short-description'>" . htmlspecialchars($job['job_description']) . "</div>
                        <div class='salary'>₱" . htmlspecialchars($job['salary_range']) . "/Month</div>
                        <div class='location'>" . htmlspecialchars($job['company_address']) . "</div>
                        <a href='job_post.php?job_id=" . htmlspecialchars($job['job_id']) . "' class='apply-btn'>View Details</a>
                    </div>";
                }
            } else {
                echo "❌ No job listings found.";
            }

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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script src="access.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".bookmark-icon").forEach(icon => {
                icon.addEventListener("click", function () {
                    let jobId = this.getAttribute("data-job-id");
                    let iconElement = this;

                    fetch("bookmark.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `job_id=${jobId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server Response:", data); // Debugging output
                            if (data.success) {
                                iconElement.setAttribute("name", data.bookmarked ? "bookmark" : "bookmark-outline");
                            } else {
                                alert("Failed to update bookmark: " + data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        });

    </script>
    <?php include 'access.php' ?>

</body>

</html>