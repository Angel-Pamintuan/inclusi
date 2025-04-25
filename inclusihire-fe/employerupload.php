<?php
session_start();
include 'database.php';

// Check user session
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User not logged in.'); window.history.back();</script>";
    exit;
}

$employer_id = $_SESSION['user_id']; // Session user_id
//var_dump($employer_id); // Debugging: Check if user_id is set

// Fetch employer_id from tbl_employer_profile
$query = "SELECT employer_id FROM tbl_employer_profile WHERE user_id = '$employer_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching employer profile: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo "<script>alert('Employer profile not found. Please complete your profile first.'); window.history.back();</script>";
    exit;
}

$employer_id = $row['employer_id']; // Correct employer_id assignment

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $job_title = mysqli_real_escape_string($conn, $_POST['jobtitle'] ?? '');
    $job_desc = mysqli_real_escape_string($conn, $_POST['jobdesc'] ?? '');
    $qualifications = mysqli_real_escape_string($conn, $_POST['qualifications'] ?? '');
    $salary_range = mysqli_real_escape_string($conn, $_POST['salary_range'] ?? '');
    $impairment = mysqli_real_escape_string($conn, $_POST['impairment'] ?? '');
    $job_type_id = mysqli_real_escape_string($conn, $_POST['job_type_id'] ?? '');
    $vacancy = mysqli_real_escape_string($conn, $_POST['vacancy'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');
    $posted_at = date('Y-m-d H:i:s');
    $job_image = '';

    // Handle file upload safely
    if (!empty($_FILES['job_image']['name'])) {
        $upload_dir = 'image/';
        $filename = basename($_FILES['job_image']['name']);
        $job_image = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['job_image']['tmp_name'], $job_image)) {
            $job_image = null; // Reset if failed
        }
    }

    // Insert into tbl_job_post
    $query = "INSERT INTO tbl_job_post 
              (employer_id, job_title, job_description, job_qualification, salary_range, job_impairment, job_type_id, vacancy, job_image, posted_at, status) 
              VALUES ('$employer_id', '$job_title', '$job_desc', '$qualifications', '$salary_range', '$impairment', '$job_type_id', '$vacancy', '$job_image', '$posted_at', '$status')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Job posted successfully!'); window.location.href='employerhome.php';</script>";
    } else {
        echo "<script>alert('Error posting job: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Upload Job</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }



        .profile-picture {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-picture img {
            display: block;
            max-width: 150px;
            height: auto;
            border-radius: 10%;
            margin-bottom: 10px;
        }

        .form-label {
            display: block;
            margin-top: 10px;
        }


        .form-control {
            width: 100%;
            height: 55px;
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .form-select {
            width: 100%;
            height: 55px;
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1E4461;
            text-align: center;
            margin-top: 40px;
        }

        .btn-submit {
            background-color: #1E4461;
            color: white;
            border: none;
            padding: 15px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .btn-submit:hover {
            background-color: #16334a;
        }

        .backarrow {
            /* Header */

            position: absolute;
            width: 24px;
            height: 24px;
            left: 21px;
            top: 51px;
        }

        .change-picture-btn {
            position: absolute;
            top: 250px;
            left: 250px;

            background: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            color: black;
            font-size: 24px;
            padding: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        button {}
    </style>
</head>

<body>
    <div class="container">
        <a href="employerhome.php">
            <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
        </a>
        <div class="title">Post a Job</div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="profile-picture">
                <div class="sub-title">Job Image</div>
                <img class="picture" id="previewImage" src="image/no-profile.png" alt="Job Image">
                <button type="button" class="change-picture-btn" onclick="triggerFileInput()">
                    <ion-icon name="add-circle-outline" style="color:black;"></ion-icon>
                </button>
                <input type="file" name="job_image" id="job_image" style="display: none;">
            </div>

            <label for="jobtitle">Job Title</label>
            <input type="text" class="form-control" id="jobtitle" name="jobtitle" required>

            <label for="jobdesc">Job Description</label>
            <textarea class="form-control" id="jobdesc" name="jobdesc" rows="4" required></textarea>

            <label for="qualifications">Qualifications</label>
            <textarea class="form-control" id="qualifications" name="qualifications" rows="4" required></textarea>

            <label for="salary">Salary Range</label>
            <input type="text" class="form-control" id="salary" name="salary_range" required>

            <label for="impairment">Suitable for Impairment</label>
            <select class="form-select" name="impairment" required>
                <?php
                $impairment = mysqli_query($conn, "SELECT * FROM tbl_impairment");
                while ($row = mysqli_fetch_assoc($impairment)) {
                    echo "<option value='{$row['impairment_id']}'>{$row['impairment']}</option>";
                }
                ?>
            </select>

            <label for="jobtype">Job Type</label>
            <select class="form-select" name="job_type_id" required>
                <?php
                $job_type_name = mysqli_query($conn, "SELECT * FROM tbl_job_type");
                while ($row = mysqli_fetch_assoc($job_type_name)) {
                    echo "<option value='{$row['job_type_id']}'>{$row['job_type_name']}</option>";
                }
                ?>
            </select>

            <label for="vacancy">Number of Vacancies</label>
            <input type="number" class="form-control" id="vacancy" name="vacancy" required>

            <label for="status">tatus</label>
            <select class="form-select" name="status" required>
                <?php
                $status = mysqli_query($conn, "SELECT * FROM tbl_status");
                while ($row = mysqli_fetch_assoc($status)) {
                    echo "<option value='{$row['status_id']}'>{$row['status']}</option>";
                }
                ?>
            </select>


            <button type="submit" class="btn-submit">Create Job</button>
        </form>
    </div>

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