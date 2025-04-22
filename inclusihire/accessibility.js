
        function readScreen() {
            const text = document.body.innerText; // Get all page text
            const speech = new SpeechSynthesisUtterance(text);
            speech.lang = "en-US"; // Set language
            speech.rate = 1; // Adjust speed (1 = normal)
            speechSynthesis.speak(speech);
        }

        document.getElementById("readText").addEventListener("click", readScreen);

        