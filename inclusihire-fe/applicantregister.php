<?php
session_start();

$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;


include 'database.php';

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'Applicant') {
    $_SESSION['status'] = "Access denied. Please select Applicant first.";
    header("Location: whoyouare.php");
    exit();
}

// Set `user_type_id` for Applicants (assuming Applicant is 1 in DB)
$usertype_id = 1;

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $_SESSION['status'] = "Passwords do not match!";
        header("Location: applicantregister.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email_query = "SELECT email FROM tbl_user WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['status'] = "Email already exists!";
        $stmt->close();
        header("Location: applicantregister.php");
        exit();
    }
    $stmt->close();

    // Insert into database
    $insert_query = "INSERT INTO tbl_user (fullname, email, phone, password, usertype_id) 
                     VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssi", $fullname, $email, $phone, $hashedPassword, $usertype_id);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        header("Location: impairment.php");
        exit();
    } else {
        $_SESSION['status'] = "Error occurred during registration.";
        header("Location: applicantregister.php");
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
            margin: 100px auto 0px;
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
            top: 585px;

            background: #1E4461;
            border-radius: 5px;
            color: white;

        }

        .or-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 90px 0;
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
            top: 685px;


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
            font-size: 24px;
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <a href="whoyouare.php"><ion-icon name="arrow-back-outline" class="backarrow"
                style="color:black;"></ion-icon></a>
        <div class="title" id="readButton">Register</div>


        <form method="POST" action="">
            <div class="form-floating mb-3">
                <input type="text" name="fullname" class="form-control custom-input" id="floatingInput"
                    placeholder="Fullname" required>
                <label for="floatingInput" class="custom-label">
                    <ion-icon name="person-outline"></ion-icon> Fullname
                </label>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control custom-input" id="floatingInput"
                    placeholder="name@example.com" required>
                <label for="floatingInput" class="custom-label">
                    <ion-icon name="person-outline"></ion-icon> Email
                </label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="phone" class="form-control custom-input" id="floatingInput"
                    placeholder="09350559301" required>
                <label for="floatingInput" class="custom-label">
                    <ion-icon name="call-outline"></ion-icon> Contact Number
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

            <div class="form-floating mb-3 position-relative">
                <input type="password" name="confirmPassword" class="form-control custom-input" id="confirmpassword"
                    placeholder="Confirm Password">
                <label for="confirmpassword" class="custom-label">
                    <ion-icon name="key-outline"></ion-icon> Confirm Password
                </label>
                <ion-icon name="eye-off-outline" class="toggle-icon"
                    onclick="togglePassword('confirmpassword', this)"></ion-icon>
            </div>

            <button type="submit" name="submit">Register</button>
        </form>


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

        <div class="login">Have an account? <a href="applicantlogin.php" style="text-decoration: none;"
                onclick="stopReading();">Log in</a></div>

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


        function sendOTP() {
            const email = document.getElementById('email').value;

            if (!email) {
                alert("Please enter your email to receive OTP.");
                return;
            }

            let otp_val = Math.floor(1000 + Math.random() * 9000); // Generate 4-digit OTP
            let emailBody = `<h2>Your OTP is ${otp_val}</h2>`;

            Email.send({
                SecureToken: "7426a6e9-c2be-43a6-a7da-41d49ba74134",
                To: email,
                From: "pamintuanshane4@gmail.com",
                Subject: "Verify Email",
                Body: emailBody,
            }).then((message) => {
                if (message === "OK") {
                    console.log("OTP sent successfully");
                    alert("OTP sent to your email: " + email);
                    fetch('store_otp.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ otp: otp_val }),
                    });
                } else {
                    console.error("Email send failed:", message);
                    alert("Failed to send OTP. Please try again.");
                }
            }).catch((error) => {
                console.error("Error while sending email:", error);
                alert("An error occurred. Please try again.");
            });

        }


    </script>




</body>

</html>