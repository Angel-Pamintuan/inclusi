<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InclusiHire</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .container-fluid {
            min-height: 100vh;
            /* Full height for any screen */
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(170.91deg, #6099D0 8.39%, #41668A 28.81%, #122333 95.28%);
            text-align: center;
            padding: 20px;
        }

        .title {
            font-size: 3rem;
            /* Adjusts based on screen size */
            font-family: 'SF Pro Display', sans-serif;
            font-weight: bold;
            color: white;
            margin-bottom: 200px;
        }

        .subtitle {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin-bottom: 15px;
        }

        .description {
            font-size: 1rem;
            font-weight: 300;
            color: white;
            max-width: 80%;
            margin-bottom: 30px;
        }

        .btn-custom {
            width: 60%;
            max-width: 250px;
            height: 50px;
            background-color: white;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #41668A;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        }

        .btn-custom:hover {
            background-color: #f0f0f0;
        }

        ion-icon {
            font-size: 24px;
            margin-left: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .title {
                font-size: 2.5rem;
            }

            .subtitle {
                font-size: 1.2rem;
            }

            .description {
                font-size: 0.9rem;
            }

            .btn-custom {
                width: 80%;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="title speak" id="title">InclusiHire</div>
        <div class="subtitle speak" id="subtitle">Welcome to InclusiHire!</div>
        <div class="description speak" id="description">
            Where talent meets opportunity. No matter your experience level, we connect you with jobs that fit your
            skills and goals.
        </div>
        <button type="submit" class="btn-custom" id="getStartedNav" aria-label="Getting started with InclusiHire">
            Get Started <ion-icon name="arrow-forward-outline"></ion-icon>
        </button>

    </div>

    <script src="speech.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>



</body>

</html>