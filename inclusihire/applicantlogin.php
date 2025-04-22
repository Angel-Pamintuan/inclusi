<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

if (isset($_SESSION['status'])) {
    echo "<script>alert('" . $_SESSION['status'] . "');</script>";
    unset($_SESSION['status']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['status'] = "Please fill in both email and password.";
        header("Location: applicantlogin.php");
        exit();
    }

    $query = "SELECT user_id, fullname, email, password, usertype_id FROM tbl_user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($user['usertype_id'] != 1) { // ✅ Only allow usertype_id = 1 (Applicants)
            $_SESSION['status'] = "Access denied. You are not an applicant.";
            header("Location: applicantlogin.php");
            exit();
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['usertype_id'] = $user['usertype_id'];

            header("Location: home.php");
            exit();
        } else {
            $_SESSION['status'] = "Invalid email or password.";
            header("Location: applicantlogin.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "No account found with that email.";
        header("Location: applicantlogin.php");
        exit();
    }
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
        body {
            font-family: 'Poppins', sans-serif;
        }

        .title {
            width: 362px;
            height: 117px;

            padding-left: 30px;
            font-weight: 600;
            font-size: 55px;
            line-height: 140%;
            letter-spacing: -0.015em;
            color: #0D0D26;
            margin: 100px auto 50px;
        }

        .backarrow {
            /* keyboard_backspace */

            position: absolute;
            width: 30px;
            height: 30px;
            left: 21px;
            top: 43px;

        }


        .toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            cursor: pointer;
            color: #7C7C7C;
        }

        button {
            /* Button */

            /* Auto layout */
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            padding: 16px 48px;
            gap: 10px;

            position: absolute;
            width: 345px;
            height: 62px;
            left: 24px;
            top: 455px;

            background: #1E4461;
            border-radius: 5px;
            color: white;

        }

        .or-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 170px 0;
        }

        .or-container hr {
            flex-grow: 1;
            border: none;
            border-top: 2px solid #ccc;
            margin: 10px;
        }

        .or-container span {
            padding: 0 10px;
            font-size: 12px;
            font-weight: bold;
            color: #7C7C7C;
        }

        .google {
            /* Group 55 */

            position: absolute;
            width: 48px;
            height: 48px;
            left: 159px;
            top: 600px;


        }

        .login {
            /* Have an account? Log in */

            position: absolute;
            width: 246px;
            height: 29px;
            left: 68px;
            top: 766px;

            font-family: 'SF Pro Display';
            font-style: normal;
            font-weight: 400;
            font-size: 17px;
            line-height: 29px;
            /* identical to box height */
            text-align: center;

            color: #737373;


        }

        .custom-input {
            height: 70px;
            /* Increase input height */
            font-size: 20px;
            /* Bigger text */
            padding: 50px;
            /* More space inside input */
        }

        .custom-label {
            font-size: 18px;
            /* Bigger floating label */
            display: flex;
            align-items: center;
            gap: 8px;
            /* Space between icon and text */
        }

        .custom-label ion-icon {
            font-size: 18px;
            /* Bigger icon */
        }

        .forgot {
            /* Forgot Password? */

            position: absolute;
            width: 150px;
            height: 24px;
            left: 114px;
            top: 525px;

            font-family: 'SF Pro Display';
            font-style: normal;
            font-weight: 400;
            font-size: 20px;
            line-height: 24px;
            /* identical to box height */
            text-align: center;

            /* Purple */
            color: #356899;


        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <a href="applicantregister.php"><ion-icon name="arrow-back-outline" class="backarrow"
                style="color:black;"></ion-icon></a>
        <div class="title" id="readButton">Log in</div>


        <form method="POST" action="">
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control custom-input" id="floatingInput"
                    placeholder="name@example.com">
                <label for="floatingInput" class="custom-label">
                    <ion-icon name="person-outline"></ion-icon> Email
                </label>
            </div>

            <div class="form-floating mb-3 position-relative">
                <input type="password" name="password" class="form-control custom-input" id="password"
                    placeholder="Password">
                <label for="password" class="custom-label">
                    <ion-icon name="key-outline"></ion-icon> Password
                </label>
                <ion-icon name="eye-off-outline" class="toggle-icon"
                    onclick="togglePassword('password', this)"></ion-icon>
            </div>

            <button type="submit" onclick="stopReading();">Login</button>
        </form>


        <div class="forgot">
            Forgot Password?
        </div>

        <div class="or-container">
            <hr>
            <span>Or continue with</span>
            <hr>
        </div>

        <div>
            <a href="#" class="google-btn" aria-label="Continue with Google" style="text-decoration: none">
                <ion-icon class="google" name="logo-google" style="color: black;"></ion-icon>
                <span style="color: white; ">Google</span>
            </a>
        </div>

        <div class="login">Doesn't have an account? <a href="applicantregister.php"
                style="text-decoration: none;">Register</a></div>

    </div>

    <?php
    include 'access.php';
    ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

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

        function togglePassword(inputId, icon) {
            var input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.setAttribute("name", "eye-outline"); // Show eye icon
            } else {
                input.type = "password";
                icon.setAttribute("name", "eye-off-outline"); // Hide eye icon
            }
        }
    </script>




</body>

</html>