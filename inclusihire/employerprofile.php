<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';  // Make sure database connection is included

if (!isset($_SESSION['user_id'])) {
    header("Location: employerlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

echo "User ID from session: " . $_SESSION['user_id'] . "<br>";

// Fetch user data from tbl_applicant
$sql = "
    SELECT 
    p.company_logo, 
    p.industry, 
    p.company_name
    FROM tbl_user a
    LEFT JOIN tbl_employer_profile p 
    ON a.user_id = p.user_id
    WHERE a.user_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Error preparing query: " . $conn->error . "<br>";
    exit();
}

$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "Company Name: " . $user['company_name'] . "<br>";
        echo "Industry: " . $user['industry'] . "<br>";
        echo "Company Logo: " . $user['company_logo'] . "<br>";
    } else {
        echo "No data found.<br>";
    }
} else {
    echo "Error executing query: " . $stmt->error . "<br>";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility</title>

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
            <a href="employerhome.php">
                <ion-icon name="arrow-back-outline" class="backarrow" style="color:black;"></ion-icon>
            </a>
            <a href="">
                <ion-icon name="ellipsis-vertical-outline" class="dots" style="color:black;"></ion-icon>
            </a>
        </div>

        <?php
        $company_logo = !empty($user['company_logo']) ? htmlspecialchars($user['company_logo']) : 'image/no-profile.png';
        ?>
        <div>
            <img class="picture" src="<?= $company_logo ?>" alt="Profile Picture">


            <h3 class="fullname"><?= htmlspecialchars($user['company_name']) ?></h3>
            <div class="contact">
                <span><?= htmlspecialchars($user['industry']) ?></span>
            </div>
        </div>


        <div class="card">
            <a href="companyprofile.php" class="editprofile">
                <ion-icon name="person-outline"></ion-icon> Edit company information
            </a>
            <div class="notification">
                <div class="notif-text">
                    <ion-icon name="notifications-outline"></ion-icon>
                    Notification
                </div>
                <span class="status">ON</span>
            </div>
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

        function logout() {
            window.location.href = 'logout.php';
        }

    </script>


</body>

</html>