<?php
if ($accessibility == 'screen_reader'): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function speakText(text, callback = null) {
                speechSynthesis.cancel();
                const speech = new SpeechSynthesisUtterance(text);
                speech.lang = "en-US";
                speech.rate = 1;

                if (callback) {
                    speech.onend = callback;
                }

                speechSynthesis.speak(speech);
            }

            function setupButtonActions(button, actionType, customText = null, targetPage = null) {
                let lastTap = 0;
                let singleTapTimeout;

                button.addEventListener("click", function () {
                    clearTimeout(singleTapTimeout);
                    singleTapTimeout = setTimeout(() => {
                        let textToRead = customText ? customText : button.innerText.trim();
                        if (textToRead) speakText(textToRead);
                    }, 300);
                });

                button.addEventListener("dblclick", function (event) {
                    event.preventDefault();
                    speechSynthesis.cancel();

                    if (actionType === "navigate" && targetPage) {
                        speakText("Loading, please wait...", () => {
                            window.location.href = targetPage;
                        });
                    } else if (actionType === "submit") {
                        speakText("Saving your preferences...", () => {
                            button.closest("form").submit();
                        });
                    }
                });

                button.addEventListener("touchend", function (event) {
                    let currentTime = new Date().getTime();
                    if (currentTime - lastTap < 300) {
                        event.preventDefault();
                        clearTimeout(singleTapTimeout);
                        speechSynthesis.cancel();

                        if (actionType === "navigate" && targetPage) {
                            speakText("Loading, please wait...", () => {
                                window.location.href = targetPage;
                            });
                        } else if (actionType === "submit") {
                            speakText("Saving your preferences...", () => {
                                button.closest("form").submit();
                            });
                        }
                    }
                    lastTap = currentTime;
                });
            }

            document.querySelectorAll(".speak").forEach(element => {
                element.addEventListener("click", function () {
                    speakText(element.innerText);
                });
            });

            const navButtons = [
                { id: "getStartedNav", text: "Accessibility", page: "accessibility.php" },
                { id: "getStartedNav1", text: "Profile Picture Button", page: "applicantprofile.php" },
                { id: "getStartedNav2", text: "More Jobs Button", page: "morejobs.php" },
                { id: "getStartedNav3", text: "Home Button", page: "home.php" },
                { id: "getStartedNav4", text: "Message Button", page: "message.php" },
                { id: "getStartedNav5", text: "Bookmark Button", page: "bookmarks.php" },
                { id: "getStartedNav6", text: "More Jobs Button", page: "morejobs.php" },
                { id: "getStartedNav7", text: "View details button", page: "job_post.php" },

            ];

            navButtons.forEach(nav => {
                const button = document.getElementById(nav.id);
                if (button) setupButtonActions(button, "navigate", nav.text, nav.page);
            });

            const formButtons = [
                { id: "getStartedForm", text: "Saving your accessibility settings" },
                { id: "getStartedForm1", text: "Submitting your request" },
                { id: "getStartedForm2", text: "Adding to bookmark" }
            ];

            formButtons.forEach(form => {
                const button = document.getElementById(form.id);
                if (button) {
                    button.addEventListener("click", function (event) {
                        event.preventDefault();
                        speakText(form.text, () => {
                            button.closest("form").submit();
                        });
                    });
                }
            });
        });
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