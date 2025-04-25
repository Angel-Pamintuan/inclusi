<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next Page</title>
</head>

<body>

    <h1>Welcome to the Next Page</h1>

    <?php if ($accessibility == 'screen_reader'): ?>
        <script>
            const textToRead = document.body.innerText;
            const speech = new SpeechSynthesisUtterance(textToRead);
            speech.lang = "en-US";
            window.speechSynthesis.speak(speech);
        </script>
    <?php elseif ($accessibility == 'voice_control'): ?>
        <script>
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.start();
            recognition.onresult = (event) => {
                console.log("Voice Command:", event.results[0][0].transcript);
            };
        </script>
    <?php elseif ($accessibility == 'focus_mode'): ?>
        <style>
            body {
                background-color: #f4f4f4;
                font-size: 20px;
            }
        </style>
        <p>Focus mode is enabled. The page is adjusted for better readability.</p>
    <?php endif; ?>

</body>

</html>