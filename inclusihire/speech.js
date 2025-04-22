// Function to read text aloud
function speakText(text, callback = null) {
    speechSynthesis.cancel(); // Stop any ongoing speech
    const speech = new SpeechSynthesisUtterance(text);
    speech.lang = "en-US";
    speech.rate = 1;

    // If there's a callback, execute it after speech ends
    if (callback) {
        speech.onend = callback;
    }

    speechSynthesis.speak(speech);
}

// Function to handle single tap (speech) and double tap (action)
function setupButtonActions(button, actionType) {
    let lastTap = 0;
    let singleTapTimeout;

    // Single tap reads text
    button.addEventListener("click", function (event) {
        clearTimeout(singleTapTimeout);
        singleTapTimeout = setTimeout(() => {
            speakText(button.innerText.trim());
        }, 300);
    });

    // Double-click for desktop
    button.addEventListener("dblclick", function (event) {
        event.preventDefault();
        speechSynthesis.cancel(); // Stop speech instantly

        if (actionType === "navigate") {
            speakText("Loading, please wait...", () => {
                window.location.href = "accessibility.php";
            });
        } else if (actionType === "submit") {
            speakText("Saving your preferences...", () => {
                button.closest("form").submit();
            });
        }
    });

    // Double-tap for mobile
    button.addEventListener("touchend", function (event) {
        let currentTime = new Date().getTime();
        let tapLength = currentTime - lastTap;

        if (tapLength < 300 && tapLength > 0) { // Quick double-tap detected
            event.preventDefault();
            clearTimeout(singleTapTimeout);
            speechSynthesis.cancel(); // Stop speech instantly

            if (actionType === "navigate") {
                speakText("Loading, please wait...", () => {
                    window.location.href = "accessibility.php";
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

// Wait for DOM to load before adding event listeners
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".speak").forEach(function (element) {
        element.addEventListener("click", function () {
            speakText(element.innerText);
        });
    });

    // First "Get Started" button (for navigation)
    const getStartedNav = document.getElementById("getStartedNav");
    if (getStartedNav) {
        setupButtonActions(getStartedNav, "navigate");
    }

    // Second "Save" button (inside form)
    const getStartedForm = document.getElementById("getStartedForm");
    if (getStartedForm) {
        setupButtonActions(getStartedForm, "submit");
    }
});

// Function to read text aloud
function speakText(text, callback = null) {
    speechSynthesis.cancel(); // Stop any ongoing speech
    const speech = new SpeechSynthesisUtterance(text);
    speech.lang = "en-US";
    speech.rate = 1;

    // If there's a callback, run it after speech ends
    if (callback) {
        speech.onend = callback;
    }

    speechSynthesis.speak(speech);
}

document.addEventListener("DOMContentLoaded", function () {
    // Handle the Save button inside the form
    const getStartedFormButton = document.getElementById("getStartedForm");

    if (getStartedFormButton) {
        getStartedFormButton.addEventListener("click", function (event) {
            event.preventDefault(); // Stop form submission

            speakText("Saving your accessibility settings", function () {
                document.querySelector("form").submit(); // Submit after speech ends
            });
        });
    }
});

