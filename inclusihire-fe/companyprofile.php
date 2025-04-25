<?php
session_start();

include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: employerlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'] ?? '';
    $company_address = $_POST['company_address'] ?? '';
    $company_size = $_POST['company_size'] ?? '';
    $industry = $_POST['industry'] ?? '';
    $company_logo = $_POST['company_logo'] ?? '';
    $since = $_POST['since'] ?? '';
    $about = $_POST['about'] ?? '';
    $website = $_POST['website'] ?? '';
    $company_type = $_POST['company_type'] ?? '';
    $head_office = $_POST['head_office'] ?? '';
    $specialization = $_POST['specialization'] ?? '';

    // ✅ Fix profile picture upload
    if (!empty($_FILES['company_logo']['name'])) {
        $upload_dir = 'image/';
        $filename = basename($_FILES['company_logo']['name']);
        $company_logo = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['company_logo']['tmp_name'], $company_logo)) {
            $company_logo = null; // Reset if failed
        }
    }

    // ✅ Check if profile already exists
    $checkQuery = "SELECT * FROM tbl_employer_profile WHERE user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ Update existing profile
        if ($company_logo) {
            $updateProfileQuery = "
            UPDATE tbl_employer_profile 
            SET company_name = ?, company_address = ?, company_size = ?, industry = ?, company_logo = ?, since = ?, about = ?, website = ?, company_type = ?, head_office = ?, specialization = ?
            WHERE user_id = ?
            ";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param(
                "sssssississi",
                $company_name,
                $company_address,
                $company_size,
                $industry,
                $company_logo,
                $since,
                $about,
                $website,
                $company_type,
                $head_office,
                $specialization,
                $user_id
            );

        } else {
            $updateProfileQuery = "
            UPDATE tbl_employer_profile 
            SET company_name = ?, company_address = ?, company_size = ?, industry = ?, since = ?, about = ?, website = ?, company_type = ?, head_office = ?, specialization = ?
            WHERE user_id = ?
            ";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param(
                "ssssississi",
                $company_name,
                $company_address,
                $company_size,
                $industry,
                $since,
                $about,
                $website,
                $company_type,
                $head_office,
                $specialization,
                $user_id
            );
        }
    } else {
        // ✅ Insert new profile record
        $insertProfileQuery = "
            INSERT INTO tbl_employer_profile (user_id, company_name, company_address, company_size, industry, company_logo, since, website, about, company_type, head_office, specialization)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($insertProfileQuery);
        $stmt->bind_param("isssssississ", $user_id, $company_name, $company_address, $company_size, $industry, $company_logo, $since, $website, $about, $company_type, $head_office, $specialization);
    }

    $stmt->execute();

    // ✅ Handle Company Gallery Image Upload
    if (!empty($_FILES['company_image']['name'][0])) {
        $upload_dir = 'image/';

        foreach ($_FILES['company_image']['tmp_name'] as $key => $tmp_name) {
            $filename = basename($_FILES['company_image']['name'][$key]);
            $filepath = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $filepath)) {
                $insertImageQuery = "INSERT INTO tbl_company_image (employer_id, image_path) VALUES (?, ?)";
                $stmt = $conn->prepare($insertImageQuery);
                $stmt->bind_param("is", $user_id, $filepath);
                $stmt->execute();
            }
        }
    }
}

// ✅ Fetch updated data
$sql = "
    SELECT 
        p.*
    FROM tbl_user a
    LEFT JOIN tbl_employer_profile p 
    ON a.user_id = p.user_id
    WHERE a.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = [
        'company_name' => '',
        'company_size' => '',
        'industry' => '',
        'company_logo' => 'image/no-profile.png',
        'company_address' => ''
    ];
}

$query = "SELECT company_name, company_address, since 
          FROM tbl_employer_profile 
          WHERE user_id = '$user_id'";

$result = mysqli_query($conn, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $company_name = htmlspecialchars($row['company_name']);
    $company_address = htmlspecialchars($row['company_address']);
    $since = htmlspecialchars($row['since']);
} else {
    $company_name = "Unknown Company";
    $company_address = "Unknown Location";
    $since = "N/A";
}



