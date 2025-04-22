<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;
include 'database.php';  // Make sure the database connection is included

if (!isset($_SESSION['user_id'])) {
    header("Location: applicantlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];  // Correct variable name

// Fetch user data from tbl_user and tbl_applicant_profile
$sql = "
    SELECT 
        a.fullname, 
        a.email, 
        a.phone, 
        p.profile_pic
    FROM tbl_user a
    LEFT JOIN tbl_applicant_profile p ON a.user_id = p.applicant_id
    WHERE a.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // ✅ Use the correct variable ($user_id)
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = [
        'fullname' => 'Unknown',
        'email' => 'Unknown',
        'phone' => 'Unknown',
        'profile_pic' => 'image/no-profile.png'
    ];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .header-container {
            /* Rectangle 49 */

            position: absolute;
            width: 400px;
            height: 263px;
            left: -3px;
            top: 0px;

            background: #1E4461;


        }

        .backarrow {
            /* Header */

            position: absolute;
            width: 24px;
            height: 24px;
            left: 21px;
            top: 51px;


        }

        .dots {
            /* fill/system/more-2-fill */

            position: absolute;
            width: 24px;
            height: 24px;
            left: 345px;
            top: 51px;


        }

        .picture {
            /* Avatar */

            position: absolute;
            width: 127px;
            height: 130px;
            left: 127px;
            top: 222px;
            border-radius: 50%;
        }

        .change-picture-btn {
            position: absolute;
            left: 210px;
            top: 333px;
            background: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            padding: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-size: 24px;
            color: black;

        }

        .fullname {
            /* Puerto Rico */

            position: absolute;
            width: 194px;
            height: 28px;
            left: 93px;
            top: 357px;

            /* Title_Poppins/Large */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            font-size: 22px;
            line-height: 28px;
            /* identical to box height, or 127% */
            text-align: center;

            /* Light/Gray-11 */
            color: #000000;
        }

        .contact {
            position: absolute;
            left: 15.6%;
            right: 13.33%;
            top: 45.54%;
            bottom: 50%;
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            text-align: center;
            letter-spacing: 0.25px;
            color: #000000;
            display: flex;
            justify-content: center;
            gap: 5px;
            /* Adjust spacing */
        }

        .card {
            position: absolute;
            width: 348px;
            height: 121px;
            left: 22px;
            top: 439px;
            background: #FFFFFF;
            box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.25);
            border-radius: 8px;
            padding: 12px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* Make sure content flows naturally from top */
            gap: 5px;
            /* Add small spacing between items */
        }

        /* Edit profile link styling */
        .editprofile {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: black;
            font-size: 16px;
        }

        /* Notification row */
        .notification {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 16px;
            color: black;
        }

        /* Notification icon and text grouped together */
        .notif-text {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* "ON" text styling */
        .status {
            font-weight: bold;
            color: green;
        }

        .card-2 {
            /* Group 68 */

            position: absolute;
            width: 348px;
            height: 86px;
            left: 22px;
            top: 584px;
            background: #FFFFFF;
            box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.25);
            border-radius: 8px;
            padding: 12px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* Make sure content flows naturally from top */
            gap: 5px;


        }

        .access {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: black;
            font-size: 16px;
        }

        button {

            /* Auto layout */
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            padding: 14.5px 18px;
            gap: 10px;

            position: absolute;
            width: 224px;
            height: 44px;
            left: 75px;
            top: 728px;

            background: #1E4461;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            font-style: bold;

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container">
            <a href="home.php">
                <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
            </a>
            <a href="">
                <ion-icon name="ellipsis-vertical-outline" class="dots" style="color:black;"></ion-icon>
            </a>
        </div>

        <?php
        $profilePic = !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'image/no-profile.png';
        ?>
        <div>
            <img class="picture" src="<?= $profilePic ?>" alt="Profile Picture">

            <h3 class="fullname speak"><?= htmlspecialchars($user['fullname']) ?></h3>
            <div class="contact speak">
                <span><?= $user['email'] ?></span>
                <span> || </span>
                <span><?= htmlspecialchars($user['phone']) ?></span>
            </div>
        </div>


        <div class="card">
            <a href="editprofile.php" class="editprofile">
                <ion-icon name="person-outline"></ion-icon> Edit profile information
            </a>
            <div class="notification speak">
                <div class="notif-text">
                    <ion-icon name="notifications-outline"></ion-icon>
                    Notification
                </div>
                <span class="status">ON</span>
            </div>
        </div>

        <div class="card-2">
            <a href="" class="access speak">
                <ion-icon name="accessibility-outline"></ion-icon> Accessibility
            </a>
        </div>

        <button onclick="logout()">Logout</button>
    </div>






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
        function stopReading() {
            speechSynthesis.cancel(); // Immediately stops any ongoing speech
        }

        document.getElementById("readButton").addEventListener("click", function () {
            stopReading(); // Ensure previous speech stops before starting a new one

            const text = document.body.innerText;
            const speech = new SpeechSynthesisUtterance(text);
            speech.lang = "en-US";
            speech.rate = 1;

            speechSynthesis.speak(speech);
        });

        function logout() {
            window.location.href = 'logout.php';
        }

    </script>


</body>

</html>