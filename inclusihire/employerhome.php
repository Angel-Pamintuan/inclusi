<?php
session_start();
include 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: employerlogin.php"); // Redirect if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .header-container {
            /* Rectangle 10 */

            position: absolute;
            width: 394px;
            height: 303px;
            left: -1px;
            top: 0px;

            background: #1E4461;


        }

        .title {
            /* Headline */

            position: absolute;
            width: 254px;
            height: 21px;
            left: 21px;
            top: 61px;

            /* Medium/14 px */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 500;
            font-size: 14px;
            line-height: 150%;
            /* identical to box height, or 21px */
            display: flex;
            align-items: center;
            letter-spacing: -0.01em;

            /* Grey / 60 */
            color: #95969D;


        }

        .heading {
            /* Headline */

            position: absolute;
            width: 254px;
            height: 26px;
            left: 21px;
            top: 84px;

            /* Bold/22 px */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            font-size: 22px;
            line-height: 120%;
            /* or 26px */
            letter-spacing: -0.015em;

            color: #F0F0FC;


        }

        .body-container {
            /* Rectangle 11 */

            position: absolute;
            width: 395px;
            height: 183px;
            left: -2px;
            top: 152px;

            background: #FAFAFD;
            border-radius: 26px 26px 0px 0px;

        }

        .featured-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            overflow-y: auto;
            max-height: 83vh;
            /* Makes it scrollable */
            padding: 10px;
            padding-bottom: 70px;
            width: 100%;
            margin-top: 3px;
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

        .logo {
            position: absolute;
            width: 48px;
            height: 45px;
            left: 20px;
            top: 10px;
            background: url(image.png) no-repeat center/cover;
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

        .job-roles,
        .job-type {
            position: absolute;
            background: #EBEBEB;
            border-radius: 30px;
            padding: 5px 10px;
            font-size: 12px;
        }

        .job-roles {
            left: 20px;
            top: 75px;
        }

        .job-type {
            left: 90px;
            top: 75px;
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
            margin-bottom: 20px;
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
            /* Soft shadow on top */
        }

        footer a {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
            color: black;
        }

        footer div {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
        }

        footer button {
            border: none;
            background: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container">
            <div class="title">Welcome to InclusiHire!</div>
            <div class="heading">Discover Jobs</div>
        </div>
        <div class="body-container">
            <div class="featured-container">

                <div class="featured">
                    <div class="bookmark"><ion-icon name="bookmark-outline"></ion-icon></div>
                    <div class="logo"></div>
                    <div class="job-title">Service Crew</div>
                    <div class="company-name">McDonald's</div>
                    <div class="job-roles">Service</div>
                    <div class="job-type">Full-time</div>
                    <div class="short-description">A McDonald's service crew takes orders, prepares food, handles
                        payments,
                        and
                        keeps the restaurant clean while ensuring great customer service.</div>
                    <div class="salary">₱200,000/Month</div>
                    <div class="location">Mexico, Pampanga</div>
                </div>

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
            <button onclick="window.location.href='employerhome.php'">
                <ion-icon name="home-outline" style="color: #1E4461; font-size: 28px"></ion-icon>
            </button>
            <button onclick="window.location.href='employermessage.php'">
                <ion-icon name="mail-outline" style="font-size: 28px"></ion-icon>
            </button>
            <button onclick="window.location.href='employerupload.php'">
                <ion-icon name="add-outline" style="font-size: 28px"></ion-icon>
            </button>
            <button onclick="window.location.href='employerprofile.php'">
                <ion-icon name="grid-outline" style="font-size: 28px"></ion-icon>
            </button>
        </footer>




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
        </script>


</body>

</html>