$query = "SELECT employer_id FROM tbl_employer_profile WHERE user_id = '$user_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $employer_id = $row['employer_id'];
} else {
    die("Error: Employer profile not found.");
}

// Fetch the latest uploaded image for the employer
$query = "SELECT image_path FROM tbl_company_gallery WHERE employer_id = '$employer_id' ORDER BY uploaded_at DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $first_image = $row['image_path'];
} else {
    $first_image = 'image/no-profile.png'; // Default image
}


if (isset($_FILES['company_gallery']) && $_FILES['company_gallery']['error'] == 0) {
    $upload_dir = 'image/';  // Ensure this folder exists
    $file_name = time() . '_' . basename($_FILES['company_gallery']['name']);
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['company_gallery']['tmp_name'], $file_path)) {
        // Check if an image already exists for this employer
        $check_query = "SELECT * FROM tbl_company_gallery WHERE employer_id = '$employer_id' ORDER BY uploaded_at ASC LIMIT 2";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            // Update the first image
            $row = mysqli_fetch_assoc($check_result);
            $gallery_id = $row['gallery_id'];

            $update_query = "UPDATE tbl_company_gallery SET image_path = '$file_path', uploaded_at = NOW() WHERE gallery_id = '$gallery_id'";
            mysqli_query($conn, $update_query);
        } else {
            // Insert new image if no image exists
            $insert_query = "INSERT INTO tbl_company_gallery (employer_id, image_path, uploaded_at) 
                             VALUES ('$employer_id', '$file_path', NOW())";
            if (!mysqli_query($conn, $insert_query)) {
                die("Error inserting image: " . mysqli_error($conn));
            }
        }

        echo "<script>alert('Image uploaded successfully!'); window.location.href='companyprofile.php';</script>";
    } else {
        echo "<script>alert('Image upload failed.'); window.history.back();</script>";
    }
}

