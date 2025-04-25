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
                SET company_name = ?, company_address = ?, company_size = ?, industry = ?, company_logo = ?
                WHERE user_id = ?
            ";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param("sssssi", $company_name, $company_address, $company_size, $industry, $company_logo, $user_id);
        } else {
            $updateProfileQuery = "
                UPDATE tbl_employer_profile 
                SET company_name = ?, company_address = ?, company_size = ?, industry = ?
                WHERE user_id = ?
            ";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param("ssssi", $company_name, $company_address, $company_size, $industry, $user_id);
        }
    } else {
        // ✅ Insert new profile record
        $insertProfileQuery = "
            INSERT INTO tbl_employer_profile (user_id, company_name, company_address, company_size, industry, company_logo)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($insertProfileQuery);
        $stmt->bind_param("isssss", $user_id, $company_name, $company_address, $company_size, $industry, $company_logo);
    }

    $stmt->execute();
}

// ✅ Fetch updated data
$sql = "
    SELECT 
        p.company_logo, 
        p.industry, 
        p.company_name,
        p.company_address,
        p.company_size
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
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility</title>
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


        .change-picture-btn {
            position: absolute;
            top: 78px;
            right: 0;
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

        .button {
            position: absolute;
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
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container">
            <a href="employerprofile.php">
                <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
            </a>
            <div class="editprofile">Edit Company Profile</div>
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="profile-container">
                <div class="profile-picture">
                    <div class="sub-title">Company Logo</div>
                    <img class="picture"
                        src="<?= !empty($user['company_logo']) ? $user['company_logo'] : 'image/no-profile.png'; ?>"
                        alt="Profile Picture">
                    <button type="button" class="change-picture-btn" onclick="triggerFileInput()">
                        <ion-icon name="add-circle-outline" style="color:black;"></ion-icon>
                    </button>
                    <input type="file" name="company_logo" id="company_logo" style="display: none;">
                </div>


                <div class="info-container">
                    <div class="box">
                        <div class="sub-title">Company Name</div>
                        <input type="text" class="form-control" name="company_name"
                            value="<?= htmlspecialchars($user['company_name']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Company Address</div>
                        <input type="text" class="form-control" name="company_address"
                            value="<?= htmlspecialchars($user['company_address']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Industry</div>
                        <input type="text" class="form-control" name="industry"
                            value="<?= htmlspecialchars($user['industry']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Company Size</div>
                        <input type="text" class="form-control" name="company_size"
                            value="<?= htmlspecialchars($user['company_size']) ?>">
                    </div>

                    <button type="submit" class="button">Submit</button>
                </div>
            </div>
        </form>

    </div>


    <script>
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