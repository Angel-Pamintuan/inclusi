<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

// Get the user type from the form (1 for applicant, 2 for employer)

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
            height: 137px;

            padding-left: 30px;
            font-weight: 600;
            font-size: 45px;
            line-height: 140%;
            letter-spacing: -0.015em;
            color: #0D0D26;
            margin: 100px auto 100px;
        }

        .backarrow {
            /* keyboard_backspace */

            position: absolute;
            width: 30px;
            height: 30px;
            left: 21px;
            top: 43px;

        }

        button {
            width: 327px;
            height: 91px;
            font-size: 32px;
            background: #F2F2F2;
            font-weight: medium;
            border-radius: 12px;
            color: #7C7C7C;
            margin: 10px auto;
            padding: 10px 20px;
            border: none;
            /* Removes border */
            outline: none;
            /* Removes focus outline */
            display: block;
            /* Ensures buttons are centered */
        }

        button:hover {
            background-color: #1E4461;
            color: white;
        }

        .or-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 20px 0;
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <a href="accessibility.php"><ion-icon name="arrow-back-outline" class="backarrow"
                style="color:black;"></ion-icon></a>

        <div class="title speak" id="readButton">Select who you are???</div>

        <form method="POST" action="usertype.php">
            <input type="hidden" name="usertype" value="1">
            <button type="submit" class="btn-custom" id="getStartedForm"
                aria-label="Applicant Button">Applicant</button>
        </form>

        <div class="or-container speak">
            <hr>
            <span class="speak">OR</span>
            <hr>
        </div>

        <!-- Employer Form -->
        <form method="POST" action="usertype.php">
            <input type="hidden" name="usertype" value="2">
            <button type="submit" class="btn-custom" id="getStartedForm1" aria-label="Employer Button">Employer</button>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <?php
    include 'access.php';
    ?>



</body>

</html>