?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Your Existing CSS (no changes) */
        .backarrow {
            position: absolute;
            width: 24px;
            height: 24px;
            left: 21px;
            top: 51px;
        }

        .editprofile {
            position: absolute;
            width: 221px;
            height: 28px;
            left: 100px;
            top: 50px;
            font-size: 20px;
            font-family: 'Poppins';
            font-weight: 600;
            line-height: 28px;
            text-align: left;

            color: #000000;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .profile-picture {
            position: relative;
            display: inline-block;
            margin-top: 100px;
        }

        .picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }


        .change-picture-btn-1 {
            position: absolute;
            top: 38px;
            right: 25px;
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

        .change-picture-btn-2 {
            position: absolute;
            top: 1190px;
            right: 95px;
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



        .info-container {
            margin-top: 0px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            max-width: 348px;
        }

        .box {
            border: 1px solid grey;
            border-radius: 10px;
            padding: 8px 12px;
        }

        .sub-title {
            font-size: 12px;
            font-weight: bold;
            color: #555;
        }

        .info {
            font-size: 14px;
            color: #333;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }


        .button {

            width: 224px;
            height: 44px;
            left: 75px;
            top: 728px;
            background: #1E4461;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            text-align: center;
        }

        .information {
            padding: 10px;
            margin-top: 220px;
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


        .image {
            gap: 20px;
            display: flex;
            justify-content: space-evenly;
            margin-bottom: 10px;
        }

        .image img {
            height: 150px;
            width: 160px;
            border-radius: 10px;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container">
            <a href="employerprofile.php">
                <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
            </a>

        </div>

        <form method="post" enctype="multipart/form-data">

            <div class="header">
                <div class="logo">
                    <img class="picture"
                        src="<?= !empty($user['company_logo']) ? $user['company_logo'] : 'image/no-profile.png'; ?>"
                        alt="Profile Picture">
                    <button type="button" class="change-picture-btn-1" onclick="triggerFileInput()">
                        <ion-icon name="add-circle-outline" style="color:black;"></ion-icon>
                    </button>
                    <input type="file" name="company_logo" id="company_logo" style="display: none;">
                </div>
                <div class="info">
                    <div class="company-name"><?= $company_name; ?></div>
                    <div>
                        <span class="speak"><?= $company_name; ?></span>
                        <span>&#8226;</span>
                        <span class="speal"><?= $company_address; ?></span>
                        <span>&#8226;</span>
                        <span class="speak"><?= $since; ?></span>
                    </div>
                </div>
            </div>


            <div class="information">
                <div class="division">
                    <div class="sub-title">Company Name</div>
                    <div><input type="text" class="form-control" name="company_name"
                            value="<?= htmlspecialchars($user['company_name']) ?>"></div>
                </div>
                <div class="division">
                    <div class="sub-title">Company Address</div>
                    <div><input type="text" class="form-control" name="company_address"
                            value="<?= htmlspecialchars($user['company_address']) ?>"></div>
                </div>
                <div class="division">
                    <div class="sub-title">About Company</div>
                    <div><input type="text" class="form-control" name="about"
                            value="<?= htmlspecialchars($user['about']) ?>"
                            style="width: 100%; height: 75px; font-size: 18px;">
                    </div>
                </div>

                <div class="division">
                    <div class="sub-title">Website</div>
                    <div class="div-text"><input type="text" class="form-control" name="website"
                            value="<?= htmlspecialchars($user['website']) ?>"></div>
                </div>

                <div class="division">
                    <div class="sub-title">Industry</div>
                    <div class="div-text"><input type="text" class="form-control" name="industry"
                            value="<?= htmlspecialchars($user['industry']) ?>"></div>
                </div>

                <div class="division">
                    <div class="sub-title">Employee Size</div>
                    <div class="div-text"><input type="text" class="form-control" name="company_size"
                            value="<?= htmlspecialchars($user['company_size']) ?>"></div>
                </div>

                <div class="division">
                    <div class="sub-title">Company Type</div>
                    <div class="div-text">
                        <select class="form-select" name="company_type" required>
                            <?php
                            $company_type = mysqli_query($conn, "SELECT * FROM tbl_company_type");
                            while ($row = mysqli_fetch_assoc($company_type)) {
                                echo "<option value='{$row['company_type_id']}'>{$row['company_type']}</option>";
                            }
                            ?>
                        </select>

                    </div>
                </div>

                <div class="division">
                    <div class="sub-title">Head Office</div>
                    <div class="div-text"><input type="text" class="form-control" name="head_office"
                            value="<?= htmlspecialchars($user['head_office']) ?>"></div>
                </div>

                <div class="division">
                    <div class="sub-title">Since</div>
                    <div class="div-text"><input type="text" class="form-control" name="since"
                            value="<?= htmlspecialchars($user['since']) ?>"></div>
                </div>

                <div class="division">
                    <div class="sub-title">Speciaization</div>
                    <div class="div-text"><input type="text" class="form-control" name="specialization"
                            value="<?= htmlspecialchars($user['specialization']) ?>"></div>
                </div>

                <div class="division">
                    <div class="sub-title">Company Gallery</div>

                </div>



            </div>





            <div class="image">

                <div class="div-text">
                    <img class="image" id="profileImage" src="<?= htmlspecialchars($first_image); ?>"
                        alt="Profile Picture" onerror="this.src='image/no-profile.png';">
                </div>
                <!-- Display uploaded profile image or default -->


                <!-- Button to trigger file input -->
                <button type="button" class="change-picture-btn-2" onclick="triggerFileInput()">
                    <ion-icon name="add-circle-outline" style="color:black;"></ion-icon>
                </button>

                <!-- Hidden file input -->
                <input type="file" name="company_gallery" id="company_gallery" style="display: none;"
                    onchange="previewImage(event)">
            </div>



            <div class="button-container">
                <button type="submit" class="button">Submit</button>
            </div>
        </form>

    </div>


    <script>
        // Function to trigger file input when button is clicked
        function triggerFileInput() {
            document.getElementById('company_gallery').click();
        }

        // Function to preview image before upload
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }


        function triggerFileInput() {
            document.getElementById('company_logo').click();
        }

        document.getElementById('company_logo').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('.profile-picture .picture').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

    </script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>


</body>

</html>