<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: applicantlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';

    // Handle profile picture upload (optional)
    $profile_picture = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = 'image/';
        $profile_picture = $upload_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    // ✅ Update tbl_user (fullname, gmail, phone)
    $updateApplicantQuery = "
        UPDATE tbl_user
        SET fullname = ?, email = ?, phone = ?
        WHERE user_id = ?
    ";
    $stmt = $conn->prepare($updateApplicantQuery);
    $stmt->bind_param("sssi", $fullname, $email, $phone, $user_id);
    $stmt->execute();

    // ✅ Check if profile already exists in tbl_applicant_profile
    $checkQuery = "SELECT * FROM tbl_applicant_profile WHERE applicant_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ Update existing profile (profile_pic only if uploaded)
        if ($profile_picture) {
            $updateProfileQuery = "
                UPDATE tbl_applicant_profile 
                SET gender = ?, address = ?, birthdate = ?, profile_pic = ?
                WHERE applicant_id = ?
            ";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param("ssssi", $gender, $address, $birthdate, $profile_picture, $user_id);
        } else {
            $updateProfileQuery = "
                UPDATE tbl_applicant_profile 
                SET gender = ?, address = ?, birthdate = ?
                WHERE applicant_id = ?
            ";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param("sssi", $gender, $address, $birthdate, $user_id);
        }
    } else {
        // ✅ Insert new profile record
        $insertProfileQuery = "
            INSERT INTO tbl_applicant_profile (applicant_id, gender, address, birthdate, profile_pic)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($insertProfileQuery);
        $stmt->bind_param("issss", $user_id, $gender, $address, $birthdate, $profile_picture);
    }

    $stmt->execute();
}

// ✅ Fetch updated data
$sql = "
    SELECT 
        a.fullname, 
        a.email, 
        a.phone, 
        p.birthdate, 
        p.profile_pic,
        p.gender, 
        p.address
    FROM tbl_user a
    LEFT JOIN tbl_applicant_profile p 
    ON a.user_id = p.applicant_id
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
        'fullname' => 'Unknown',
        'email' => 'Unknown',
        'phone' => 'Unknown',
        'birthdate' => '',
        'profile_pic' => 'image/no-profile.png',
        'gender' => '',
        'address' => ''
    ];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            left: 80px;
            top: 50px;
            font-size: 24px;
            font-family: 'Poppins';
            font-weight: 600;
            line-height: 28px;
            text-align: center;
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
            <a href="applicantprofile.php">
                <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
            </a>
            <div class="editprofile">Edit Profile</div>
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="profile-container">
                <div class="profile-picture">
                    <img class="picture"
                        src="<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'image/no-profile.png'; ?>"
                        alt="Profile Picture">
                    <button type="button" class="change-picture-btn" onclick="triggerFileInput()">
                        <ion-icon name="add-circle-outline" style="color:black;"></ion-icon>
                    </button>
                    <input type="file" name="profile_picture" id="profilePictureInput" style="display: none;">
                </div>


                <div class="info-container">
                    <div class="box">
                        <div class="sub-title">Fullname</div>
                        <input type="text" class="form-control" name="fullname"
                            value="<?= htmlspecialchars($user['fullname']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Gmail</div>
                        <input type="email" class="form-control" name="email"
                            value="<?= htmlspecialchars($user['email']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Phone Number</div>
                        <input type="text" class="form-control" name="phone"
                            value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Birthdate</div>
                        <input type="date" class="form-control" name="birthdate"
                            value="<?= htmlspecialchars($user['birthdate']) ?>">
                    </div>

                    <div class="box">
                        <div class="sub-title">Gender</div>
                        <select class="form-select" name="gender" required>
                            <?php
                            $genders = mysqli_query($conn, "SELECT * FROM tbl_gender");
                            while ($row = mysqli_fetch_assoc($genders)) {
                                $selected = ($row['gender_id'] == $user['gender']) ? 'selected' : '';
                                echo "<option value='{$row['gender_id']}' $selected>{$row['gender']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="box">
                        <div class="sub-title">Address</div>
                        <input type="text" class="form-control" name="address"
                            value="<?= htmlspecialchars($user['address']) ?>">
                    </div>

                    <button type="submit" class="button">Submit</button>
                </div>
            </div>
        </form>

    </div>


    <script>
        function triggerFileInput() {
            document.getElementById('profilePictureInput').click();
        }

        document.getElementById('profilePictureInput').addEventListener('change', function (event) {
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