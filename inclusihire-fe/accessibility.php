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
            font-size: 32px;
            line-height: 140%;
            letter-spacing: -0.015em;
            color: #0D0D26;
            margin: 100px auto 100px;
        }

        .form-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Moves the radio button to the right */
            width: 327px;
            height: 52px;
            margin: 10px auto;
            background: #F2F2F2;
            box-shadow: 0px 2px 10px -2px rgba(13, 21, 38, 0.02);
            border-radius: 12px;
            padding: 10px 20px;
            /* Add padding for better spacing */
            cursor: pointer;
        }

        .form-check-label {
            font-size: 18px;
            color: #333;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
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
            width: 327px;
            height: 56px;
            left: 33px;
            top: 698px;

            background: #1E4461;
            border-radius: 5px;
            color: white;

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title speak">Do you need any of these accessibility features?</div>

        <form action="save_accessibility.php" method="POST">
            <div class="form-check">
                <label class="form-check-label speak" for="radio1">Screen Reader</label>
                <input class="form-check-input" type="radio" name="accessibility" value="screen_reader" id="radio1">
            </div>

            <div class="form-check">
                <label class="form-check-label speak" for="radio2">Voice Control</label>
                <input class="form-check-input" type="radio" name="accessibility" value="voice_control" id="radio2">
            </div>

            <div class="form-check">
                <label class="form-check-label speak" for="radio3">Focus Mode</label>
                <input class="form-check-input" type="radio" name="accessibility" value="focus_mode" id="radio3">
            </div>

            <button type="submit" class="btn-custom" id="getStartedForm">Save</button>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script src="speech.js"></script>



</body>

</html>